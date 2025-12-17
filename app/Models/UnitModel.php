<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitModel extends Model
{
    protected $table         = 'units';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'name',
        'short_name',
        'is_active',   // NEW
        'created_at',
        'updated_at',
    ];

    /**
     * Untuk dropdown (default: hanya yang aktif).
     * Kalau suatu saat butuh semua unit (termasuk nonaktif), set $onlyActive=false.
     */
    public function getForDropdown(bool $onlyActive = true): array
    {
        $qb = $this->orderBy('name', 'ASC');

        if ($onlyActive) {
            $qb->where('is_active', 1);
        }

        return $qb->findAll();
    }
}
