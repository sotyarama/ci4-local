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
        $perPage  = $this->sanitizePerPage($this->request->getGet('per_page'));
        $page     = $this->sanitizePage($this->request->getGet('page'));
        $wantCsv  = ($this->request->getGet('export') === 'csv');

        $db = \Config\Database::connect();

        $baseBuilder = $db->table('sales')
            ->select('sale_date, SUM(total_amount) AS total_sales, SUM(total_cost) AS total_cost')
            ->where('status !=', 'void')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'DESC');

        $this->applyDateFilter($baseBuilder, $dateFrom, $dateTo);

        if ($wantCsv) {
            $rows = (clone $baseBuilder)->get()->getResultArray();
            return $this->exportDailyCsv($rows, $dateFrom, $dateTo);
        }

        $totalRows = $this->countDailyRows($dateFrom, $dateTo);
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $rows = (clone $baseBuilder)
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $totals = $this->aggregateDailyTotals($dateFrom, $dateTo);


        $data = [
            'title'     => 'Laporan Penjualan Harian',
            'subtitle'  => 'Ringkasan omzet, HPP, dan margin per tanggal',
            'rows'      => $rows,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
            'perPage'   => $perPage,
            'page'      => $page,
            'totalRows' => $totalRows,
            'totalPages'=> $totalPages,
            'totalSalesAll' => (float) ($totals['total_sales'] ?? 0),
            'totalCostAll'  => (float) ($totals['total_cost'] ?? 0),
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
        $perPage  = $this->sanitizePerPage($this->request->getGet('per_page'));
        $page     = $this->sanitizePage($this->request->getGet('page'));
        $wantCsv  = ($this->request->getGet('export') === 'csv');

        $db = \Config\Database::connect();

        $baseBuilder = $db->table('sale_items si')
            ->select('si.menu_id, COALESCE(m.name, "Menu (hapus)") AS menu_name')
            ->select('SUM(si.qty) AS total_qty')
            ->select('SUM(si.subtotal) AS total_sales')
            ->select('SUM(si.hpp_snapshot * si.qty) AS total_cost')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->join('menus m', 'm.id = si.menu_id', 'left')
            ->where('s.status !=', 'void')
            ->groupBy('si.menu_id, m.name')
            ->orderBy('total_sales', 'DESC');

        $this->applyDateFilter($baseBuilder, $dateFrom, $dateTo, 's.sale_date');

        if ($wantCsv) {
            $rows = (clone $baseBuilder)->get()->getResultArray();
            return $this->exportPerMenuCsv($rows, $dateFrom, $dateTo);
        }

        $totalRows = $this->countMenuRows($dateFrom, $dateTo);
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $rows = (clone $baseBuilder)
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $totals = $this->aggregateMenuTotals($dateFrom, $dateTo);


        $data = [
            'title'     => 'Penjualan per Menu',
            'subtitle'  => 'Qty, omzet, HPP, dan margin per menu untuk periode tertentu',
            'rows'      => $rows,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
            'perPage'   => $perPage,
            'page'      => $page,
            'totalRows' => $totalRows,
            'totalPages'=> $totalPages,
            'totalQtyAll'   => (float) ($totals['total_qty'] ?? 0),
            'totalSalesAll' => (float) ($totals['total_sales'] ?? 0),
            'totalCostAll'  => (float) ($totals['total_cost'] ?? 0),
        ];

        return view('reports/sales_menu', $data);
    }

    /**
     * Ringkasan penjualan per kategori menu (periode, qty, omzet, HPP total, margin).
     */
    public function perCategory()
    {
        $dateFrom = $this->request->getGet('date_from') ?: null;
        $dateTo   = $this->request->getGet('date_to') ?: null;
        $perPage  = $this->sanitizePerPage($this->request->getGet('per_page'));
        $page     = $this->sanitizePage($this->request->getGet('page'));
        $wantCsv  = ($this->request->getGet('export') === 'csv');

        $db = \Config\Database::connect();

        $baseBuilder = $db->table('sale_items si')
            ->select('mc.id AS category_id')
            ->select('COALESCE(mc.name, "Kategori (hapus)") AS category_name')
            ->select('SUM(si.qty) AS total_qty')
            ->select('SUM(si.subtotal) AS total_sales')
            ->select('SUM(si.hpp_snapshot * si.qty) AS total_cost')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->join('menus m', 'm.id = si.menu_id', 'left')
            ->join('menu_categories mc', 'mc.id = m.menu_category_id', 'left')
            ->where('s.status !=', 'void')
            ->groupBy('mc.id, mc.name')
            ->orderBy('total_sales', 'DESC');

        $this->applyDateFilter($baseBuilder, $dateFrom, $dateTo, 's.sale_date');

        if ($wantCsv) {
            $rows = (clone $baseBuilder)->get()->getResultArray();
            return $this->exportPerCategoryCsv($rows, $dateFrom, $dateTo);
        }

        $totalRows  = $this->countCategoryRows($dateFrom, $dateTo);
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $rows = (clone $baseBuilder)
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $totals = $this->aggregateCategoryTotals($dateFrom, $dateTo);

        $data = [
            'title'     => 'Penjualan per Kategori Menu',
            'subtitle'  => 'Qty, omzet, HPP, dan margin per kategori menu',
            'rows'      => $rows,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
            'perPage'   => $perPage,
            'page'      => $page,
            'totalRows' => $totalRows,
            'totalPages'=> $totalPages,
            'totalQtyAll'   => (float) ($totals['total_qty'] ?? 0),
            'totalSalesAll' => (float) ($totals['total_sales'] ?? 0),
            'totalCostAll'  => (float) ($totals['total_cost'] ?? 0),
        ];

        return view('reports/sales_category', $data);
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

    private function exportPerCategoryCsv(array $rows, ?string $dateFrom, ?string $dateTo)
    {
        $fh = fopen('php://temp', 'r+');

        fputcsv($fh, ['Kategori', 'Qty', 'Omzet', 'HPP', 'Margin', 'Margin %']);

        foreach ($rows as $r) {
            $qty    = (float) ($r['total_qty'] ?? 0);
            $sales  = (float) ($r['total_sales'] ?? 0);
            $cost   = (float) ($r['total_cost'] ?? 0);
            $margin = $sales - $cost;
            $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;

            fputcsv($fh, [
                $r['category_name'] ?? '',
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
        $filename = 'sales_per_category_' . $period . '.csv';

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

    private function sanitizePerPage($input): int
    {
        $allowed = [20, 50, 100, 200];
        $default = 50;
        $val = (int) ($input ?? 0);
        if (in_array($val, $allowed, true)) {
            return $val;
        }
        return $default;
    }

    private function sanitizePage($input): int
    {
        $page = (int) ($input ?? 1);
        return $page > 0 ? $page : 1;
    }

    private function applyDateFilter($builder, ?string $dateFrom, ?string $dateTo, string $field = 'sale_date')
    {
        if ($dateFrom) {
            $builder->where($field . ' >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where($field . ' <=', $dateTo);
        }
    }

    private function countDailyRows(?string $dateFrom, ?string $dateTo): int
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sales')
            ->select('COUNT(DISTINCT sale_date) AS cnt')
            ->where('status !=', 'void');

        $this->applyDateFilter($builder, $dateFrom, $dateTo);

        $row = $builder->get()->getRowArray();
        return (int) ($row['cnt'] ?? 0);
    }

    private function aggregateDailyTotals(?string $dateFrom, ?string $dateTo): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sales')
            ->select('SUM(total_amount) AS total_sales, SUM(total_cost) AS total_cost')
            ->where('status !=', 'void');

        $this->applyDateFilter($builder, $dateFrom, $dateTo);

        return $builder->get()->getRowArray() ?? ['total_sales' => 0, 'total_cost' => 0];
    }

    private function countMenuRows(?string $dateFrom, ?string $dateTo): int
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sale_items si')
            ->select('COUNT(DISTINCT si.menu_id) AS cnt')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->where('s.status !=', 'void');

        $this->applyDateFilter($builder, $dateFrom, $dateTo, 's.sale_date');

        $row = $builder->get()->getRowArray();
        return (int) ($row['cnt'] ?? 0);
    }

    private function aggregateMenuTotals(?string $dateFrom, ?string $dateTo): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sale_items si')
            ->select('SUM(si.qty) AS total_qty, SUM(si.subtotal) AS total_sales, SUM(si.hpp_snapshot * si.qty) AS total_cost')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->where('s.status !=', 'void');

        $this->applyDateFilter($builder, $dateFrom, $dateTo, 's.sale_date');

        return $builder->get()->getRowArray() ?? ['total_qty' => 0, 'total_sales' => 0, 'total_cost' => 0];
    }

    private function countCategoryRows(?string $dateFrom, ?string $dateTo): int
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sale_items si')
            ->select('COUNT(DISTINCT m.menu_category_id) AS cnt')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->join('menus m', 'm.id = si.menu_id', 'left')
            ->where('s.status !=', 'void');

        $this->applyDateFilter($builder, $dateFrom, $dateTo, 's.sale_date');

        $row = $builder->get()->getRowArray();
        return (int) ($row['cnt'] ?? 0);
    }

    private function aggregateCategoryTotals(?string $dateFrom, ?string $dateTo): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sale_items si')
            ->select('SUM(si.qty) AS total_qty, SUM(si.subtotal) AS total_sales, SUM(si.hpp_snapshot * si.qty) AS total_cost')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->join('menus m', 'm.id = si.menu_id', 'left')
            ->where('s.status !=', 'void');

        $this->applyDateFilter($builder, $dateFrom, $dateTo, 's.sale_date');

        return $builder->get()->getRowArray() ?? ['total_qty' => 0, 'total_sales' => 0, 'total_cost' => 0];
    }
}
