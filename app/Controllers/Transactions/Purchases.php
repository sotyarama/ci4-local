<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\PurchaseModel;
use App\Models\PurchaseItemModel;
use App\Models\SupplierModel;
use App\Models\RawMaterialModel;

class Purchases extends BaseController
{
    protected PurchaseModel $purchaseModel;
    protected PurchaseItemModel $itemModel;
    protected SupplierModel $supplierModel;
    protected RawMaterialModel $rawModel;

    public function __construct()
    {
        $this->purchaseModel = new PurchaseModel();
        $this->itemModel     = new PurchaseItemModel();
        $this->supplierModel = new SupplierModel();
        $this->rawModel      = new RawMaterialModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('purchases p')
            ->select('p.*, s.name AS supplier_name')
            ->join('suppliers s', 's.id = p.supplier_id', 'left')
            ->orderBy('p.purchase_date', 'DESC')
            ->orderBy('p.id', 'DESC');

        $purchases = $builder->get()->getResultArray();

        $data = [
            'title'      => 'Pembelian Bahan Baku',
            'subtitle'   => 'Riwayat pembelian',
            'purchases'  => $purchases,
        ];

        return view('transactions/purchases_index', $data);
    }

    public function create()
    {
        $suppliers = $this->supplierModel->getActive();
        $materials = $this->rawModel
            ->withUnit()
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'     => 'Tambah Pembelian',
            'subtitle'  => 'Input pembelian bahan baku',
            'suppliers' => $suppliers,
            'materials' => $materials,
        ];

        return view('transactions/purchases_form', $data);
    }

    public function store()
    {
        $rules = [
            'supplier_id'   => 'required|integer',
            'purchase_date' => 'required|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $itemsInput = $this->request->getPost('items') ?? [];
        $items = [];

        // Bersihkan item kosong
        foreach ($itemsInput as $row) {
            $rawId = (int) ($row['raw_material_id'] ?? 0);
            $qty   = (float) ($row['qty'] ?? 0);
            $cost  = (float) ($row['unit_cost'] ?? 0);

            if ($rawId > 0 && $qty > 0 && $cost >= 0) {
                $items[] = [
                    'raw_material_id' => $rawId,
                    'qty'             => $qty,
                    'unit_cost'       => $cost,
                    'total_cost'      => $qty * $cost,
                ];
            }
        }

        if (empty($items)) {
            return redirect()->back()
                ->with('errors', ['items' => 'Minimal satu baris item pembelian diisi.'])
                ->withInput();
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Hitung total
        $total = array_sum(array_column($items, 'total_cost'));

        // Insert header
        $purchaseData = [
            'supplier_id'   => (int) $this->request->getPost('supplier_id'),
            'purchase_date' => $this->request->getPost('purchase_date'),
            'invoice_no'    => $this->request->getPost('invoice_no') ?: null,
            'total_amount'  => $total,
            'notes'         => $this->request->getPost('notes') ?: null,
        ];

        $purchaseId = $this->purchaseModel->insert($purchaseData, true);

        // Insert items + update stok / cost
        foreach ($items as $item) {
            $item['purchase_id'] = $purchaseId;
            $this->itemModel->insert($item);

            // Update stok & cost raw material
            $material = $this->rawModel->find($item['raw_material_id']);
            if ($material) {
                $prevStock = (float) $material['current_stock'];
                $prevAvg   = (float) $material['cost_avg'];

                $newQty   = $item['qty'];
                $unitCost = $item['unit_cost'];

                $newStock = $prevStock + $newQty;

                if ($prevStock <= 0) {
                    $newAvg = $unitCost;
                } else {
                    $totalValueBefore = $prevStock * $prevAvg;
                    $totalValueNew    = $newQty * $unitCost;
                    $newAvg           = ($totalValueBefore + $totalValueNew) / $newStock;
                }

                $this->rawModel->update($material['id'], [
                    'current_stock' => $newStock,
                    'cost_last'     => $unitCost,
                    'cost_avg'      => $newAvg,
                ]);
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pembelian.')
                ->withInput();
        }

        return redirect()->to(site_url('purchases'))
            ->with('message', 'Pembelian berhasil disimpan.');
    }

    public function detail(int $id)
    {
        $db = \Config\Database::connect();

        $header = $db->table('purchases p')
            ->select('p.*, s.name AS supplier_name, s.phone, s.address')
            ->join('suppliers s', 's.id = p.supplier_id', 'left')
            ->where('p.id', $id)
            ->get()->getRowArray();

        if (! $header) {
            return redirect()->to(site_url('purchases'))
                ->with('error', 'Data pembelian tidak ditemukan.');
        }

        $items = $db->table('purchase_items i')
            ->select('i.*, r.name AS material_name, u.short_name AS unit_short')
            ->join('raw_materials r', 'r.id = i.raw_material_id', 'left')
            ->join('units u', 'u.id = r.unit_id', 'left')
            ->where('i.purchase_id', $id)
            ->get()->getResultArray();

        $data = [
            'title'    => 'Detail Pembelian',
            'subtitle' => 'Rincian pembelian bahan baku',
            'header'   => $header,
            'items'    => $items,
        ];

        return view('transactions/purchases_detail', $data);
    }
}
