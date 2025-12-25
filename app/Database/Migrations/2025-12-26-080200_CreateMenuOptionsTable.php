<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenuOptionsTable extends Migration
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
            'group_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'price_delta' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'variant_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'qty_multiplier' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
                'default'    => 1.0000,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
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
        $this->forge->addKey('group_id');
        $this->forge->addKey('variant_id');
        $this->forge->addForeignKey('group_id', 'menu_option_groups', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('variant_id', 'raw_material_variants', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('menu_options', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('menu_options', true);
    }
}
