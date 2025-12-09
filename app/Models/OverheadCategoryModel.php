<?php

namespace App\Models;

use CodeIgniter\Model;

class OverheadCategoryModel extends Model
{
    protected $table         = 'overhead_categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'name',
        'is_active',
        'created_at',
        'updated_at',
    ];
}
