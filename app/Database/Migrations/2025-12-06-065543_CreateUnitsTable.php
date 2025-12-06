<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUnitsTable extends Migration
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
                'constraint' => 50, // contoh: Gram, Mililiter, Pcs
            ],
            'short_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 10, // contoh: gr, ml, pcs
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
        $this->forge->addUniqueKey('short_name');
        $this->forge->createTable('units', true);
    }

    public function down()
    {
        $this->forge->dropTable('units', true);
    }
}
