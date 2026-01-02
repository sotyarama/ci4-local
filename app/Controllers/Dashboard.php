<?php

namespace App\Controllers;

use DateTime;
use DateTimeZone;

/**
 * Dashboard Controller
 *
 * Menyediakan ringkasan metrik utama untuk halaman dashboard:
 * - Penjualan: hari ini, 7 hari terakhir, bulan berjalan, bulan lalu
 * - Top menu (7 hari terakhir)
 * - Transaksi terbaru
 * - Stok bahan baku yang mendekati/minimum
 * - Total pembelian & overhead bulan berjalan (operational + payroll)
 *
 * Catatan:
 * - Tidak mengubah data; hanya agregasi/summary untuk view `dashboard`.
 * - Semua query mengecualikan transaksi sales berstatus "void".
 */
class Dashboard extends BaseController
{
    /**
     * Render dashboard utama.
     *
     * @return string
     */
    public function index(): string
    {
        // 1) Determine selected date range (date-only) using helper
        helper('tr_daterange');
        $range = tr_parse_range_dateonly($this->request);
        $dateFrom = $range['startDate'];
        $dateTo   = $range['endDate'];

        // Also keep timezone & today for labels
        $tz = new DateTimeZone('Asia/Jakarta');
        $todayDate = new DateTime('now', $tz);

        // Format tanggal yang dipakai oleh query (kolom date disimpan sebagai Y-m-d)
        $today       = $todayDate->format('Y-m-d');
        $monthStart  = $todayDate->format('Y-m-01');
        $weekStart   = (clone $todayDate)->modify('-6 days')->format('Y-m-d'); // rolling 7 days (incl today)

        // Periode bulan lalu
        $lastMonthStart = (clone $todayDate)->modify('first day of last month')->format('Y-m-d');
        $lastMonthEnd   = (clone $todayDate)->modify('last day of last month')->format('Y-m-d');

        // 3) Siapkan DB connection
        $db = \Config\Database::connect();

        // 4) Agregasi sales: use selected date range (date-only) for dashboard KPIs
        $todayStats     = $this->aggregateSales($db, $dateFrom, $dateTo);
        $weekStats      = $this->aggregateSales($db, $dateFrom, $dateTo);
        $monthStats     = $this->aggregateSales($db, $dateFrom, $dateTo);
        $lastMonthStats = $this->aggregateSales($db, $dateFrom, $dateTo);

        // 4) Dataset tambahan untuk widget dashboard
        // Top menus & recent sales follow the selected range
        $topMenus    = $this->getTopMenus($db, $dateFrom, $dateTo, 5);
        $recentSales = $this->getRecentSales($db, 5, $dateFrom, $dateTo);
        $lowStocks   = $this->getLowStocks($db, 6);

        // 5) Rekap pembelian & biaya (bulan berjalan)
        $purchaseMonth = $this->sumPurchases($db, $monthStart, $today);
        $overheadMonth = $this->sumOverheads($db, $monthStart, $today);
        $payrollMonth  = $this->sumPayrolls($db, $todayDate->format('Y-m'));

        // 6) Delta penjualan bulan berjalan vs bulan lalu (persen)
        $monthDeltaPct = null;
        if (($lastMonthStats['sales'] ?? 0) > 0) {
            $monthDeltaPct = (($monthStats['sales'] - $lastMonthStats['sales']) / $lastMonthStats['sales']) * 100;
        }

        // 7) Payload untuk view (jangan ubah key agar view tetap kompatibel)
        $data = [
            'title'            => 'Cafe POS Dashboard',
            'subtitle'         => 'Ringkasan penjualan, stok, dan biaya operasional',

            // Labels & periode tampilan
            'today'            => $today,
            'weekStart'        => $weekStart,
            'dateFrom'         => $dateFrom,
            'dateTo'           => $dateTo,
            'monthLabel'       => $todayDate->format('Y-m'),
            'lastMonthLabel'   => (clone $todayDate)->modify('first day of last month')->format('Y-m'),

            // Sales stats
            'todayStats'       => $todayStats,
            'weekStats'        => $weekStats,
            'monthStats'       => $monthStats,
            'lastMonthStats'   => $lastMonthStats,
            'monthDeltaPct'    => $monthDeltaPct,

            // Dashboard widgets
            'topMenus'         => $topMenus,
            'recentSales'      => $recentSales,
            'lowStocks'        => $lowStocks,

            // Financial summaries (bulan berjalan)
            'purchaseMonth'    => $purchaseMonth,
            'overheadMonth'    => $overheadMonth + $payrollMonth,
            'overheadBreakdown' => [
                'operational' => $overheadMonth,
                'payroll'     => $payrollMonth,
            ],
        ];

        return view('dashboard', $data);
    }

    /**
     * Agregasi penjualan dan item untuk periode tertentu.
     *
     * @param mixed       $db       Database connection (CI4)
     * @param string|null $dateFrom Format 'Y-m-d' (inclusive) atau null
     * @param string|null $dateTo   Format 'Y-m-d' (inclusive) atau null
     * @return array{sales:float,cost:float,margin:float,margin_pct:float,tx:int,items:float,avg_ticket:float}
     */
    private function aggregateSales($db, ?string $dateFrom, ?string $dateTo): array
    {
        // A) Ringkas transaksi sales (total sales, total cost, jumlah transaksi)
        $salesBuilder = $db->table('sales')
            ->selectSum('total_amount', 'total_sales')
            ->selectSum('total_cost', 'total_cost')
            ->selectCount('id', 'tx_count')
            ->where('status !=', 'void');

        if ($dateFrom) {
            $salesBuilder->where('sale_date >=', $dateFrom);
        }
        if ($dateTo) {
            $salesBuilder->where('sale_date <=', $dateTo);
        }

        $salesRow = $salesBuilder->get()->getRowArray() ?? [];

        // B) Ringkas jumlah item terjual (qty) dengan join ke sales untuk filter void & tanggal
        $itemsBuilder = $db->table('sale_items si')
            ->select('SUM(si.qty) AS item_qty')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->where('s.status !=', 'void');

        if ($dateFrom) {
            $itemsBuilder->where('s.sale_date >=', $dateFrom);
        }
        if ($dateTo) {
            $itemsBuilder->where('s.sale_date <=', $dateTo);
        }

        $itemsRow = $itemsBuilder->get()->getRowArray() ?? [];

        // C) Hitung metrik turunan (margin, margin%, avg ticket)
        $sales  = (float) ($salesRow['total_sales'] ?? 0);
        $cost   = (float) ($salesRow['total_cost'] ?? 0);
        $tx     = (int)   ($salesRow['tx_count'] ?? 0);
        $items  = (float) ($itemsRow['item_qty'] ?? 0);

        $margin = $sales - $cost;

        return [
            'sales'      => $sales,
            'cost'       => $cost,
            'margin'     => $margin,
            'margin_pct' => $sales > 0 ? ($margin / $sales * 100.0) : 0,
            'tx'         => $tx,
            'items'      => $items,
            'avg_ticket' => $tx > 0 ? ($sales / $tx) : 0,
        ];
    }

    /**
     * Ambil top menu untuk periode tertentu (default 7 hari terakhir).
     */
    private function getTopMenus($db, string $dateFrom, string $dateTo, int $limit = 5): array
    {
        return $db->table('sale_items si')
            ->select('si.menu_id, COALESCE(m.name, "Menu (hapus)") AS menu_name')
            ->select('SUM(si.qty) AS total_qty')
            ->select('SUM(si.subtotal) AS total_sales')
            ->select('SUM(si.hpp_snapshot * si.qty) AS total_cost')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->join('menus m', 'm.id = si.menu_id', 'left')
            ->where('s.status !=', 'void')
            ->where('s.sale_date >=', $dateFrom)
            ->where('s.sale_date <=', $dateTo)
            ->groupBy('si.menu_id, m.name')
            ->orderBy('total_sales', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil transaksi penjualan terbaru (exclude void).
     */
    private function getRecentSales($db, int $limit = 5, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $builder = $db->table('sales')
            ->where('status !=', 'void');

        if ($dateFrom) {
            $builder->where('sale_date >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('sale_date <=', $dateTo);
        }

        return $builder
            ->orderBy('sale_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil daftar bahan baku yang stoknya <= minimum stock.
     */
    private function getLowStocks($db, int $limit = 6): array
    {
        return $db->table('raw_materials rm')
            ->select('rm.*, u.short_name AS unit_short')
            ->join('units u', 'u.id = rm.unit_id', 'left')
            ->where('rm.min_stock >', 0)
            ->where('rm.current_stock <= rm.min_stock')
            ->where('rm.is_active', 1)
            ->orderBy('(rm.current_stock / NULLIF(rm.min_stock, 0))', 'ASC', false)
            ->orderBy('rm.current_stock', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Total pembelian pada rentang tanggal.
     */
    private function sumPurchases($db, string $dateFrom, string $dateTo): float
    {
        $row = $db->table('purchases')
            ->select('SUM(total_amount) AS total_amount')
            ->where('purchase_date >=', $dateFrom)
            ->where('purchase_date <=', $dateTo)
            ->get()
            ->getRowArray();

        return (float) ($row['total_amount'] ?? 0);
    }

    /**
     * Total overhead operasional pada rentang tanggal.
     */
    private function sumOverheads($db, string $dateFrom, string $dateTo): float
    {
        $row = $db->table('overheads')
            ->select('SUM(amount) AS total_amount')
            ->where('trans_date >=', $dateFrom)
            ->where('trans_date <=', $dateTo)
            ->get()
            ->getRowArray();

        return (float) ($row['total_amount'] ?? 0);
    }

    /**
     * Total payroll pada periode bulan tertentu (format 'Y-m').
     */
    private function sumPayrolls($db, string $periodMonth): float
    {
        $row = $db->table('payrolls')
            ->select('SUM(amount) AS total_amount')
            ->where('period_month', $periodMonth)
            ->get()
            ->getRowArray();

        return (float) ($row['total_amount'] ?? 0);
    }
}
