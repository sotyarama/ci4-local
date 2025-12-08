<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RawMaterialsSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $db  = $this->db;

        // Hindari duplikasi jika sudah pernah seed
        $already = $db->table('raw_materials')->countAllResults();
        if ($already > 0) {
            return;
        }

        $unitGr = $db->table('units')->where('short_name', 'gr')->get()->getRow('id');
        $unitMl = $db->table('units')->where('short_name', 'ml')->get()->getRow('id');
        $unitPcs = $db->table('units')->where('short_name', 'pcs')->get()->getRow('id');

        $data = [
            [
                'name'           => 'Biji Kopi Arabica',
                'unit_id'        => $unitGr,
                'current_stock'  => 0,
                'min_stock'      => 500,
                'cost_last'      => 0,
                'cost_avg'       => 0,
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'Susu Fresh',
                'unit_id'        => $unitMl,
                'current_stock'  => 0,
                'min_stock'      => 2000,
                'cost_last'      => 0,
                'cost_avg'       => 0,
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'Bubuk Cokelat',
                'unit_id'        => $unitGr,
                'current_stock'  => 0,
                'min_stock'      => 500,
                'cost_last'      => 0,
                'cost_avg'       => 0,
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'Gula Pasir',
                'unit_id'        => $unitGr,
                'current_stock'  => 0,
                'min_stock'      => 500,
                'cost_last'      => 0,
                'cost_avg'       => 0,
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'Kentang',
                'unit_id'        => $unitGr,
                'current_stock'  => 0,
                'min_stock'      => 1000,
                'cost_last'      => 0,
                'cost_avg'       => 0,
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'Minyak Goreng',
                'unit_id'        => $unitMl,
                'current_stock'  => 0,
                'min_stock'      => 2000,
                'cost_last'      => 0,
                'cost_avg'       => 0,
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ];

        $db->table('raw_materials')->insertBatch($data);
    }
}
