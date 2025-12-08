<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        if ($this->db->table('roles')->countAllResults() > 0) {
            return;
        }

        $data = [
            [
                'name'        => 'owner',
                'description' => 'Pemilik cafe, akses penuh',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'staff',
                'description' => 'Staff operasional (kasir, pembelian, stok)',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'auditor',
                'description' => 'Auditor, akses baca/report saja',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
