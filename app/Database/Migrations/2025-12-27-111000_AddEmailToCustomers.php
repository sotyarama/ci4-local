<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailToCustomers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('customers', [
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'phone',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('customers', 'email');
    }
}
