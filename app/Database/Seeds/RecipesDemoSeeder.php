<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RecipesDemoSeeder extends Seeder
{
    public function run()
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        // Ambil menu by SKU agar aman dari karakter khusus
        $menuBySku = [];
        $menus = $db->table('menus')->select('id, sku')->get()->getResultArray();
        foreach ($menus as $m) {
            $menuBySku[$m['sku']] = $m['id'];
        }

        $rawByName = [];
        $raws = $db->table('raw_materials')->select('id, name')->get()->getResultArray();
        foreach ($raws as $r) {
            $rawByName[$r['name']] = $r['id'];
        }

        $recipes = [
            [
                'sku'        => 'COF-ESP',
                'yield_qty'  => 1,
                'yield_unit' => 'porsi',
                'items'      => [
                    ['name' => 'Biji Kopi Arabica', 'qty' => 18, 'waste_pct' => 0, 'note' => 'Dose per shot'],
                ],
            ],
            [
                'sku'        => 'COF-LAT',
                'yield_qty'  => 1,
                'yield_unit' => 'porsi',
                'items'      => [
                    ['name' => 'Biji Kopi Arabica', 'qty' => 18,  'waste_pct' => 0,  'note' => 'Espresso shot'],
                    ['name' => 'Susu Fresh',        'qty' => 200, 'waste_pct' => 5,  'note' => 'Steamed milk'],
                    ['name' => 'Gula Pasir',        'qty' => 10,  'waste_pct' => 0,  'note' => 'Opsional'],
                ],
            ],
            [
                'sku'        => 'NC-CHOC',
                'yield_qty'  => 1,
                'yield_unit' => 'porsi',
                'items'      => [
                    ['name' => 'Bubuk Cokelat', 'qty' => 30,  'waste_pct' => 0, 'note' => ''],
                    ['name' => 'Susu Fresh',    'qty' => 150, 'waste_pct' => 5, 'note' => ''],
                    ['name' => 'Gula Pasir',    'qty' => 10,  'waste_pct' => 0, 'note' => ''],
                ],
            ],
            [
                'sku'        => 'SN-FR',
                'yield_qty'  => 1,
                'yield_unit' => 'porsi',
                'items'      => [
                    ['name' => 'Kentang',       'qty' => 200, 'waste_pct' => 5, 'note' => 'Potato weight'],
                    ['name' => 'Minyak Goreng', 'qty' => 20,  'waste_pct' => 10,'note' => 'Absorbed oil'],
                ],
            ],
        ];

        foreach ($recipes as $rec) {
            $menuId = $menuBySku[$rec['sku']] ?? null;
            if (! $menuId) {
                continue;
            }

            // Upsert sederhana: jika sudah ada, skip
            $existing = $db->table('recipes')->where('menu_id', $menuId)->get()->getRowArray();
            if ($existing) {
                continue;
            }

            $recipeId = $db->table('recipes')->insert([
                'menu_id'    => $menuId,
                'yield_qty'  => $rec['yield_qty'],
                'yield_unit' => $rec['yield_unit'],
                'notes'      => null,
                'created_at' => $now,
                'updated_at' => $now,
            ], true);

            $itemsInsert = [];
            foreach ($rec['items'] as $it) {
                $rawId = $rawByName[$it['name']] ?? null;
                if (! $rawId) {
                    continue;
                }
                $itemsInsert[] = [
                    'recipe_id'       => $recipeId,
                    'raw_material_id' => $rawId,
                    'qty'             => $it['qty'],
                    'waste_pct'       => $it['waste_pct'],
                    'note'            => $it['note'],
                ];
            }

            if (! empty($itemsInsert)) {
                $db->table('recipe_items')->insertBatch($itemsInsert);
            }
        }
    }
}
