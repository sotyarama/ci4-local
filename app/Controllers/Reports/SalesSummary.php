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

        if ($this->request->getGet('export') === 'csv') {
            return $this->exportDailyCsv($rows, $dateFrom, $dateTo);
        }

        $data = [
            'title'     => 'Laporan Penjualan Harian',
            'subtitle'  => 'Ringkasan omzet, HPP, dan margin per tanggal',
            'rows'      => $rows,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
        ];

        return view('reports/sales_daily', $data);
    }

    /**
     * Ringkasan penjualan per menu (periode, qty, omzet, HPP total, margin).
     */
    public function perMenu()
    {
        $dateFrom = $this->request->getGet('date_from') ?: null;
        $dateTo   = $this->request->getGet('date_to') ?: null;

        $db = \Config\Database::connect();

        $builder = $db->table('sale_items si')
            ->select('si.menu_id, COALESCE(m.name, "Menu (hapus)") AS menu_name')
            ->select('SUM(si.qty) AS total_qty')
            ->select('SUM(si.subtotal) AS total_sales')
            ->select('SUM(si.hpp_snapshot * si.qty) AS total_cost')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->join('menus m', 'm.id = si.menu_id', 'left')
            ->groupBy('si.menu_id, m.name')
            ->orderBy('total_sales', 'DESC');

        if ($dateFrom) {
            $builder->where('s.sale_date >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('s.sale_date <=', $dateTo);
        }

        $rows = $builder->get()->getResultArray();

        if ($this->request->getGet('export') === 'csv') {
            return $this->exportPerMenuCsv($rows, $dateFrom, $dateTo);
        }

        $data = [
            'title'     => 'Penjualan per Menu',
            'subtitle'  => 'Qty, omzet, HPP, dan margin per menu untuk periode tertentu',
            'rows'      => $rows,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
        ];

        return view('reports/sales_menu', $data);
    }

    private function exportDailyCsv(array $rows, ?string $dateFrom, ?string $dateTo)
    {
        $fh = fopen('php://temp', 'r+');

        fputcsv($fh, ['Tanggal', 'Total Penjualan', 'Total HPP', 'Margin', 'Margin %']);

        foreach ($rows as $r) {
            $sales  = (float) ($r['total_sales'] ?? 0);
            $cost   = (float) ($r['total_cost'] ?? 0);
            $margin = $sales - $cost;
            $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;

            fputcsv($fh, [
                $r['sale_date'] ?? '',
                $sales,
                $cost,
                $margin,
                round($marginPct, 2),
            ]);
        }

        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        $period = $this->formatPeriodLabel($dateFrom, $dateTo);
        $filename = 'sales_daily_' . $period . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    private function exportPerMenuCsv(array $rows, ?string $dateFrom, ?string $dateTo)
    {
        $fh = fopen('php://temp', 'r+');

        fputcsv($fh, ['Menu', 'Qty', 'Omzet', 'HPP', 'Margin', 'Margin %']);

        foreach ($rows as $r) {
            $qty    = (float) ($r['total_qty'] ?? 0);
            $sales  = (float) ($r['total_sales'] ?? 0);
            $cost   = (float) ($r['total_cost'] ?? 0);
            $margin = $sales - $cost;
            $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;

            fputcsv($fh, [
                $r['menu_name'] ?? '',
                $qty,
                $sales,
                $cost,
                $margin,
                round($marginPct, 2),
            ]);
        }

        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        $period = $this->formatPeriodLabel($dateFrom, $dateTo);
        $filename = 'sales_per_menu_' . $period . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    private function formatPeriodLabel(?string $from, ?string $to): string
    {
        $exportDate = date('Ymd');

        if ($from && $to) {
            return $from . '_to_' . $to . '_' . $exportDate;
        }
        if ($from) {
            return 'from_' . $from . '_' . $exportDate;
        }
        if ($to) {
            return 'to_' . $to . '_' . $exportDate;
        }

        // Tanpa filter, tetap sertakan tanggal ekspor agar unik
        return 'all_' . $exportDate;
    }
}
