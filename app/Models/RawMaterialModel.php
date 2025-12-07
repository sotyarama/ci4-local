<?php

namespace App\Models;

use CodeIgniter\Model;

class RawMaterialModel extends Model
{
    protected $table         = 'raw_materials';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'name',
        'unit_id',
        'current_stock',
        'min_stock',
        'cost_last',
        'cost_avg',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function withUnit()
    {
        return $this->select('raw_materials.*, units.name AS unit_name, units.short_name AS unit_short')
                    ->join('units', 'units.id = raw_materials.unit_id', 'left');
    }
}
