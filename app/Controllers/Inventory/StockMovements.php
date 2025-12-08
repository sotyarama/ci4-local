<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;
use App\Models\StockMovementModel;
use App\Models\RawMaterialModel;

class StockMovements extends BaseController
{
    protected StockMovementModel $movementModel;
    protected RawMaterialModel $rawModel;

    public function __construct()
    {
        $this->movementModel = new StockMovementModel();
        $this->rawModel      = new RawMaterialModel();
    }

    /**
     * Riwayat pergerakan stok (IN/OUT) dengan filter bahan & tanggal.
     */
    public function index()
    {
        $rawId    = (int) ($this->request->getGet('raw_material_id') ?? 0);
        $dateFrom = $this->request->getGet('date_from') ?: null;
        $dateTo   = $this->request->getGet('date_to') ?: null;

        $builder = $this->movementModel
            ->withMaterial()
            ->orderBy('stock_movements.created_at', 'DESC')
            ->orderBy('stock_movements.id', 'DESC');

        if ($rawId > 0) {
            $builder->where('stock_movements.raw_material_id', $rawId);
        }

        if ($dateFrom) {
            $builder->where('DATE(stock_movements.created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(stock_movements.created_at) <=', $dateTo);
        }

        $movements = $builder->findAll();

        $materials = $this->rawModel
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'       => 'Riwayat Stok',
            'subtitle'    => 'Pergerakan stok IN/OUT per bahan baku',
            'movements'   => $movements,
            'materials'   => $materials,
            'filterRawId' => $rawId,
            'filterFrom'  => $dateFrom,
            'filterTo'    => $dateTo,
        ];

        return view('inventory/stock_movements_index', $data);
    }
}
