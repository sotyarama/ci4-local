<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToSalesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sales', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'completed',
                'after'      => 'notes',
            ],
            'void_reason' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'status',
            ],
            'voided_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'void_reason',
            ],
        ]);

    }

    public function down()
    {
        $this->forge->dropColumn('sales', ['status', 'void_reason', 'voided_at']);
    }
}
