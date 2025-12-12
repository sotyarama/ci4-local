<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubrecipeSupport extends Migration
{
    public function up()
    {
        // Allow recipe_items to reference either raw materials or child recipes.
        $this->forge->modifyColumn('recipe_items', [
            'raw_material_id' => [
                'name'       => 'raw_material_id',
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true, // nullable karena bisa pakai sub-recipe
            ],
        ]);

        $this->forge->addColumn('recipe_items', [
            'item_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'raw',
                'after'      => 'recipe_id',
            ],
            'child_recipe_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'raw_material_id',
            ],
        ]);

        // Indexes to speed lookups on child recipes.
        $this->db->query('CREATE INDEX idx_recipe_items_child_recipe ON recipe_items (child_recipe_id)');
        $this->db->query('CREATE INDEX idx_recipe_items_item_type ON recipe_items (item_type)');
    }

    public function down()
    {
        // Drop new columns
        $this->forge->dropColumn('recipe_items', ['item_type', 'child_recipe_id']);

        // Revert raw_material_id to NOT NULL (original definition)
        $this->forge->modifyColumn('recipe_items', [
            'raw_material_id' => [
                'name'       => 'raw_material_id',
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}
