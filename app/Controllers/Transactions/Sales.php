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
        $today = date('Y-m-d');

        $todayAgg = $this->saleModel
            ->selectSum('total_amount', 'total_sales')
            ->selectSum('total_cost', 'total_cost')
            ->where('sale_date', $today)
            ->first();

        $totalSalesToday = (float) ($todayAgg['total_sales'] ?? 0);
        $totalCostToday  = (float) ($todayAgg['total_cost'] ?? 0);
        $marginToday     = $totalSalesToday - $totalCostToday;
        $marginPctToday  = $totalSalesToday > 0 ? ($marginToday / $totalSalesToday * 100) : 0;

        $sales = $this->saleModel
            ->orderBy('sale_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [
            'title'           => 'Penjualan',
            'subtitle'        => 'Riwayat transaksi penjualan',
            'sales'           => $sales,
            'todaySales'      => $totalSalesToday,
            'todayCost'       => $totalCostToday,
            'todayMargin'     => $marginToday,
            'todayMarginPct'  => $marginPctToday,
            'todayDate'       => $today,
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

                $menuData = $this->menuModel->find($menuId);
                $menuName = $menuData['name'] ?? 'Menu';
                $basePrice= (float) ($menuData['price'] ?? 0);

                $items[] = [
                    'menu_id'   => $menuId,
                    'menu_name' => $menuName,
                    'qty'       => $qty,
                    'price'     => $price,
                    'subtotal'  => $subtotal,
                    'base_price'=> $basePrice,
                ];
            }
        }

        if (empty($items)) {
            return redirect()->back()
                ->with('errors', ['items' => 'Minimal satu baris item penjualan harus diisi.'])
                ->withInput();
        }

        // Validasi override harga jual per item
        $priceErrors = [];
        foreach ($items as $row) {
            if ($row['price'] <= 0) {
                $priceErrors[] = "Harga jual untuk <b>{$row['menu_name']}</b> harus lebih dari 0.";
                continue;
            }

            $basePrice = (float) ($row['base_price'] ?? 0);
            if ($basePrice > 0 && $row['price'] < $basePrice) {
                $priceErrors[] = "Harga jual untuk <b>{$row['menu_name']}</b> lebih rendah dari harga master (Rp " . number_format($basePrice, 0, ',', '.') . "). Ubah harga di master menu atau konfirmasi harga.";
            }
        }

        if (! empty($priceErrors)) {
            return redirect()->back()
                ->with('errors', $priceErrors)
                ->withInput();
        }

        // ============================================================
        // STEP 1: VALIDASI RESEP & HITUNG KEBUTUHAN BAHAN BAKU
        // ============================================================

        $errors   = [];
        $rawNeeds = []; // total kebutuhan bahan baku dari seluruh menu

        foreach ($items as $row) {

            $menuId  = $row['menu_id'];
            $qtySale = $row['qty'];

            // 1) Cek resep
            $recipe = $this->recipeModel->where('menu_id', $menuId)->first();
            if (! $recipe) {
                $errors[] = "Menu <b>{$row['menu_name']}</b> belum memiliki resep. Transaksi dibatalkan.";
                continue;
            }

            $recipeItems = $this->recipeModel->getRecipeItems((int) $recipe['id']);

            if (empty($recipeItems)) {
                $errors[] = "Menu <b>{$row['menu_name']}</b> belum memiliki detail bahan. Transaksi dibatalkan.";
                continue;
            }

            // 2) Hitung kebutuhan bahan per menu
            $yieldQty = (float) ($recipe['yield_qty'] ?? 1);
            if ($yieldQty <= 0) {
                $yieldQty = 1;
            }

            $factor = $qtySale / $yieldQty;

            foreach ($recipeItems as $ri) {
                $rawId    = (int) $ri['raw_material_id'];
                $baseQty  = (float) $ri['qty'];
                $wastePct = (float) $ri['waste_pct'];

                if ($rawId <= 0 || $baseQty <= 0) {
                    continue;
                }

                $effectivePerBatch = $baseQty * (1 + $wastePct / 100.0);
                $needQty           = $effectivePerBatch * $factor;

                if (! isset($rawNeeds[$rawId])) {
                    $rawNeeds[$rawId] = 0;
                }

                $rawNeeds[$rawId] += $needQty;
            }
        }

        if (! empty($errors)) {
            return redirect()->back()
                ->with('errors', $errors)
                ->withInput();
        }

        // ============================================================
        // STEP 2: VALIDASI STOK BAHAN BAKU
        // ============================================================

        $shortages = [];

        foreach ($rawNeeds as $rawId => $neededQty) {
            $rm = $this->rawModel->find($rawId);
            if (! $rm) {
                continue;
            }

            if ($rm['current_stock'] < $neededQty) {
                $shortages[] = [
                    'name'   => $rm['name'],
                    'needed' => round($neededQty, 3),
                    'stock'  => round($rm['current_stock'], 3),
                ];
            }
        }

        if (! empty($shortages)) {

            $errors = [];

            foreach ($shortages as $s) {
                $errors[] = "Stok tidak mencukupi untuk <b>{$s['name']}</b>: butuh {$s['needed']}, stok hanya {$s['stock']}";
            }

            return redirect()->back()
                ->with('errors', $errors)
                ->withInput();
        }

        // ============================================================
        // STEP 3: SIMPAN TRANSAKSI + HITUNG TOTAL COST
        // ============================================================

        $db = \Config\Database::connect();
        $db->transStart();

        $totalAmount = array_sum(array_column($items, 'subtotal'));
        $totalCost   = 0.0; // ← akumulasi HPP semua item

        // Header penjualan
        $headerData = [
            'sale_date'     => $this->request->getPost('sale_date'),
            'invoice_no'    => $this->request->getPost('invoice_no') ?: null,
            'customer_name' => $this->request->getPost('customer_name') ?: null,
            'total_amount'  => $totalAmount,
            'total_cost'    => 0, // akan di-update setelah hitung HPP
            'notes'         => $this->request->getPost('notes') ?: null,
            // created_by sementara tidak dipakai (kolomnya sudah di-drop)
        ];


        $saleId = $this->saleModel->insert($headerData, true);

        // DEBUG jika insert gagal
        // if (! $saleId) {
        //     dd(
        //         'HEADER FAIL',
        //         $headerData,
        //         $this->saleModel->errors(),    // error dari model (kalau ada rule)
        //         \Config\Database::connect()->error()  // error DB (kalau ada)
        //     );
        // }


        // ============================================================
        // STEP 4: PROSES ITEM (HPP, STOCK OUT, MOVEMENT)
        // ============================================================

        foreach ($items as $item) {
            $menuId = $item['menu_id'];
            $qty    = $item['qty'];

            // HPP per porsi
            $hppData       = $this->recipeModel->calculateHppForMenu($menuId);
            $hppPerPortion = (float) ($hppData['hpp_per_yield'] ?? 0);

            // Akumulasi total HPP transaksi
            $lineCost  = $hppPerPortion * $qty;
            $totalCost += $lineCost;

            // Simpan sale_items
            $saleItemId = $this->saleItemModel->insert([
                'sale_id'      => $saleId,
                'menu_id'      => $menuId,
                'qty'          => $qty,
                'price'        => $item['price'],
                'subtotal'     => $item['subtotal'],
                'hpp_snapshot' => $hppPerPortion,
            ], true);

            // Kurangi stok bahan baku
            if ($hppData && ! empty($hppData['items'])) {
                $recipe      = $hppData['recipe'];
                $recipeItems = $hppData['items'];

                $yieldQty = (float) ($recipe['yield_qty'] ?? 1);
                if ($yieldQty <= 0) {
                    $yieldQty = 1;
                }

                $factor = $qty / $yieldQty;

                foreach ($recipeItems as $ri) {
                    $rawId    = (int) ($ri['raw_material_id'] ?? 0);
                    $baseQty  = (float) ($ri['qty'] ?? 0);
                    $wastePct = (float) ($ri['waste_pct'] ?? 0);

                    if ($rawId <= 0 || $baseQty <= 0) {
                        continue;
                    }

                    $effectivePerBatch = $baseQty * (1 + $wastePct / 100.0);
                    $qtyToDeduct       = $effectivePerBatch * $factor;

                    // Update stok
                    $material = $this->rawModel->find($rawId);
                    if ($material) {
                        // Optimistic guard: stok harus masih cukup pada saat eksekusi
                        if ($material['current_stock'] < $qtyToDeduct) {
                            $db->transRollback();

                            return redirect()->back()
                                ->with('errors', [
                                    "Stok berubah saat penyimpanan. Bahan <b>{$material['name']}</b> butuh " .
                                    number_format($qtyToDeduct, 3, ',', '.') . ' ' .
                                    'namun stok tersisa ' . number_format($material['current_stock'], 3, ',', '.'),
                                ])
                                ->withInput();
                        }

                        $newStock = $material['current_stock'] - $qtyToDeduct;
                        $this->rawModel->update($rawId, ['current_stock' => $newStock]);
                    }

                    // Stock movement
                    $this->movementModel->insert([
                        'raw_material_id' => $rawId,
                        'movement_type'   => 'OUT',
                        'qty'             => $qtyToDeduct,
                        'ref_type'        => 'sale',
                        'ref_id'          => $saleId,
                        'note'            => 'Penjualan menu ID ' . $menuId . ' (sale_item ' . $saleItemId . ')',
                        'created_at'      => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // UPDATE total_cost di header sales
        $this->saleModel->update($saleId, [
            'total_cost' => $totalCost,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            $err = $db->error();   // ambil error mysql / mariadb

            log_message('error', 'Gagal simpan transaksi penjualan: {error}', [
                'error' => $err['message'] ?? 'unknown DB error',
            ]);

            return redirect()->back()
                ->with('errors', [
                    'Terjadi kesalahan saat menyimpan transaksi.',
                    'Detail: ' . ($err['message'] ?? 'unknown DB error'),
                ])
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
