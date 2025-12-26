<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleItemModel extends Model
{
    protected $table         = 'sale_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';

    protected $allowedFields = [
        'sale_id',
        'menu_id',
        'qty',
        'price',
        'subtotal',
        'hpp_snapshot',
        'item_note',
    ];

    // Tabel sale_items tidak punya created_at/updated_at
    protected $useTimestamps = false;

    /**
     * Join dengan tabel menus (dan kategori) untuk kebutuhan laporan.
     *
     * @return $this
     */
    public function withMenu()
    {
        return $this->select('
                sale_items.*,
                m.name        AS menu_name,
                m.price       AS menu_default_price,
                c.name        AS category_name
            ')
            ->join('menus m', 'm.id = sale_items.menu_id', 'left')
            ->join('menu_categories c', 'c.id = m.menu_category_id', 'left');
    }
}
