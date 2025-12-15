<?php

namespace App\Controllers\Reports;

use App\Controllers\BaseController;
use App\Models\RawMaterialModel;
use App\Models\StockMovementModel;

class StockSummary extends BaseController
{
    protected RawMaterialModel $rawModel;
    protected StockMovementModel $movementModel;

    public function __construct()
    {
        $this->rawModel      = new RawMaterialModel();
        $this->movementModel = new StockMovementModel();
    }

    public function variance()
    {
        $dateFrom = $this->request->getGet('date_from') ?: null;
        $dateTo   = $this->request->getGet('date_to') ?: null;
        $materialId = (int) ($this->request->getGet('material_id') ?? 0);
        $search   = trim((string) ($this->request->getGet('q') ?? ''));

        $today = date('Y-m-d');
        if (! $dateTo) {
            $dateTo = $today;
        }
        if (! $dateFrom) {
            $dateFrom = date('Y-m-01');
        }

        $fromDateTime = $dateFrom . ' 00:00:00';
        $toDateTime   = $dateTo . ' 23:59:59';

        // Ambil daftar bahan
        $materialsBuilder = $this->rawModel->withUnit()->orderBy('name', 'ASC');
        if ($materialId > 0) {
            $materialsBuilder->where('raw_materials.id', $materialId);
        }
        if ($search !== '') {
            $materialsBuilder->like('raw_materials.name', $search);
        }
        $materials = $materialsBuilder->findAll();
        $materialIds = array_column($materials, 'id');

        $openingMap = [];
        $rangeMap   = [];
        $closingMap = [];

        if (! empty($materialIds)) {
            // Opening: total movement sebelum start
            $openingRows = $this->movementModel
                ->select('raw_material_id')
                ->select('SUM(CASE WHEN movement_type = "IN" THEN qty ELSE -qty END) AS net_qty')
                ->whereIn('raw_material_id', $materialIds)
                ->where('created_at <', $fromDateTime)
                ->groupBy('raw_material_id')
                ->findAll();

            foreach ($openingRows as $row) {
                $openingMap[(int) $row['raw_material_id']] = (float) ($row['net_qty'] ?? 0);
            }

            // Periode range
            $rangeRows = $this->movementModel
                ->select('raw_material_id')
                ->select('SUM(CASE WHEN movement_type = "IN" THEN qty ELSE 0 END) AS total_in')
                ->select('SUM(CASE WHEN movement_type = "OUT" THEN qty ELSE 0 END) AS total_out')
                ->select('SUM(CASE WHEN movement_type = "IN" THEN qty ELSE -qty END) AS net_qty')
                ->whereIn('raw_material_id', $materialIds)
                ->where('created_at >=', $fromDateTime)
                ->where('created_at <=', $toDateTime)
                ->groupBy('raw_material_id')
                ->findAll();

            foreach ($rangeRows as $row) {
                $id = (int) $row['raw_material_id'];
                $rangeMap[$id] = [
                    'in'  => (float) ($row['total_in'] ?? 0),
                    'out' => (float) ($row['total_out'] ?? 0),
                    'net' => (float) ($row['net_qty'] ?? 0),
                ];
            }

            // Closing sampai akhir periode: opening + range net
            foreach ($materialIds as $id) {
                $opening = $openingMap[$id] ?? 0.0;
                $net     = $rangeMap[$id]['net'] ?? 0.0;
                $closingMap[$id] = $opening + $net;
            }
        }

        $rows = [];
        foreach ($materials as $m) {
            $id = (int) $m['id'];
            $opening = (float) ($openingMap[$id] ?? 0);
            $in      = (float) ($rangeMap[$id]['in'] ?? 0);
            $out     = (float) ($rangeMap[$id]['out'] ?? 0);
            $closing = (float) ($closingMap[$id] ?? $opening);
            $current = (float) ($m['current_stock'] ?? 0);
            $variance = $current - $closing;

            $rows[] = [
                'id'          => $id,
                'name'        => $m['name'],
                'unit'        => $m['unit_short'] ?? '',
                'opening'     => $opening,
                'in'          => $in,
                'out'         => $out,
                'closing'     => $closing,
                'current'     => $current,
                'variance'    => $variance,
            ];
        }

        return view('reports/stock_variance', [
            'title'    => 'Laporan Stok & Selisih',
            'subtitle' => 'Rekap saldo awal, IN/OUT, saldo akhir periode vs stok sistem.',
            'rows'     => $rows,
            'dateFrom' => $dateFrom,
            'dateTo'   => $dateTo,
            'materialId'=> $materialId,
            'search'   => $search,
            'materials'=> $materials,
        ]);
    }
}
