<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQtyPrecisionToRawMaterials extends Migration
{
    public function up()
    {
        $this->forge->addColumn('raw_materials', [
            'qty_precision' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'unit_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('raw_materials', 'qty_precision');
    }
}
