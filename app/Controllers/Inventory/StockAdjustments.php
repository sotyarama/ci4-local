<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;

class StockAdjustments extends BaseController
{
    public function index()
    {
        return view('inventory/stock_adjustments', [
            'title'    => 'Penyesuaian Stok',
            'subtitle' => 'Stub: rencana form adjustment manual + jejak movement.',
        ]);
    }
}
