<?php

namespace App\Models;

use CodeIgniter\Model;

class RecipeItemModel extends Model
{
    protected $table      = 'recipe_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'recipe_id',
        'raw_material_id',
        'qty',
        'waste_pct',
        'note',
    ];

    // Penting: karena tabel belum punya created_at/updated_at
    protected $useTimestamps = false;

    public function withMaterial()
    {
        return $this->select('recipe_items.*, raw_materials.name AS material_name, units.short_name AS unit_short')
                    ->join('raw_materials', 'raw_materials.id = recipe_items.raw_material_id', 'left')
                    ->join('units', 'units.id = raw_materials.unit_id', 'left');
    }
}
