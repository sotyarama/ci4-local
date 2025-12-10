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
        $openingBalance = $this->request->getGet('opening_balance');
        $openingBalance = $openingBalance !== null && $openingBalance !== '' ? (float) $openingBalance : null;
        $runningBalanceMap = [];

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

        // Hitung saldo berjalan hanya bila filter bahan dipilih agar konteks jelas.
        if ($rawId > 0 && ! empty($movements)) {
            $ascBuilder = $this->movementModel
                ->where('stock_movements.raw_material_id', $rawId);

            if ($dateFrom) {
                $ascBuilder->where('DATE(stock_movements.created_at) >=', $dateFrom);
            }
            if ($dateTo) {
                $ascBuilder->where('DATE(stock_movements.created_at) <=', $dateTo);
            }

            $ascMovements = $ascBuilder
                ->orderBy('stock_movements.created_at', 'ASC')
                ->orderBy('stock_movements.id', 'ASC')
                ->findAll();

            $balance = round($openingBalance ?? 0.0, 6);
            foreach ($ascMovements as $mv) {
                $qty = round((float) ($mv['qty'] ?? 0), 6);
                if (strtoupper($mv['movement_type']) === 'IN') {
                    $balance += $qty;
                } else {
                    $balance -= $qty;
                }

                $balance = round($balance, 6);
                $runningBalanceMap[$mv['id']] = $balance;
            }
        }

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
            'openingBalance' => $openingBalance,
            'runningBalanceMap' => $runningBalanceMap,
        ];

        return view('inventory/stock_movements_index', $data);
    }

    /**
     * Kartu stok per bahan: kronologi IN/OUT + saldo berjalan.
     */
    public function card()
    {
        $rawId          = (int) ($this->request->getGet('raw_material_id') ?? 0);
        $dateFrom       = $this->request->getGet('date_from') ?: null;
        $dateTo         = $this->request->getGet('date_to') ?: null;
        $openingBalance = $this->request->getGet('opening_balance');
        $openingBalance = $openingBalance !== null && $openingBalance !== '' ? (float) $openingBalance : null;

        $materials = $this->rawModel
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $selectedMaterial = $rawId > 0 ? $this->rawModel->find($rawId) : null;

        $movements = [];
        $runningBalance = null;

        if ($selectedMaterial) {
            $builder = $this->movementModel
                ->withMaterial()
                ->where('stock_movements.raw_material_id', $rawId);

            if ($dateFrom) {
                $builder->where('DATE(stock_movements.created_at) >=', $dateFrom);
            }
            if ($dateTo) {
                $builder->where('DATE(stock_movements.created_at) <=', $dateTo);
            }

            $movements = $builder
                ->orderBy('stock_movements.created_at', 'ASC')
                ->orderBy('stock_movements.id', 'ASC')
                ->findAll();

            $runningBalance = [];
            $balance = round($openingBalance ?? 0.0, 6);

            foreach ($movements as $mv) {
                $qty = round((float) ($mv['qty'] ?? 0), 6);
                if (strtoupper($mv['movement_type']) === 'IN') {
                    $balance += $qty;
                } else {
                    $balance -= $qty;
                }

                $balance = round($balance, 6);
                $runningBalance[] = $balance;
            }
        }

        $data = [
            'title'            => 'Kartu Stok',
            'subtitle'         => 'Kronologi IN/OUT dan saldo berjalan per bahan baku',
            'materials'        => $materials,
            'selectedMaterial' => $selectedMaterial,
            'movements'        => $movements,
            'runningBalance'   => $runningBalance,
            'filterFrom'       => $dateFrom,
            'filterTo'         => $dateTo,
            'openingBalance'   => $openingBalance,
        ];

        return view('inventory/stock_card', $data);
    }
}
