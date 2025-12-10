<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuCategoryModel extends Model
{
    protected $table         = 'menu_categories';
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
        return $this->orderBy('name', 'ASC')
                    ->findAll();
    }
}
