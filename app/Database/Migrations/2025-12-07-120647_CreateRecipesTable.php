<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRecipesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'menu_id'    => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'yield_qty'  => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'default'    => 1,  // berapa porsi / cup yang dihasilkan resep ini
            ],
            'yield_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true, // contoh: "cup", "porsi"
            ],
            'notes'      => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addUniqueKey('menu_id'); // 1 menu = 1 resep utama
        $this->forge->addForeignKey('menu_id', 'menus', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('recipes', true);
    }

    public function down()
    {
        $this->forge->dropTable('recipes', true);
    }
}
