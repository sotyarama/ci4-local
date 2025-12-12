<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\PurchaseModel;
use App\Models\SupplierModel;
use App\Models\RawMaterialModel;

class PurchaseSummary extends BaseController
{
    protected PurchaseModel $purchaseModel;
    protected SupplierModel $supplierModel;
    protected RawMaterialModel $rawModel;

    public function __construct()
    {
        $this->purchaseModel = new PurchaseModel();
        $this->supplierModel = new SupplierModel();
        $this->rawModel      = new RawMaterialModel();
    }

    public function perSupplier()
    {
        $dateFrom   = $this->request->getGet('date_from') ?: null;
        $dateTo     = $this->request->getGet('date_to') ?: null;
        $supplierId = $this->request->getGet('supplier_id') ?: null;

        $builder = $this->purchaseModel
            ->select([
                'purchases.supplier_id',
                'COALESCE(suppliers.name, \'-\') AS supplier_name',
                'COUNT(*) AS purchase_count',
                'SUM(purchases.total_amount) AS total_amount',
            ])
            ->join('suppliers', 'suppliers.id = purchases.supplier_id', 'left');

        if ($dateFrom) {
            $builder->where('purchases.purchase_date >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('purchases.purchase_date <=', $dateTo);
        }
        if (! empty($supplierId)) {
            $builder->where('purchases.supplier_id', (int) $supplierId);
        }

        $rows = $builder
            ->groupBy('purchases.supplier_id, suppliers.name')
            ->orderBy('supplier_name', 'ASC')
            ->findAll();

        $grandTotal = array_sum(array_map(static function ($row) {
            return (float) ($row['total_amount'] ?? 0);
        }, $rows));

        $suppliers = $this->supplierModel
            ->select('id, name')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'      => 'Laporan Pembelian per Supplier',
            'subtitle'   => 'Ringkas total pembelian per pemasok',
            'rows'       => $rows,
            'grandTotal' => $grandTotal,
            'dateFrom'   => $dateFrom,
            'dateTo'     => $dateTo,
            'supplierId' => $supplierId,
            'suppliers'  => $suppliers,
        ];

        return view('reports/purchases_supplier', $data);
    }

    public function perMaterial()
    {
        $dateFrom   = $this->request->getGet('date_from') ?: null;
        $dateTo     = $this->request->getGet('date_to') ?: null;
        $supplierId = $this->request->getGet('supplier_id') ?: null;
        $materialId = $this->request->getGet('raw_material_id') ?: null;

        $db = \Config\Database::connect();
        $builder = $db->table('purchase_items pi')
            ->select([
                'pi.raw_material_id',
                'rm.name AS material_name',
                'p.supplier_id',
                's.name AS supplier_name',
                'COUNT(DISTINCT pi.purchase_id) AS purchase_count',
                'SUM(pi.qty) AS total_qty',
                'AVG(pi.unit_cost) AS avg_price',
                'MIN(pi.unit_cost) AS min_price',
                'MAX(pi.unit_cost) AS max_price',
                'SUM(pi.total_cost) AS total_cost',
            ])
            ->join('purchases p', 'p.id = pi.purchase_id', 'left')
            ->join('suppliers s', 's.id = p.supplier_id', 'left')
            ->join('raw_materials rm', 'rm.id = pi.raw_material_id', 'left');

        if ($dateFrom) {
            $builder->where('p.purchase_date >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('p.purchase_date <=', $dateTo);
        }
        if (! empty($supplierId)) {
            $builder->where('p.supplier_id', (int) $supplierId);
        }
        if (! empty($materialId)) {
            $builder->where('pi.raw_material_id', (int) $materialId);
        }

        $rows = $builder
            ->groupBy('pi.raw_material_id, p.supplier_id, rm.name, s.name')
            ->orderBy('rm.name', 'ASC')
            ->orderBy('s.name', 'ASC')
            ->get()->getResultArray();

        $grandTotal = array_sum(array_map(static function ($row) {
            return (float) ($row['total_cost'] ?? 0);
        }, $rows));

        $suppliers = $this->supplierModel
            ->select('id, name')
            ->orderBy('name', 'ASC')
            ->findAll();

        $materials = $this->rawModel
            ->select('id, name')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'       => 'Laporan Pembelian per Bahan',
            'subtitle'    => 'Bandingkan harga & total pembelian per bahan dan pemasok',
            'rows'        => $rows,
            'grandTotal'  => $grandTotal,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'supplierId'  => $supplierId,
            'materialId'  => $materialId,
            'suppliers'   => $suppliers,
            'materials'   => $materials,
        ];

        return view('reports/purchases_material', $data);
    }
}
