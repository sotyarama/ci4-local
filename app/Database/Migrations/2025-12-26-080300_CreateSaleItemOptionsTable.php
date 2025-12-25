<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSaleItemOptionsTable extends Migration
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
            'sale_item_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'option_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'qty_selected' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
                'default'    => 1.0000,
            ],
            'option_name_snapshot' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'price_delta_snapshot' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'variant_id_snapshot' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey('sale_item_id');
        $this->forge->addKey('option_id');
        $this->forge->addKey('variant_id_snapshot');
        $this->forge->addForeignKey('sale_item_id', 'sale_items', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('option_id', 'menu_options', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('variant_id_snapshot', 'raw_material_variants', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('sale_item_options', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('sale_item_options', true);
    }
}
