<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseModel extends Model
{
    protected $table         = 'purchases';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'supplier_id',
        'purchase_date',
        'invoice_no',
        'total_amount',
        'notes',
        'created_at',
        'updated_at',
    ];
}
