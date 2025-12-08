<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table         = 'stock_movements';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    protected $allowedFields = [
        'raw_material_id',
        'movement_type',
        'qty',
        'ref_type',
        'ref_id',
        'note',
        'created_at',
    ];

    /**
     * Helper untuk join ke raw_materials + units
     */
    public function withMaterial()
    {
        return $this->select('stock_movements.*, r.name AS material_name, u.short_name AS unit_short')
                    ->join('raw_materials r', 'r.id = stock_movements.raw_material_id', 'left')
                    ->join('units u', 'u.id = r.unit_id', 'left');
    }
}
