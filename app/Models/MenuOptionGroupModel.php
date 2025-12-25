<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuOptionGroupModel extends Model
{
    protected $table         = 'menu_option_groups';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'menu_id',
        'name',
        'is_required',
        'min_select',
        'max_select',
        'sort_order',
        'show_on_kitchen_ticket',
        'is_active',
    ];
}
