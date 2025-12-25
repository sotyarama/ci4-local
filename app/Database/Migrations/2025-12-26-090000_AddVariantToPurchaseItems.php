<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVariantToPurchaseItems extends Migration
{
    public function up()
    {
        $this->forge->addColumn('purchase_items', [
            'raw_material_variant_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'raw_material_id',
            ],
        ]);

        $this->db->query('CREATE INDEX idx_purchase_items_variant ON purchase_items (raw_material_variant_id)');
        $this->db->query('ALTER TABLE purchase_items ADD CONSTRAINT fk_purchase_items_variant FOREIGN KEY (raw_material_variant_id) REFERENCES raw_material_variants(id) ON UPDATE CASCADE ON DELETE SET NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE purchase_items DROP FOREIGN KEY fk_purchase_items_variant');
        $this->db->query('DROP INDEX idx_purchase_items_variant ON purchase_items');
        $this->forge->dropColumn('purchase_items', ['raw_material_variant_id']);
    }
}
