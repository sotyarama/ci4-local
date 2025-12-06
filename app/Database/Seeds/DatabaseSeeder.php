<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Urutan penting: roles & master dulu
        $this->call('RolesSeeder');
        $this->call('UnitsSeeder');
        $this->call('MenuCategoriesSeeder');
        $this->call('MenusSeeder');
        $this->call('UsersSeeder');
    }
}
