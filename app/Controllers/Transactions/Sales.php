<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\MenuModel;
use App\Models\RecipeModel;
use App\Models\RawMaterialModel;
use App\Models\StockMovementModel;

class Sales extends BaseController
{
    protected SaleModel $saleModel;
    protected SaleItemModel $saleItemModel;
    protected MenuModel $menuModel;
    protected RecipeModel $recipeModel;
    protected RawMaterialModel $rawModel;
    protected StockMovementModel $movementModel;

    public function __construct()
    {
        $this->saleModel     = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->menuModel     = new MenuModel();
        $this->recipeModel   = new RecipeModel();
        $this->rawModel      = new RawMaterialModel();
        $this->movementModel = new StockMovementModel();
    }

    /**
     * List penjualan (riwayat).
     */
    public function index()
    {
        $sales = $this->saleModel
            ->orderBy('sale_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [
            'title'   => 'Penjualan',
            'subtitle'=> 'Riwayat transaksi penjualan',
            'sales'   => $sales,
        ];

        return view('transactions/sales_index', $data);
    }

    /**
     * Form input penjualan (POS minimalis).
     */
    public function create()
    {
        $menus = $this->menuModel
            ->orderBy('name', 'ASC')
            ->findAll();

        $today = date('Y-m-d');

        $data = [
            'title'    => 'Input Penjualan',
            'subtitle' => 'Pencatatan transaksi penjualan harian',
            'menus'    => $menus,
            'today'    => $today,
        ];

        return view('transactions/sales_form', $data);
    }

    /**
     * Simpan transaksi penjualan:
     * - sales (header)
     * - sale_items (detail)
     * - HPP snapshot per menu (hpp_snapshot)
     * - Stock OUT dari raw_materials + stock_movements
     */
    public function store()
    {
        $rules = [
            'sale_date' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $itemsInput = $this->request->getPost('items') ?? [];
        $items      = [];

        // Bersihkan baris kosong
        foreach ($itemsInput as $row) {
            $menuId = (int) ($row['menu_id'] ?? 0);
            $qty    = (float) ($row['qty'] ?? 0);
            $price  = (float) ($row['price'] ?? 0);

            if ($menuId > 0 && $qty > 0 && $price >= 0) {
                $subtotal = $qty * $price;

                $items[] = [
                    'menu_id'  => $menuId,
                    'qty'      => $qty,
                    'price'    => $price,
                    'subtotal' => $subtotal,
                ];
            }
        }

        if (empty($items)) {
            return redirect()->back()
                ->with('errors', ['items' => 'Minimal satu baris item penjualan harus diisi.'])
                ->withInput();
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Hitung total
        $total = array_sum(array_column($items, 'subtotal'));

        // Header penjualan
        $headerData = [
            'sale_date'     => $this->request->getPost('sale_date'),
            'invoice_no'    => $this->request->getPost('invoice_no') ?: null,
            'customer_name' => $this->request->getPost('customer_name') ?: null,
            'total_amount'  => $total,
            'notes'         => $this->request->getPost('notes') ?: null,
            'created_by'    => session('user_id') ?? null, // asumsi key session
        ];

        $saleId = $this->saleModel->insert($headerData, true);

        // Proses item + HPP + pengurangan stok
        foreach ($items as $item) {
            $menuId = $item['menu_id'];
            $qty    = $item['qty'];

            // Hitung HPP berdasarkan resep (jika ada)
            $hppData = $this->recipeModel->calculateHppForMenu($menuId);
            $hppPerPortion = 0;

            if ($hppData !== null) {
                $hppPerPortion = (float) ($hppData['hpp_per_yield'] ?? 0);
            }

            // Insert sale_items (snapshot HPP per porsi)
            $saleItemId = $this->saleItemModel->insert([
                'sale_id'      => $saleId,
                'menu_id'      => $menuId,
                'qty'          => $qty,
                'price'        => $item['price'],
                'subtotal'     => $item['subtotal'],
                'hpp_snapshot' => $hppPerPortion,
            ], true);

            // Kalau ada resep → lakukan pengurangan stok bahan baku
            if ($hppData !== null && ! empty($hppData['items'])) {
                $recipe      = $hppData['recipe'];
                $recipeItems = $hppData['items'];

                $yieldQty = (float) ($recipe['yield_qty'] ?? 1);
                if ($yieldQty <= 0) {
                    $yieldQty = 1; // guard terhadap pembagian 0
                }

                // Faktor skala: berapa batch resep yang "terpakai" untuk qty penjualan ini
                $factor = $qty / $yieldQty;

                foreach ($recipeItems as $ri) {
                    $rawId    = (int) ($ri['raw_material_id'] ?? 0);
                    $baseQty  = (float) ($ri['qty'] ?? 0);
                    $wastePct = (float) ($ri['waste_pct'] ?? 0);

                    if ($rawId <= 0 || $baseQty <= 0) {
                        continue;
                    }

                    // Qty efektif per batch (ingat waste%)
                    $effectivePerBatch = $baseQty * (1 + $wastePct / 100.0);

                    // Qty yang harus dikurangi dari stok bahan baku
                    $qtyToDeduct = $effectivePerBatch * $factor;

                    // Update stok bahan baku
                    $material = $this->rawModel->find($rawId);
                    if ($material) {
                        $currentStock = (float) ($material['current_stock'] ?? 0);
                        $newStock     = $currentStock - $qtyToDeduct;

                        $this->rawModel->update($rawId, [
                            'current_stock' => $newStock,
                        ]);
                    }

                    // Catat stock movement (OUT)
                    $this->movementModel->insert([
                        'raw_material_id' => $rawId,
                        'movement_type'   => 'OUT',
                        'qty'             => $qtyToDeduct,
                        'ref_type'        => 'sale',
                        'ref_id'          => $saleId,
                        'note'            => 'Penjualan menu ID ' . $menuId . ' (sale_item ' . $saleItemId . ')',
                    ]);
                }
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan penjualan.')
                ->withInput();
        }

        return redirect()->to(site_url('transactions/sales'))
            ->with('message', 'Transaksi penjualan berhasil disimpan.');
    }

    /**
     * Detail 1 transaksi penjualan.
     */
    public function detail(int $id)
    {
        $sale = $this->saleModel->find($id);
        if (! $sale) {
            return redirect()->to(site_url('transactions/sales'))
                ->with('error', 'Data penjualan tidak ditemukan.');
        }

        $items = $this->saleItemModel
            ->withMenu()
            ->where('sale_id', $id)
            ->findAll();

        $data = [
            'title'   => 'Detail Penjualan',
            'subtitle'=> 'Rincian transaksi',
            'sale'    => $sale,
            'items'   => $items,
        ];

        return view('transactions/sales_detail', $data);
    }
}
