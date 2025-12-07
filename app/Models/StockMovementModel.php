<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table         = 'stock_movements';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'raw_material_id',
        'movement_type',
        'qty',
        'ref_type',
        'ref_id',
        'note',
        'created_at',
    ];
}
