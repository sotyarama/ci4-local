<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRawMaterialsTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'current_stock' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,3',
                'default'    => 0,
            ],
            'min_stock' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,3',
                'default'    => 0,
            ],
            'cost_last' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'cost_avg' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('name');
        //$this->forge->addForeignKey('unit_id', 'units', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('raw_materials', true);
    }

    public function down()
    {
        $this->forge->dropTable('raw_materials', true);
    }
}
