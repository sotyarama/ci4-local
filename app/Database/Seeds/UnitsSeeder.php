<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UnitsSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'name'       => 'Gram',
                'short_name' => 'gr',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Mililiter',
                'short_name' => 'ml',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Pieces',
                'short_name' => 'pcs',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('units')->insertBatch($data);
    }
}
