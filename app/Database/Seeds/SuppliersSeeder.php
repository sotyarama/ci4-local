<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuppliersSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        if ($this->db->table('suppliers')->countAllResults() > 0) {
            return;
        }

        $data = [
            [
                'name'       => 'PT Maju Jaya',
                'phone'      => '0812-3456-7890',
                'address'    => 'Jl. Kopi No. 1, Jakarta',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'CV Bahan Sejahtera',
                'phone'      => '0813-2222-3333',
                'address'    => 'Jl. Susu No. 5, Bandung',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('suppliers')->insertBatch($data);
    }
}
