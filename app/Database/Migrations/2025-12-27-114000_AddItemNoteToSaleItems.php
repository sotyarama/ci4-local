<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddItemNoteToSaleItems extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sale_items', [
            'item_note' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'hpp_snapshot',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sale_items', 'item_note');
    }
}
