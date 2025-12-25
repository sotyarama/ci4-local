<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'name',
        'description',
        'created_at',
        'updated_at',
    ];

    public function getForDropdown(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }
}
