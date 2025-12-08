<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuCategoriesSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        if ($this->db->table('menu_categories')->countAllResults() > 0) {
            return;
        }

        $data = [
            [
                'name'        => 'Coffee',
                'description' => 'Menu berbasis kopi',
                'sort_order'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Non-Coffee',
                'description' => 'Minuman tanpa kopi',
                'sort_order'  => 2,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Snack',
                'description' => 'Makanan ringan',
                'sort_order'  => 3,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        $this->db->table('menu_categories')->insertBatch($data);
    }
}
