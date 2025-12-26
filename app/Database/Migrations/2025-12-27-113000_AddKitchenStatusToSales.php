<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKitchenStatusToSales extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sales', [
            'kitchen_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'open',
                'after'      => 'change_amount',
            ],
            'kitchen_done_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'kitchen_status',
            ],
        ]);

        // Set record lama sebagai done agar tidak memenuhi antrian.
        $this->db->query("UPDATE sales SET kitchen_status = 'done', kitchen_done_at = COALESCE(kitchen_done_at, created_at, NOW())");
    }

    public function down()
    {
        $this->forge->dropColumn('sales', ['kitchen_status', 'kitchen_done_at']);
    }
}
