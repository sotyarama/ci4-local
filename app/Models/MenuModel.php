<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table          = 'menus';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'name',
        'menu_category_id',
        'sku',
        'price',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function withCategory()
    {
        return $this->select('menus.*, menu_categories.name AS category_name')
                    ->join('menu_categories', 'menu_categories.id = menus.menu_category_id', 'left');
    }
}
