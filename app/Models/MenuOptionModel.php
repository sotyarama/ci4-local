<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuOptionModel extends Model
{
    protected $table         = 'menu_options';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'group_id',
        'name',
        'price_delta',
        'variant_id',
        'qty_multiplier',
        'sort_order',
        'is_active',
    ];
}
