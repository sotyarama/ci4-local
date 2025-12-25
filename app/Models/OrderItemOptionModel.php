<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemOptionModel extends Model
{
    protected $table         = 'sale_item_options';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'sale_item_id',
        'option_id',
        'qty_selected',
        'option_name_snapshot',
        'price_delta_snapshot',
        'variant_id_snapshot',
    ];
}
