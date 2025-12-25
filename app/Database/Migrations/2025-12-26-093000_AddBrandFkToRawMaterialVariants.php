<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBrandFkToRawMaterialVariants extends Migration
{
    public function up()
    {
        $this->db->query('CREATE INDEX idx_raw_material_variants_brand ON raw_material_variants (brand_id)');
        $this->db->query('ALTER TABLE raw_material_variants ADD CONSTRAINT fk_raw_material_variants_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON UPDATE CASCADE ON DELETE SET NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE raw_material_variants DROP FOREIGN KEY fk_raw_material_variants_brand');
        $this->db->query('DROP INDEX idx_raw_material_variants_brand ON raw_material_variants');
    }
}
