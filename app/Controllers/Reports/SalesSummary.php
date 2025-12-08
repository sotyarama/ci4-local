<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\SaleModel;

class SalesSummary extends BaseController
{
    protected SaleModel $saleModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
    }

    /**
     * Ringkasan penjualan per hari (omzet, HPP, margin).
     */
    public function daily()
    {
        $dateFrom = $this->request->getGet('date_from') ?: null;
        $dateTo   = $this->request->getGet('date_to') ?: null;

        $db = \Config\Database::connect();

        $builder = $db->table('sales')
            ->select('sale_date, SUM(total_amount) AS total_sales, SUM(total_cost) AS total_cost')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'DESC');

        if ($dateFrom) {
            $builder->where('sale_date >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('sale_date <=', $dateTo);
        }

        $rows = $builder->get()->getResultArray();

        $data = [
            'title'     => 'Laporan Penjualan Harian',
            'subtitle'  => 'Ringkasan omzet, HPP, dan margin per tanggal',
            'rows'      => $rows,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
        ];

        return view('reports/sales_daily', $data);
    }
}
