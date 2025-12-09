<?php

namespace App\Models;

use CodeIgniter\Model;

class OverheadModel extends Model
{
    protected $table         = 'overheads';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'trans_date',
        'category_id',
        'category',
        'description',
        'amount',
        'created_at',
        'updated_at',
    ];
}
