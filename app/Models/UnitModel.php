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
        'created_at',
        'updated_at',
    ];

    public function getForDropdown(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }
}
