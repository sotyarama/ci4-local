<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVariantStockAndFlags extends Migration
{
    public function up()
    {
        // raw_materials: flag + optional brand
        $this->forge->addColumn('raw_materials', [
            'has_variants' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'brand_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        $this->db->query('CREATE INDEX idx_raw_materials_brand ON raw_materials (brand_id)');
        $this->db->query('ALTER TABLE raw_materials ADD CONSTRAINT fk_raw_materials_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON UPDATE CASCADE ON DELETE SET NULL');

        // raw_material_variants: stock per varian
        $this->forge->addColumn('raw_material_variants', [
            'current_stock' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,3',
                'default'    => 0,
            ],
            'min_stock' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,3',
                'default'    => 0,
            ],
        ]);

        // stock_movements: link to variant (optional)
        $this->forge->addColumn('stock_movements', [
            'raw_material_variant_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        $this->db->query('CREATE INDEX idx_stock_movements_variant ON stock_movements (raw_material_variant_id)');
        $this->db->query('ALTER TABLE stock_movements ADD CONSTRAINT fk_stock_movements_variant FOREIGN KEY (raw_material_variant_id) REFERENCES raw_material_variants(id) ON UPDATE CASCADE ON DELETE SET NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE stock_movements DROP FOREIGN KEY fk_stock_movements_variant');
        $this->db->query('DROP INDEX idx_stock_movements_variant ON stock_movements');
        $this->forge->dropColumn('stock_movements', 'raw_material_variant_id');

        $this->forge->dropColumn('raw_material_variants', ['current_stock', 'min_stock']);

        $this->db->query('ALTER TABLE raw_materials DROP FOREIGN KEY fk_raw_materials_brand');
        $this->db->query('DROP INDEX idx_raw_materials_brand ON raw_materials');
        $this->forge->dropColumn('raw_materials', ['has_variants', 'brand_id']);
    }
}
