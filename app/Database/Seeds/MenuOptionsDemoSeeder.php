<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuOptionsDemoSeeder extends Seeder
{
    public function run()
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        $unitGr  = $db->table('units')->where('short_name', 'gr')->get()->getRow('id');
        $unitMl  = $db->table('units')->where('short_name', 'ml')->get()->getRow('id');
        $unitPcs = $db->table('units')->where('short_name', 'pcs')->get()->getRow('id');

        $categorySnack = $db->table('menu_categories')->where('name', 'Snack')->get()->getRow('id');
        $categoryCoffee = $db->table('menu_categories')->where('name', 'Coffee')->get()->getRow('id');

        if (! $categorySnack) {
            $categorySnack = $db->table('menu_categories')->insert([
                'name'        => 'Snack',
                'description' => 'Makanan ringan',
                'sort_order'  => 3,
                'created_at'  => $now,
                'updated_at'  => $now,
            ], true);
        }

        if (! $categoryCoffee) {
            $categoryCoffee = $db->table('menu_categories')->insert([
                'name'        => 'Coffee',
                'description' => 'Menu berbasis kopi',
                'sort_order'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ], true);
        }

        $menus = [
            [
                'name'             => 'Mie Goreng',
                'menu_category_id' => $categorySnack,
                'sku'              => 'FD-MG',
                'price'            => 16000,
            ],
            [
                'name'             => 'Mie Kuah',
                'menu_category_id' => $categorySnack,
                'sku'              => 'FD-MK',
                'price'            => 16000,
            ],
            [
                'name'             => 'Kopi Panas',
                'menu_category_id' => $categoryCoffee,
                'sku'              => 'COF-KP',
                'price'            => 12000,
            ],
        ];

        $menuIds = [];
        foreach ($menus as $menu) {
            $existing = $db->table('menus')->where('name', $menu['name'])->get()->getRowArray();
            if ($existing) {
                $menuIds[$menu['name']] = (int) $existing['id'];
                continue;
            }

            $menuIds[$menu['name']] = $db->table('menus')->insert([
                'name'             => $menu['name'],
                'menu_category_id' => $menu['menu_category_id'],
                'sku'              => $menu['sku'],
                'price'            => $menu['price'],
                'is_active'        => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ], true);
        }

        $rawSeed = [
            'Mie Instan'     => ['unit' => $unitPcs, 'has_variants' => 1],
            'Telur'          => ['unit' => $unitPcs, 'has_variants' => 0],
            'Sosis'          => ['unit' => $unitPcs, 'has_variants' => 0],
            'Bakso'          => ['unit' => $unitPcs, 'has_variants' => 0],
            'Kopi Sachet'    => ['unit' => $unitPcs, 'has_variants' => 1],
            'Bumbu Mie'      => ['unit' => $unitGr, 'has_variants' => 0],
            'Air'            => ['unit' => $unitMl, 'has_variants' => 0],
            'Minyak Goreng'  => ['unit' => $unitMl, 'has_variants' => 0],
        ];

        $rawByName = [];
        foreach ($rawSeed as $name => $info) {
            $existing = $db->table('raw_materials')->where('name', $name)->get()->getRowArray();
            if ($existing) {
                $rawByName[$name] = (int) $existing['id'];
                $expected = (int) ($info['has_variants'] ?? 0);
                if ((int) ($existing['has_variants'] ?? 0) !== $expected) {
                    $db->table('raw_materials')
                        ->where('id', $existing['id'])
                        ->update(['has_variants' => $expected]);
                }
                continue;
            }

            $rawByName[$name] = $db->table('raw_materials')->insert([
                'name'          => $name,
                'unit_id'       => $info['unit'],
                'has_variants'  => (int) ($info['has_variants'] ?? 0),
                'current_stock' => 0,
                'min_stock'     => 0,
                'cost_last'     => 0,
                'cost_avg'      => 0,
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ], true);
        }

        $brandNames = [
            'Indomie',
            'Mie Sedaap',
            'Kapal Api',
            'ABC',
        ];
        $brandIds = [];

        foreach ($brandNames as $brandName) {
            $existing = $db->table('brands')->where('name', $brandName)->get()->getRowArray();
            if ($existing) {
                $brandIds[$brandName] = (int) $existing['id'];
                continue;
            }

            $brandIds[$brandName] = $db->table('brands')->insert([
                'name'       => $brandName,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], true);
        }

        $variantMap = [];
        $variantDefs = [
            'Indomie Goreng' => [
                'raw'     => 'Mie Instan',
                'brand'   => 'Indomie',
                'variant' => 'Goreng',
            ],
            'Mie Sedaap Goreng' => [
                'raw'     => 'Mie Instan',
                'brand'   => 'Mie Sedaap',
                'variant' => 'Goreng',
            ],
            'Telur' => [
                'raw'     => 'Telur',
                'brand'   => null,
                'variant' => 'Telur',
            ],
            'Sosis' => [
                'raw'     => 'Sosis',
                'brand'   => null,
                'variant' => 'Sosis',
            ],
            'Bakso' => [
                'raw'     => 'Bakso',
                'brand'   => null,
                'variant' => 'Bakso',
            ],
            'Kopi Kapal Api' => [
                'raw'     => 'Kopi Sachet',
                'brand'   => 'Kapal Api',
                'variant' => 'Original',
            ],
            'Kopi ABC' => [
                'raw'     => 'Kopi Sachet',
                'brand'   => 'ABC',
                'variant' => 'Original',
            ],
        ];

        foreach ($variantDefs as $label => $def) {
            $rawId = $rawByName[$def['raw']] ?? null;
            if (! $rawId) {
                continue;
            }

            $brandName = $def['brand'];
            $brandId = $brandName ? ($brandIds[$brandName] ?? null) : null;

            $existingBuilder = $db->table('raw_material_variants')
                ->where('raw_material_id', $rawId)
                ->where('variant_name', $def['variant']);
            if ($brandId === null) {
                $existingBuilder->where('brand_id', null);
            } else {
                $existingBuilder->where('brand_id', $brandId);
            }

            $existing = $existingBuilder->get()->getRowArray();
            if ($existing) {
                $variantMap[$label] = (int) $existing['id'];
                continue;
            }

            $variantMap[$label] = $db->table('raw_material_variants')->insert([
                'raw_material_id' => $rawId,
                'brand_id'        => $brandId,
                'variant_name'    => $def['variant'],
                'sku_code'        => null,
                'current_stock'   => 0,
                'min_stock'       => 0,
                'is_active'       => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ], true);
        }

        $groupDefs = [
            'Mie Goreng' => [
                [
                    'name'        => 'Pilih Mie',
                    'is_required' => 1,
                    'min_select'  => 1,
                    'max_select'  => 1,
                    'sort_order'  => 1,
                ],
                [
                    'name'        => 'Tambah Topping',
                    'is_required' => 0,
                    'min_select'  => 0,
                    'max_select'  => 3,
                    'sort_order'  => 2,
                ],
            ],
            'Mie Kuah' => [
                [
                    'name'        => 'Pilih Mie',
                    'is_required' => 1,
                    'min_select'  => 1,
                    'max_select'  => 1,
                    'sort_order'  => 1,
                ],
                [
                    'name'        => 'Tambah Topping',
                    'is_required' => 0,
                    'min_select'  => 0,
                    'max_select'  => 3,
                    'sort_order'  => 2,
                ],
            ],
            'Kopi Panas' => [
                [
                    'name'        => 'Pilih Kopi Sachet',
                    'is_required' => 1,
                    'min_select'  => 1,
                    'max_select'  => 1,
                    'sort_order'  => 1,
                ],
            ],
        ];

        $groupIds = [];
        foreach ($groupDefs as $menuName => $groups) {
            $menuId = $menuIds[$menuName] ?? null;
            if (! $menuId) {
                continue;
            }
            foreach ($groups as $group) {
                $existing = $db->table('menu_option_groups')
                    ->where('menu_id', $menuId)
                    ->where('name', $group['name'])
                    ->get()
                    ->getRowArray();

                if ($existing) {
                    $groupIds[$menuName][$group['name']] = (int) $existing['id'];
                    continue;
                }

                $groupIds[$menuName][$group['name']] = $db->table('menu_option_groups')->insert([
                    'menu_id'                => $menuId,
                    'name'                   => $group['name'],
                    'is_required'            => $group['is_required'],
                    'min_select'             => $group['min_select'],
                    'max_select'             => $group['max_select'],
                    'sort_order'             => $group['sort_order'],
                    'show_on_kitchen_ticket' => 1,
                    'is_active'              => 1,
                    'created_at'             => $now,
                    'updated_at'             => $now,
                ], true);
            }
        }

        $optionDefs = [
            'Mie Goreng' => [
                'Pilih Mie' => [
                    ['name' => 'Indomie Goreng', 'price' => 0],
                    ['name' => 'Mie Sedaap Goreng', 'price' => 0],
                ],
                'Tambah Topping' => [
                    ['name' => 'Telur', 'price' => 3000],
                    ['name' => 'Sosis', 'price' => 4000],
                    ['name' => 'Bakso', 'price' => 4000],
                ],
            ],
            'Mie Kuah' => [
                'Pilih Mie' => [
                    ['name' => 'Indomie Goreng', 'price' => 0],
                    ['name' => 'Mie Sedaap Goreng', 'price' => 0],
                ],
                'Tambah Topping' => [
                    ['name' => 'Telur', 'price' => 3000],
                    ['name' => 'Sosis', 'price' => 4000],
                    ['name' => 'Bakso', 'price' => 4000],
                ],
            ],
            'Kopi Panas' => [
                'Pilih Kopi Sachet' => [
                    ['name' => 'Kopi Kapal Api', 'price' => 0],
                    ['name' => 'Kopi ABC', 'price' => 0],
                ],
            ],
        ];

        foreach ($optionDefs as $menuName => $groups) {
            foreach ($groups as $groupName => $options) {
                $groupId = $groupIds[$menuName][$groupName] ?? null;
                if (! $groupId) {
                    continue;
                }

                foreach ($options as $opt) {
                    $variantId = $variantMap[$opt['name']] ?? null;
                    if (! $variantId) {
                        continue;
                    }

                    $existing = $db->table('menu_options')
                        ->where('group_id', $groupId)
                        ->where('name', $opt['name'])
                        ->get()
                        ->getRowArray();

                    if ($existing) {
                        continue;
                    }

                    $db->table('menu_options')->insert([
                        'group_id'      => $groupId,
                        'name'          => $opt['name'],
                        'price_delta'   => $opt['price'],
                        'variant_id'    => $variantId,
                        'qty_multiplier'=> 1,
                        'sort_order'    => 0,
                        'is_active'     => 1,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);
                }
            }
        }

        $recipes = [
            'Mie Goreng' => [
                ['name' => 'Bumbu Mie', 'qty' => 20, 'waste_pct' => 0, 'note' => 'Bumbu dasar'],
                ['name' => 'Minyak Goreng', 'qty' => 10, 'waste_pct' => 0, 'note' => 'Untuk goreng'],
            ],
            'Mie Kuah' => [
                ['name' => 'Bumbu Mie', 'qty' => 15, 'waste_pct' => 0, 'note' => 'Bumbu dasar'],
                ['name' => 'Air', 'qty' => 250, 'waste_pct' => 0, 'note' => 'Kuah'],
            ],
            'Kopi Panas' => [
                ['name' => 'Air', 'qty' => 200, 'waste_pct' => 0, 'note' => 'Air panas'],
            ],
        ];

        foreach ($recipes as $menuName => $items) {
            $menuId = $menuIds[$menuName] ?? null;
            if (! $menuId) {
                continue;
            }

            $existing = $db->table('recipes')->where('menu_id', $menuId)->get()->getRowArray();
            if ($existing) {
                continue;
            }

            $recipeId = $db->table('recipes')->insert([
                'menu_id'    => $menuId,
                'yield_qty'  => 1,
                'yield_unit' => 'porsi',
                'notes'      => null,
                'created_at' => $now,
                'updated_at' => $now,
            ], true);

            $itemsInsert = [];
            foreach ($items as $item) {
                $rawId = $rawByName[$item['name']] ?? null;
                if (! $rawId) {
                    continue;
                }
                $itemsInsert[] = [
                    'recipe_id'       => $recipeId,
                    'raw_material_id' => $rawId,
                    'qty'             => $item['qty'],
                    'waste_pct'       => $item['waste_pct'],
                    'note'            => $item['note'],
                ];
            }

            if (! empty($itemsInsert)) {
                $db->table('recipe_items')->insertBatch($itemsInsert);
            }
        }
    }
}
