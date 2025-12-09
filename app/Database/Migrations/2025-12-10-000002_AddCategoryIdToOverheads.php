<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryIdToOverheads extends Migration
{
    public function up()
    {
        $fields = [
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'trans_date',
            ],
        ];

        $this->forge->addColumn('overheads', $fields);

        // Optional: index for faster lookup
        $this->db->query('CREATE INDEX idx_overheads_category_id ON overheads (category_id)');
    }

    public function down()
    {
        $this->forge->dropColumn('overheads', 'category_id');
    }
}
