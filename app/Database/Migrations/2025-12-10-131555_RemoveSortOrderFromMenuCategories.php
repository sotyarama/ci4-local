<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveSortOrderFromMenuCategories extends Migration
{
    public function up()
    {
        // Drop kolom sort_order (tidak dipakai lagi)
        if ($this->db->fieldExists('sort_order', 'menu_categories')) {
            $this->forge->dropColumn('menu_categories', 'sort_order');
        }
    }

    public function down()
    {
        // Restore kolom sort_order jika dibutuhkan rollback
        $this->forge->addColumn('menu_categories', [
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'description',
            ],
        ]);
    }
}
