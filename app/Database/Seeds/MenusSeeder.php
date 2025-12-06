<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenusSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $db = $this->db;

        $coffeeId     = $db->table('menu_categories')->where('name', 'Coffee')->get()->getRow('id');
        $nonCoffeeId  = $db->table('menu_categories')->where('name', 'Non-Coffee')->get()->getRow('id');
        $snackId      = $db->table('menu_categories')->where('name', 'Snack')->get()->getRow('id');

        $data = [
            [
                'name'             => 'Espresso',
                'menu_category_id' => $coffeeId,
                'sku'              => 'COF-ESP',
                'price'            => 18000,
                'is_active'        => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'name'             => 'Caffè Latte',
                'menu_category_id' => $coffeeId,
                'sku'              => 'COF-LAT',
                'price'            => 25000,
                'is_active'        => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'name'             => 'Iced Chocolate',
                'menu_category_id' => $nonCoffeeId,
                'sku'              => 'NC-CHOC',
                'price'            => 26000,
                'is_active'        => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'name'             => 'French Fries',
                'menu_category_id' => $snackId,
                'sku'              => 'SN-FR',
                'price'            => 22000,
                'is_active'        => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
        ];

        $db->table('menus')->insertBatch($data);
    }
}
