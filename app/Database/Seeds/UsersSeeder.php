<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $db  = $this->db;

        // Ambil role owner
        $ownerRoleId = $db->table('roles')->where('name', 'owner')->get()->getRow('id');

        $data = [
            'username'      => 'owner',
            'password_hash' => password_hash('owner123', PASSWORD_DEFAULT),
            'full_name'     => 'Owner Cafe',
            'email'         => 'owner@example.com',
            'role_id'       => $ownerRoleId,
            'active'        => 1,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        $db->table('users')->insert($data);
    }
}
