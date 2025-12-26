<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table         = 'customers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';

    protected $allowedFields = [
        'name',
        'phone',
        'email',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveForDropdown(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getDefaultCustomer(): array
    {
        $customer = $this->where('name', 'Tamu')->orderBy('id', 'ASC')->first();
        if ($customer) {
            return $customer;
        }

        $id = $this->insert([
            'name'      => 'Tamu',
            'phone'     => null,
            'is_active' => 1,
        ], true);

        return $this->find($id) ?? [
            'id' => (int) $id,
            'name' => 'Tamu',
            'phone' => null,
            'is_active' => 1,
        ];
    }
}
