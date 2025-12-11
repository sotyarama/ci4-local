<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\PurchaseModel;
use App\Models\SupplierModel;

class PurchaseSummary extends BaseController
{
    protected PurchaseModel $purchaseModel;
    protected SupplierModel $supplierModel;

    public function __construct()
    {
        $this->purchaseModel = new PurchaseModel();
        $this->supplierModel = new SupplierModel();
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
}
