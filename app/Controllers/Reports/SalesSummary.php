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

    /**
     * Ringkasan penjualan by time (harian/mingguan/bulanan/tahunan).
     */
    public function byTime()
    {
        $startParam = $this->request->getGet('start') ?? $this->request->getGet('date_from');
        $endParam   = $this->request->getGet('end') ?? $this->request->getGet('date_to');
        $allDay     = $this->request->getGet('allday');
        $allDay     = ($allDay === '0') ? false : true;
        $startTime  = $this->sanitizeTime($this->request->getGet('start_time'), '00:00');
        $endTime    = $this->sanitizeTime($this->request->getGet('end_time'), '23:59');

        // Default: this month
        $today      = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
        $defaultStart = (clone $today)->modify('first day of this month')->format('Y-m-d');
        $defaultEnd   = $today->format('Y-m-d');

        $startDate = $startParam ?: $defaultStart;
        $endDate   = $endParam   ?: $defaultEnd;

        // Validate range
        $startObj = \DateTime::createFromFormat('Y-m-d', $startDate, new \DateTimeZone('Asia/Jakarta')) ?: new \DateTime($defaultStart, new \DateTimeZone('Asia/Jakarta'));
        $endObj   = \DateTime::createFromFormat('Y-m-d', $endDate, new \DateTimeZone('Asia/Jakarta'))   ?: new \DateTime($defaultEnd, new \DateTimeZone('Asia/Jakarta'));

        if ($startObj > $endObj) {
            $tmp = $startObj;
            $startObj = $endObj;
            $endObj = $tmp;
        }

        // Clamp max range to 366 days for safety
        $maxDays = 366;
        $diffDays = (int) $startObj->diff($endObj)->format('%a');
        if ($diffDays + 1 > $maxDays) {
            $endObj = (clone $startObj)->modify('+' . ($maxDays - 1) . ' days');
        }

        if ($allDay) {
            $fromDateTime = $startObj->format('Y-m-d') . ' 00:00:00';
            $toDateTime   = $endObj->format('Y-m-d') . ' 23:59:59';
        } else {
            $fromDateTime = $startObj->format('Y-m-d') . ' ' . $startTime . ':00';
            $toDateTime   = $endObj->format('Y-m-d') . ' ' . $endTime . ':59';
        }

        $dateFrom = $startObj->format('Y-m-d');
        $dateTo   = $endObj->format('Y-m-d');
        $perPage  = $this->sanitizePerPage($this->request->getGet('per_page'));
        $page     = $this->sanitizePage($this->request->getGet('page'));
        $wantCsv  = ($this->request->getGet('export') === 'csv');

        $group    = strtolower((string) ($this->request->getGet('group') ?? 'day'));
        $allowedGroups = ['day', 'week', 'month', 'year'];
        if (! in_array($group, $allowedGroups, true)) {
            $group = 'day';
        }

        [$periodExpr, $periodKeyExpr] = $this->resolvePeriodExpressions($group);

        $db = \Config\Database::connect();

        $baseBuilder = $db->table('sales')
            ->select("{$periodExpr} AS period", false)
            ->select("{$periodKeyExpr} AS period_key", false)
            ->select('SUM(total_amount) AS total_sales')
            ->select('SUM(total_cost) AS total_cost')
            ->where('status !=', 'void')
            ->groupBy('period_key')
            ->orderBy('period_key', 'DESC');

        $this->applyDateTimeFilter($baseBuilder, $fromDateTime, $toDateTime);

        if ($wantCsv) {
            $rows = (clone $baseBuilder)->get()->getResultArray();
            return $this->exportTimeCsv($rows, $dateFrom, $dateTo, $group, $allDay, $startTime, $endTime);
        }

        $totalRows  = $this->countTimeRows($fromDateTime, $toDateTime, $periodKeyExpr);
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $rows = (clone $baseBuilder)
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $totals = $this->aggregateTimeTotals($fromDateTime, $toDateTime);

        $data = [
            'title'     => 'Laporan Penjualan by Time',
            'subtitle'  => 'Ringkasan omzet, HPP, margin per periode (harian/mingguan/bulanan/tahunan)',
            'rows'      => $rows,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
            'startDate' => $dateFrom,
            'endDate'   => $dateTo,
            'allDay'    => $allDay,
            'startTime' => $startTime,
            'endTime'   => $endTime,
            'rangeDays' => (int) ($startObj->diff($endObj)->format('%a')) + 1,
            'perPage'   => $perPage,
            'page'      => $page,
            'totalRows' => $totalRows,
            'totalPages'=> $totalPages,
            'totalSalesAll' => (float) ($totals['total_sales'] ?? 0),
            'totalCostAll'  => (float) ($totals['total_cost'] ?? 0),
            'group'     => $group,
            'groupOptions' => $allowedGroups,
        ];

        return view('reports/sales_time', $data);
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

    private function exportTimeCsv(array $rows, ?string $dateFrom, ?string $dateTo, string $group, bool $allDay, string $startTime, string $endTime)
    {
        $fh = fopen('php://temp', 'r+');

        fputcsv($fh, ['Periode', 'Total Penjualan', 'Total HPP', 'Margin', 'Margin %', 'Group', 'All Day', 'Start Time', 'End Time']);

        foreach ($rows as $r) {
            $sales  = (float) ($r['total_sales'] ?? 0);
            $cost   = (float) ($r['total_cost'] ?? 0);
            $margin = $sales - $cost;
            $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;

            fputcsv($fh, [
                $r['period'] ?? '',
                $sales,
                $cost,
                $margin,
                round($marginPct, 2),
                $group,
                $allDay ? '1' : '0',
                $startTime,
                $endTime,
            ]);
        }

        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        $period = $this->formatPeriodLabel($dateFrom, $dateTo);
        $filename = 'sales_by_time_' . $group . '_' . $period . '.csv';

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

    private function sanitizeTime($input, string $default): string
    {
        if (! is_string($input) || $input === '') {
            return $default;
        }

        $parts = explode(':', $input);
        $h = isset($parts[0]) ? (int) $parts[0] : 0;
        $m = isset($parts[1]) ? (int) $parts[1] : 0;

        $h = max(0, min(23, $h));
        $m = max(0, min(59, $m));

        return str_pad((string) $h, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $m, 2, '0', STR_PAD_LEFT);
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

    private function applyDateTimeFilter($builder, ?string $fromDateTime, ?string $toDateTime, string $field = 'created_at')
    {
        if ($fromDateTime) {
            $builder->where($field . ' >=', $fromDateTime);
        }

        if ($toDateTime) {
            $builder->where($field . ' <=', $toDateTime);
        }
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

    private function resolvePeriodExpressions(string $group): array
    {
        switch ($group) {
            case 'week':
                return [
                    'CONCAT(YEAR(created_at), "-W", LPAD(WEEK(created_at, 1), 2, "0"))',
                    'YEARWEEK(created_at, 1)'
                ];
            case 'month':
                return ['DATE_FORMAT(created_at, "%Y-%m")', 'DATE_FORMAT(created_at, "%Y-%m")'];
            case 'year':
                return ['DATE_FORMAT(created_at, "%Y")', 'DATE_FORMAT(created_at, "%Y")'];
            case 'day':
            default:
                return ['DATE(created_at)', 'DATE(created_at)'];
        }
    }

    private function countTimeRows(?string $fromDateTime, ?string $toDateTime, string $periodKeyExpr): int
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sales')
            ->select("COUNT(DISTINCT {$periodKeyExpr}) AS cnt", false)
            ->where('status !=', 'void');

        $this->applyDateTimeFilter($builder, $fromDateTime, $toDateTime);

        $row = $builder->get()->getRowArray();
        return (int) ($row['cnt'] ?? 0);
    }


    private function aggregateTimeTotals(?string $fromDateTime, ?string $toDateTime): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sales')
            ->select('SUM(total_amount) AS total_sales, SUM(total_cost) AS total_cost')
            ->where('status !=', 'void');

        $this->applyDateTimeFilter($builder, $fromDateTime, $toDateTime);

        return $builder->get()->getRowArray() ?? ['total_sales' => 0, 'total_cost' => 0];
    }
}
