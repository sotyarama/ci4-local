<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseItemModel extends Model
{
    protected $table         = 'purchase_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'purchase_id',
        'raw_material_id',
        'raw_material_variant_id',
        'qty',
        'unit_cost',
        'total_cost',
    ];
}
