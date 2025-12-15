<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $tz        = new \DateTimeZone('Asia/Jakarta');
        $todayObj  = new \DateTime('now', $tz);
        $today     = $todayObj->format('Y-m-d');
        $monthStart = $todayObj->format('Y-m-01');
        $weekStart = (clone $todayObj)->modify('-6 days')->format('Y-m-d');

        $lastMonthStart = (clone $todayObj)->modify('first day of last month')->format('Y-m-d');
        $lastMonthEnd   = (clone $todayObj)->modify('last day of last month')->format('Y-m-d');

        $db = \Config\Database::connect();

        $todayStats     = $this->aggregateSales($db, $today, $today);
        $monthStats     = $this->aggregateSales($db, $monthStart, $today);
        $lastMonthStats = $this->aggregateSales($db, $lastMonthStart, $lastMonthEnd);
        $weekStats      = $this->aggregateSales($db, $weekStart, $today);

        $topMenus    = $this->getTopMenus($db, $weekStart, $today, 5);
        $recentSales = $this->getRecentSales($db, 5);
        $lowStocks   = $this->getLowStocks($db, 6);

        $purchaseMonth = $this->sumPurchases($db, $monthStart, $today);
        $overheadMonth = $this->sumOverheads($db, $monthStart, $today);
        $payrollMonth  = $this->sumPayrolls($db, $todayObj->format('Y-m'));

        $monthDeltaPct = null;
        if ($lastMonthStats['sales'] > 0) {
            $monthDeltaPct = (($monthStats['sales'] - $lastMonthStats['sales']) / $lastMonthStats['sales']) * 100;
        }

        $data = [
            'title'          => 'Cafe POS Dashboard',
            'subtitle'       => 'Ringkasan penjualan, stok, dan biaya operasional',
            'today'          => $today,
            'weekStart'      => $weekStart,
            'monthLabel'     => $todayObj->format('Y-m'),
            'lastMonthLabel' => (clone $todayObj)->modify('first day of last month')->format('Y-m'),
            'todayStats'     => $todayStats,
            'monthStats'     => $monthStats,
            'lastMonthStats' => $lastMonthStats,
            'weekStats'      => $weekStats,
            'monthDeltaPct'  => $monthDeltaPct,
            'topMenus'       => $topMenus,
            'recentSales'    => $recentSales,
            'lowStocks'      => $lowStocks,
            'purchaseMonth'  => $purchaseMonth,
            'overheadMonth'  => $overheadMonth + $payrollMonth,
            'overheadBreakdown' => [
                'operational' => $overheadMonth,
                'payroll'     => $payrollMonth,
            ],
        ];

        return view('dashboard', $data);
    }

    private function aggregateSales($db, ?string $dateFrom, ?string $dateTo): array
    {
        $builder = $db->table('sales')
            ->selectSum('total_amount', 'total_sales')
            ->selectSum('total_cost', 'total_cost')
            ->selectCount('id', 'tx_count')
            ->where('status !=', 'void');

        if ($dateFrom) {
            $builder->where('sale_date >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('sale_date <=', $dateTo);
        }

        $row = $builder->get()->getRowArray() ?? [];

        $itemBuilder = $db->table('sale_items si')
            ->select('SUM(si.qty) AS item_qty')
            ->join('sales s', 's.id = si.sale_id', 'inner')
            ->where('s.status !=', 'void');

        if ($dateFrom) {
            $itemBuilder->where('s.sale_date >=', $dateFrom);
        }
        if ($dateTo) {
            $itemBuilder->where('s.sale_date <=', $dateTo);
        }

        $itemRow = $itemBuilder->get()->getRowArray() ?? [];

        $sales  = (float) ($row['total_sales'] ?? 0);
        $cost   = (float) ($row['total_cost'] ?? 0);
        $margin = $sales - $cost;
        $tx     = (int) ($row['tx_count'] ?? 0);
        $items  = (float) ($itemRow['item_qty'] ?? 0);

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

    private function getRecentSales($db, int $limit = 5): array
    {
        return $db->table('sales')
            ->where('status !=', 'void')
            ->orderBy('sale_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

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
