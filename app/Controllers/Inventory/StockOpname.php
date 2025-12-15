<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;

class StockOpname extends BaseController
{
    public function index()
    {
        return view('inventory/stock_opname', [
            'title'    => 'Stock Opname & Selisih Fisik',
            'subtitle' => 'Stub: rencana input hitung fisik + selisih vs sistem.',
        ]);
    }
}
