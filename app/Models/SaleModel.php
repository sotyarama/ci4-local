<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table         = 'sales';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';

    protected $allowedFields = [
        'sale_date',
        'invoice_no',
        'customer_name',
        'total_amount',
        'total_cost',
        'notes',
    ];

    protected $useTimestamps = true; // pakai created_at & updated_at

    /**
     * Helper untuk ambil list sales dengan optional filter tanggal.
     *
     * @param string|null $dateFrom format Y-m-d
     * @param string|null $dateTo   format Y-m-d
     * @return $this
     */
    public function filterByDateRange(?string $dateFrom, ?string $dateTo)
    {
        if ($dateFrom) {
            $this->where('sale_date >=', $dateFrom);
        }

        if ($dateTo) {
            $this->where('sale_date <=', $dateTo);
        }

        return $this;
    }
}
