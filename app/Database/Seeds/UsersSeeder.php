<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $db  = $this->db;

        $roles = $this->getRolesMap();

        $users = [
            [
                'username'      => 'owner',
                'password_hash' => password_hash('owner123', PASSWORD_DEFAULT),
                'full_name'     => 'Owner Cafe',
                'email'         => 'owner@example.com',
                'role_id'       => $roles['owner'] ?? null,
            ],
            [
                'username'      => 'staff1',
                'password_hash' => password_hash('staff123', PASSWORD_DEFAULT),
                'full_name'     => 'Staff Operasional 1',
                'email'         => 'staff1@example.com',
                'role_id'       => $roles['staff'] ?? null,
            ],
            [
                'username'      => 'staff2',
                'password_hash' => password_hash('staff123', PASSWORD_DEFAULT),
                'full_name'     => 'Staff Operasional 2',
                'email'         => 'staff2@example.com',
                'role_id'       => $roles['staff'] ?? null,
            ],
            [
                'username'      => 'auditor',
                'password_hash' => password_hash('auditor123', PASSWORD_DEFAULT),
                'full_name'     => 'Auditor',
                'email'         => 'auditor@example.com',
                'role_id'       => $roles['auditor'] ?? null,
            ],
        ];

        foreach ($users as $user) {
            if (! $user['role_id']) {
                continue;
            }

            if ($db->table('users')->where('username', $user['username'])->countAllResults() > 0) {
                continue;
            }

            $db->table('users')->insert([
                'username'      => $user['username'],
                'password_hash' => $user['password_hash'],
                'full_name'     => $user['full_name'],
                'email'         => $user['email'],
                'role_id'       => $user['role_id'],
                'active'        => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }

    private function getRolesMap(): array
    {
        $rows = $this->db->table('roles')->select('id, name')->get()->getResultArray();
        $map = [];
        foreach ($rows as $r) {
            $map[strtolower($r['name'])] = $r['id'];
        }
        return $map;
    }
}
