<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollModel extends Model
{
    protected $table         = 'payrolls';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'user_id',
        'period_month',
        'pay_date',
        'amount',
        'notes',
        'created_at',
        'updated_at',
    ];

    public function withUser()
    {
        return $this->select('payrolls.*, u.full_name AS staff_name, u.username AS staff_username')
            ->join('users u', 'u.id = payrolls.user_id', 'left');
    }
}
