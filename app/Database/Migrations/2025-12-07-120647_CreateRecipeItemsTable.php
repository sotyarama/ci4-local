<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRecipeItemsTable extends Migration
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
        'recipe_id' => [
            'type'       => 'INT',
            'constraint' => 10,
            'unsigned'   => true,
        ],
        'raw_material_id' => [
            'type'       => 'INT',
            'constraint' => 10,
            'unsigned'   => true,
        ],
        'qty' => [
            'type'       => 'DECIMAL',
            'constraint' => '15,3',
            'default'    => 0,
        ],
        'waste_pct' => [
            'type'       => 'DECIMAL',
            'constraint' => '5,2',
            'default'    => 0,
        ],
        'note' => [
            'type'       => 'VARCHAR',
            'constraint' => 255,
            'null'       => true,
        ],
    ]);

    $this->forge->addKey('id', true);
    $this->forge->addKey('recipe_id');
    $this->forge->addKey('raw_material_id');

    // 👉 Sementara TANPA foreign key dulu supaya tidak kena errno 150.
    // Nanti kalau sudah stabil, kita bisa buat migration baru khusus ALTER TABLE
    // untuk tambah FK ke recipes(id) dan raw_materials(id).

    $this->forge->createTable('recipe_items', true);
}


    public function down()
    {
        $this->forge->dropTable('recipe_items', true);
    }
}
