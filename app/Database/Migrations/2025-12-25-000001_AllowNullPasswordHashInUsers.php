<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AllowNullPasswordHashInUsers extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('users', [
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('users', [
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ]);
    }
}
