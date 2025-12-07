<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockMovementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'raw_material_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'movement_type' => [
                'type'       => 'ENUM',
                'constraint' => ['IN', 'OUT'],
            ],
            'qty' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,3',
                'default'    => 0,
            ],
            'ref_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50, // contoh: 'purchase', 'sale', 'adjustment'
            ],
            'ref_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'note' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('raw_material_id');
        $this->forge->addKey(['ref_type', 'ref_id']);
        $this->forge->createTable('stock_movements', true);
    }

    public function down()
    {
        $this->forge->dropTable('stock_movements', true);
    }
}
