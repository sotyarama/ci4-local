<?php

namespace App\Models;

use CodeIgniter\Model;

class RawMaterialModel extends Model
{
    protected $table      = 'raw_materials';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * Timestamps
     * Pastikan tabel raw_materials punya kolom created_at & updated_at (DATETIME/TIMESTAMP).
     */
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * NOTE:
     * - created_at & updated_at TIDAK perlu ada di allowedFields saat useTimestamps=true
     *   (biar tidak bisa di-set via input).
     */
    protected $allowedFields = [
        'name',
        'unit_id',
        'current_stock',
        'min_stock',
        'cost_last',
        'cost_avg',
        'is_active',
    ];

    /**
     * Join units untuk kebutuhan list/index.
     * Output tambahan:
     * - unit_name  (units.name)
     * - unit_short (units.short_name)
     */
    public function withUnit()
    {
        return $this->select('raw_materials.*, units.name AS unit_name, units.short_name AS unit_short')
            ->join('units', 'units.id = raw_materials.unit_id', 'left');
    }
}
