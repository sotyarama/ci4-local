<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table         = 'stock_movements';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = false; // table hanya punya created_at (diisi manual)

    protected $allowedFields = [
        'raw_material_id',
        'raw_material_variant_id',
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
        return $this->select('stock_movements.*, r.name AS material_name, r.qty_precision AS qty_precision, u.short_name AS unit_short, rmv.variant_name, b.name AS brand_name')
                    ->join('raw_materials r', 'r.id = stock_movements.raw_material_id', 'left')
                    ->join('units u', 'u.id = r.unit_id', 'left')
                    ->join('raw_material_variants rmv', 'rmv.id = stock_movements.raw_material_variant_id', 'left')
                    ->join('brands b', 'b.id = rmv.brand_id', 'left');
    }
}
