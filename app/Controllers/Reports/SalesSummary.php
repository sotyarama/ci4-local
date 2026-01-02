<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use Dompdf\Dompdf;
use Dompdf\Options;

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
        $export   = $this->request->getGet('export');
        $wantCsv  = ($export === 'csv');
        $wantPdf  = ($export === 'pdf');

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
        if ($wantPdf) {
            $rows = (clone $baseBuilder)->get()->getResultArray();
            $totals = $this->aggregateMenuTotals($dateFrom, $dateTo);
            return $this->exportPerMenuPdf($rows, $dateFrom, $dateTo, $totals);
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
            'totalPages' => $totalPages,
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
        $export   = $this->request->getGet('export');
        $wantCsv  = ($export === 'csv');
        $wantPdf  = ($export === 'pdf');

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
        if ($wantPdf) {
            $rows = (clone $baseBuilder)->get()->getResultArray();
            $totals = $this->aggregateCategoryTotals($dateFrom, $dateTo);
            return $this->exportPerCategoryPdf($rows, $dateFrom, $dateTo, $totals);
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
            'totalPages' => $totalPages,
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
        // Use date-range helper to parse and normalize request params
        helper('tr_daterange');
        $range = tr_parse_range_datetime($this->request, ['timezone' => 'Asia/Jakarta', 'maxDays' => 366]);

        $startDate = $range['startDate'];
        $endDate   = $range['endDate'];
        $allDay    = $range['allDay'];
        $startTime = $range['startTime'];
        $endTime   = $range['endTime'];
        $fromDateTime = $range['fromDateTime'];
        $toDateTime   = $range['toDateTime'];
        $rangeDays = $range['rangeDays'];

        $dateFrom = $startDate;
        $dateTo   = $endDate;
        $perPage  = $this->sanitizePerPage($this->request->getGet('per_page'));
        $page     = $this->sanitizePage($this->request->getGet('page'));
        $export   = $this->request->getGet('export');
        $wantCsv  = ($export === 'csv');
        $wantPdf  = ($export === 'pdf');

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
        if ($wantPdf) {
            $rows = (clone $baseBuilder)->get()->getResultArray();
            $totals = $this->aggregateTimeTotals($fromDateTime, $toDateTime);
            return $this->exportTimePdf($rows, $dateFrom, $dateTo, $group, $allDay, $startTime, $endTime, $totals);
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
            'rangeDays' => (int) $rangeDays,
            'perPage'   => $perPage,
            'page'      => $page,
            'totalRows' => $totalRows,
            'totalPages' => $totalPages,
            'totalSalesAll' => (float) ($totals['total_sales'] ?? 0),
            'totalCostAll'  => (float) ($totals['total_cost'] ?? 0),
            'group'     => $group,
            'groupOptions' => $allowedGroups,
        ];

        return view('reports/sales_time', $data);
    }

    /**
     * Ringkasan penjualan per customer (order, omzet, HPP, margin).
     */
    public function perCustomer()
    {
        $dateFrom = $this->request->getGet('date_from') ?: null;
        $dateTo   = $this->request->getGet('date_to') ?: null;
        $perPage  = $this->sanitizePerPage($this->request->getGet('per_page'));
        $page     = $this->sanitizePage($this->request->getGet('page'));
        $export   = $this->request->getGet('export');
        $wantCsv  = ($export === 'csv');
        $wantPdf  = ($export === 'pdf');
        $mode     = strtolower((string) ($this->request->getGet('mode') ?? 'full'));
        if (! in_array($mode, ['full', 'compact'], true)) {
            $mode = 'full';
        }

        $db = \Config\Database::connect();

        $itemsSub = $db->table('sale_items')
            ->select('sale_id, SUM(qty) AS total_items')
            ->groupBy('sale_id');
        $itemsSql = $itemsSub->getCompiledSelect();

        $baseBuilder = $db->table('sales s')
            ->select('s.customer_id')
            ->select('COALESCE(c.name, s.customer_name, "Tamu") AS customer_name', false)
            ->select('c.phone AS customer_phone')
            ->select('c.email AS customer_email')
            ->select('COUNT(s.id) AS total_orders')
            ->select('SUM(COALESCE(si.total_items, 0)) AS total_items', false)
            ->select('SUM(s.total_amount) AS total_sales')
            ->select('SUM(s.total_cost) AS total_cost')
            ->select('COUNT(DISTINCT s.sale_date) AS active_days')
            ->select('MAX(s.sale_date) AS last_order_date')
            ->join('customers c', 'c.id = s.customer_id', 'left')
            ->join('(' . $itemsSql . ') si', 'si.sale_id = s.id', 'left', false)
            ->where('s.status !=', 'void')
            ->groupBy('s.customer_id, customer_name, c.phone, c.email')
            ->orderBy('total_sales', 'DESC');

        $this->applyDateFilter($baseBuilder, $dateFrom, $dateTo, 's.sale_date');

        if ($wantCsv) {
            $rows = (clone $baseBuilder)->get()->getResultArray();
            return $this->exportPerCustomerCsv($rows, $dateFrom, $dateTo);
        }
        if ($wantPdf) {
            $rows = (clone $baseBuilder)->get()->getResultArray();
            $totals = $this->aggregateCustomerTotals($dateFrom, $dateTo);
            return $this->exportPerCustomerPdf($rows, $dateFrom, $dateTo, $mode, $totals);
        }

        $totalRows  = $this->countCustomerRows($dateFrom, $dateTo);
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $rows = (clone $baseBuilder)
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $totals = $this->aggregateCustomerTotals($dateFrom, $dateTo);

        $data = [
            'title'     => 'Penjualan per Customer',
            'subtitle'  => 'Ringkasan order, omzet, HPP, dan margin per customer',
            'rows'      => $rows,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
            'perPage'   => $perPage,
            'page'      => $page,
            'totalRows' => $totalRows,
            'totalPages' => $totalPages,
            'mode'      => $mode,
            'totalOrdersAll' => (int) ($totals['total_orders'] ?? 0),
            'totalItemsAll'  => (float) ($totals['total_items'] ?? 0),
            'totalSalesAll'  => (float) ($totals['total_sales'] ?? 0),
            'totalCostAll'   => (float) ($totals['total_cost'] ?? 0),
        ];

        return view('reports/sales_customer', $data);
    }

    /**
     * Detail penjualan per customer (list transaksi + ringkasan).
     */
    public function customerDetail(int $id)
    {
        $dateFrom = $this->request->getGet('date_from') ?: null;
        $dateTo   = $this->request->getGet('date_to') ?: null;
        $perPage  = $this->sanitizePerPage($this->request->getGet('per_page'));
        $page     = $this->sanitizePage($this->request->getGet('page'));
        $mode     = strtolower((string) ($this->request->getGet('mode') ?? ''));
        if (! in_array($mode, ['full', 'compact'], true)) {
            $mode = null;
        }

        $db = \Config\Database::connect();

        $itemsSub = $db->table('sale_items')
            ->select('sale_id, SUM(qty) AS total_items')
            ->groupBy('sale_id');
        $itemsSql = $itemsSub->getCompiledSelect();

        $customer = $db->table('customers')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (! $customer) {
            $nameRow = $db->table('sales')
                ->select('customer_name')
                ->where('customer_id', $id)
                ->limit(1)
                ->get()
                ->getRowArray();
            $customer = [
                'id' => $id,
                'name' => $nameRow['customer_name'] ?? 'Tamu',
                'phone' => null,
                'email' => null,
            ];
        }

        $summaryBuilder = $db->table('sales s')
            ->select('COUNT(s.id) AS total_orders')
            ->select('SUM(COALESCE(si.total_items, 0)) AS total_items', false)
            ->select('SUM(s.total_amount) AS total_sales')
            ->select('SUM(s.total_cost) AS total_cost')
            ->select('COUNT(DISTINCT s.sale_date) AS active_days')
            ->select('MAX(s.sale_date) AS last_order_date')
            ->join('(' . $itemsSql . ') si', 'si.sale_id = s.id', 'left', false)
            ->where('s.status !=', 'void')
            ->where('s.customer_id', $id);

        $this->applyDateFilter($summaryBuilder, $dateFrom, $dateTo, 's.sale_date');

        $summary = $summaryBuilder->get()->getRowArray() ?? [
            'total_orders' => 0,
            'total_items' => 0,
            'total_sales' => 0,
            'total_cost' => 0,
            'active_days' => 0,
            'last_order_date' => null,
        ];

        $countBuilder = $db->table('sales s')
            ->select('COUNT(s.id) AS cnt')
            ->where('s.status !=', 'void')
            ->where('s.customer_id', $id);

        $this->applyDateFilter($countBuilder, $dateFrom, $dateTo, 's.sale_date');

        $countRow = $countBuilder->get()->getRowArray();
        $totalRows = (int) ($countRow['cnt'] ?? 0);

        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $listBuilder = $db->table('sales s')
            ->select('s.id, s.sale_date, s.invoice_no, s.total_amount, s.total_cost, s.payment_method, s.kitchen_status, s.created_at')
            ->select('COALESCE(si.total_items, 0) AS total_items', false)
            ->join('(' . $itemsSql . ') si', 'si.sale_id = s.id', 'left', false)
            ->where('s.status !=', 'void')
            ->where('s.customer_id', $id)
            ->orderBy('s.sale_date', 'DESC')
            ->orderBy('s.id', 'DESC')
            ->limit($perPage, $offset);

        $this->applyDateFilter($listBuilder, $dateFrom, $dateTo, 's.sale_date');

        $rows = $listBuilder->get()->getResultArray();

        return view('reports/sales_customer_detail', [
            'title'      => 'Detail Penjualan Customer',
            'subtitle'   => 'Rincian transaksi dan ringkasan per customer',
            'customer'   => $customer,
            'summary'    => $summary,
            'rows'       => $rows,
            'dateFrom'   => $dateFrom,
            'dateTo'     => $dateTo,
            'perPage'    => $perPage,
            'page'       => $page,
            'totalRows'  => $totalRows,
            'totalPages' => $totalPages,
            'mode'       => $mode,
        ]);
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

    private function exportPerCustomerCsv(array $rows, ?string $dateFrom, ?string $dateTo)
    {
        $fh = fopen('php://temp', 'r+');

        fputcsv($fh, ['Customer', 'Phone', 'Email', 'Orders', 'Items', 'Omzet', 'HPP', 'Margin', 'Margin %', 'Active Days', 'Avg Orders/Day', 'Last Order']);

        foreach ($rows as $r) {
            $orders = (int) ($r['total_orders'] ?? 0);
            $items  = (float) ($r['total_items'] ?? 0);
            $sales  = (float) ($r['total_sales'] ?? 0);
            $cost   = (float) ($r['total_cost'] ?? 0);
            $margin = $sales - $cost;
            $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;
            $activeDays = (int) ($r['active_days'] ?? 0);
            $avgOrders  = $activeDays > 0 ? ($orders / $activeDays) : 0;

            fputcsv($fh, [
                $r['customer_name'] ?? 'Tamu',
                $r['customer_phone'] ?? '',
                $r['customer_email'] ?? '',
                $orders,
                $items,
                $sales,
                $cost,
                $margin,
                round($marginPct, 2),
                $activeDays,
                round($avgOrders, 2),
                $r['last_order_date'] ?? '',
            ]);
        }

        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        $period = $this->formatPeriodLabel($dateFrom, $dateTo);
        $filename = 'sales_per_customer_' . $period . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    private function exportPerCategoryPdf(array $rows, ?string $dateFrom, ?string $dateTo, array $totals)
    {
        $period = $this->formatPeriodLabel($dateFrom, $dateTo);
        $filename = 'sales_per_category_' . $period . '.pdf';

        $data = [
            'title'     => 'Penjualan per Kategori Menu',
            'subtitle'  => 'Qty, omzet, HPP, dan margin per kategori menu',
            'metaLines' => ['Periode: ' . $this->formatPeriodDisplay($dateFrom, $dateTo)],
            'rows'      => $rows,
            'totalQtyAll'   => (float) ($totals['total_qty'] ?? 0),
            'totalSalesAll' => (float) ($totals['total_sales'] ?? 0),
            'totalCostAll'  => (float) ($totals['total_cost'] ?? 0),
        ];

        return $this->renderPdf('reports/pdf/sales_category', $data, $filename);
    }

    private function exportPerMenuPdf(array $rows, ?string $dateFrom, ?string $dateTo, array $totals)
    {
        $period = $this->formatPeriodLabel($dateFrom, $dateTo);
        $filename = 'sales_per_menu_' . $period . '.pdf';

        $data = [
            'title'     => 'Penjualan per Menu',
            'subtitle'  => 'Qty, omzet, HPP, dan margin per menu untuk periode tertentu',
            'metaLines' => ['Periode: ' . $this->formatPeriodDisplay($dateFrom, $dateTo)],
            'rows'      => $rows,
            'totalQtyAll'   => (float) ($totals['total_qty'] ?? 0),
            'totalSalesAll' => (float) ($totals['total_sales'] ?? 0),
            'totalCostAll'  => (float) ($totals['total_cost'] ?? 0),
        ];

        return $this->renderPdf('reports/pdf/sales_menu', $data, $filename);
    }

    private function exportTimePdf(
        array $rows,
        ?string $dateFrom,
        ?string $dateTo,
        string $group,
        bool $allDay,
        string $startTime,
        string $endTime,
        array $totals
    ) {
        $period = $this->formatPeriodLabel($dateFrom, $dateTo);
        $filename = 'sales_by_time_' . $group . '_' . $period . '.pdf';

        $metaLines = [
            'Periode: ' . $this->formatPeriodDisplay($dateFrom, $dateTo),
            'Group: ' . strtoupper($group),
            'All day: ' . ($allDay ? 'Ya' : 'Tidak'),
        ];

        if (! $allDay) {
            $metaLines[] = 'Jam: ' . $startTime . ' - ' . $endTime;
        }

        $data = [
            'title'     => 'Laporan Penjualan by Time',
            'subtitle'  => 'Ringkasan omzet, HPP, margin per periode (harian/mingguan/bulanan/tahunan)',
            'metaLines' => $metaLines,
            'rows'      => $rows,
            'totalSalesAll' => (float) ($totals['total_sales'] ?? 0),
            'totalCostAll'  => (float) ($totals['total_cost'] ?? 0),
        ];

        return $this->renderPdf('reports/pdf/sales_time', $data, $filename);
    }

    private function exportPerCustomerPdf(array $rows, ?string $dateFrom, ?string $dateTo, string $mode, array $totals)
    {
        $mode = $mode === 'compact' ? 'compact' : 'full';
        $period = $this->formatPeriodLabel($dateFrom, $dateTo);
        $filename = 'sales_per_customer_' . $period . '.pdf';
        $modeLabel = $mode === 'compact' ? 'Ringkas' : 'Lengkap';

        $data = [
            'title'     => 'Penjualan per Customer',
            'subtitle'  => 'Ringkasan order, omzet, HPP, dan margin per customer',
            'metaLines' => [
                'Periode: ' . $this->formatPeriodDisplay($dateFrom, $dateTo),
                'Mode: ' . $modeLabel,
            ],
            'rows'      => $rows,
            'mode'      => $mode,
            'totalOrdersAll' => (int) ($totals['total_orders'] ?? 0),
            'totalItemsAll'  => (float) ($totals['total_items'] ?? 0),
            'totalSalesAll'  => (float) ($totals['total_sales'] ?? 0),
            'totalCostAll'   => (float) ($totals['total_cost'] ?? 0),
        ];

        $orientation = $mode === 'compact' ? 'portrait' : 'landscape';

        return $this->renderPdf('reports/pdf/sales_customer', $data, $filename, 'A4', $orientation);
    }

    private function renderPdf(string $view, array $data, string $filename, string $paper = 'A4', string $orientation = 'portrait')
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->loadHtml(view($view, $data));
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    private function formatPeriodDisplay(?string $from, ?string $to): string
    {
        if ($from && $to) {
            return $from . ' s/d ' . $to;
        }
        if ($from) {
            return 'Dari ' . $from;
        }
        if ($to) {
            return 'Sampai ' . $to;
        }

        return 'Semua tanggal';
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

    private function countCustomerRows(?string $dateFrom, ?string $dateTo): int
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sales s')
            ->select('COUNT(DISTINCT s.customer_id) AS cnt')
            ->where('s.status !=', 'void');

        $this->applyDateFilter($builder, $dateFrom, $dateTo, 's.sale_date');

        $row = $builder->get()->getRowArray();
        return (int) ($row['cnt'] ?? 0);
    }

    private function aggregateCustomerTotals(?string $dateFrom, ?string $dateTo): array
    {
        $db = \Config\Database::connect();

        $itemsSub = $db->table('sale_items')
            ->select('sale_id, SUM(qty) AS total_items')
            ->groupBy('sale_id');
        $itemsSql = $itemsSub->getCompiledSelect();

        $builder = $db->table('sales s')
            ->select('COUNT(s.id) AS total_orders, SUM(COALESCE(si.total_items, 0)) AS total_items, SUM(s.total_amount) AS total_sales, SUM(s.total_cost) AS total_cost', false)
            ->join('(' . $itemsSql . ') si', 'si.sale_id = s.id', 'left', false)
            ->where('s.status !=', 'void');

        $this->applyDateFilter($builder, $dateFrom, $dateTo, 's.sale_date');

        return $builder->get()->getRowArray() ?? ['total_orders' => 0, 'total_items' => 0, 'total_sales' => 0, 'total_cost' => 0];
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
