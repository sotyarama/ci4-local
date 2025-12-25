<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SplitBrandVariantOnRawMaterialVariants extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $variantRows = $db->table('raw_material_variants')
            ->distinct()
            ->select('variant_name')
            ->get()
            ->getResultArray();

        foreach ($variantRows as $row) {
            $name = trim((string) ($row['variant_name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $existing = $db->table('brands')->where('name', $name)->get()->getRowArray();
            if ($existing) {
                continue;
            }

            $db->table('brands')->insert([
                'name'       => $name,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $rows = $db->table('raw_material_variants')
            ->select('id, variant_name, brand_id')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $variantId = (int) ($row['id'] ?? 0);
            $brandId   = (int) ($row['brand_id'] ?? 0);
            $variantName = trim((string) ($row['variant_name'] ?? ''));
            if ($variantId <= 0 || $brandId > 0 || $variantName === '') {
                continue;
            }

            $brand = $db->table('brands')->where('name', $variantName)->get()->getRowArray();
            if (! $brand) {
                continue;
            }

            $db->table('raw_material_variants')
                ->where('id', $variantId)
                ->update([
                    'brand_id' => (int) ($brand['id'] ?? 0),
                    'variant_name' => 'Original',
                ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $rows = $db->table('raw_material_variants')
            ->select('raw_material_variants.id, raw_material_variants.variant_name, raw_material_variants.brand_id, brands.name AS brand_name')
            ->join('brands', 'brands.id = raw_material_variants.brand_id', 'left')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $variantId = (int) ($row['id'] ?? 0);
            $variantName = (string) ($row['variant_name'] ?? '');
            $brandName = (string) ($row['brand_name'] ?? '');
            if ($variantId <= 0) {
                continue;
            }

            if ($variantName === 'Original' && $brandName !== '') {
                $db->table('raw_material_variants')
                    ->where('id', $variantId)
                    ->update([
                        'variant_name' => $brandName,
                        'brand_id' => null,
                    ]);
            }
        }
    }
}
