<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\MenuModel;
use App\Models\RawMaterialModel;
use App\Models\RecipeItemModel;
use App\Models\RecipeModel;

class Recipes extends BaseController
{
    protected MenuModel $menuModel;
    protected RecipeModel $recipeModel;
    protected RecipeItemModel $itemModel;
    protected RawMaterialModel $rawModel;
    protected AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->menuModel     = new MenuModel();
        $this->recipeModel   = new RecipeModel();
        $this->itemModel     = new RecipeItemModel();
        $this->rawModel      = new RawMaterialModel();
        $this->auditLogModel = new AuditLogModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('menus m')
            ->select('m.*, c.name AS category_name, r.id AS recipe_id')
            ->join('menu_categories c', 'c.id = m.menu_category_id', 'left')
            ->join('recipes r', 'r.menu_id = m.id', 'left')
            ->orderBy('c.name', 'ASC')
            ->orderBy('m.name', 'ASC');

        $menus = $builder->get()->getResultArray();

        // Tambahkan info HPP per menu (jika sudah ada resep)
        foreach ($menus as &$menu) {
            if (! empty($menu['recipe_id'])) {
                $hpp = $this->recipeModel->calculateHppForMenu((int) $menu['id']);
                if ($hpp !== null) {
                    $menu['hpp_per_yield'] = $hpp['hpp_per_yield'] ?? 0;
                    $menu['yield_unit']    = $hpp['recipe']['yield_unit'] ?? 'porsi';
                } else {
                    $menu['hpp_per_yield'] = null;
                    $menu['yield_unit']    = null;
                }
            } else {
                $menu['hpp_per_yield'] = null;
                $menu['yield_unit']    = null;
            }
        }
        unset($menu); // safety

        $totalMenu = $this->menuModel->countAllResults();
        $menuWithRecipe = $this->recipeModel
            ->select('menu_id')
            ->distinct()
            ->countAllResults();
        $canCreateRecipe = $totalMenu > $menuWithRecipe;

        $data = [
            'title'    => 'Master Resep Menu',
            'subtitle' => 'Mapping menu ke bahan baku (BOM)',
            'menus'    => $menus,
            'canCreateRecipe' => $canCreateRecipe,
        ];

        return view('master/recipes_index', $data);
    }

    public function create()
    {
        $totalMenu = $this->menuModel->countAllResults();
        $menuWithRecipe = $this->recipeModel
            ->select('menu_id')
            ->distinct()
            ->countAllResults();
        $canCreateRecipe = $totalMenu > $menuWithRecipe;

        if (! $canCreateRecipe) {
            return redirect()->to(site_url('master/recipes'))
                ->with('error', 'Semua menu sudah memiliki resep.');
        }

        // Hanya tampilkan menu yang belum punya resep
        $db = \Config\Database::connect();
        $menus = $db->table('menus m')
            ->select('m.*')
            ->join('recipes r', 'r.menu_id = m.id', 'left')
            ->where('r.id IS NULL', null, false)
            ->orderBy('m.name', 'ASC')
            ->get()->getResultArray();

        $materials = $this->rawModel
            ->withUnit()
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $recipes = $this->getRecipeOptions();

        $data = [
            'title'     => 'Tambah Resep Menu',
            'subtitle'  => 'Definisikan komposisi bahan baku per menu',
            'mode'      => 'create',
            'menus'     => $menus,
            'materials' => $materials,
            'recipes'   => $recipes,
            'recipe'    => null,
            'items'     => [],
        ];

        return view('master/recipes_form', $data);
    }

    public function store()
    {
        $rules = [
            'menu_id'   => 'required|integer',
            'yield_qty' => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $itemsInput   = $this->request->getPost('items') ?? [];
        $items        = [];
        $itemErrors   = [];
        $materialName = $this->getMaterialNameMap();
        $childIds     = [];
        $recipeMap    = $this->getRecipeNameMap();

        foreach ($itemsInput as $idx => $row) {
            $type  = $row['item_type'] ?? 'raw';
            $rawId = (int) ($row['raw_material_id'] ?? 0);
            $child = (int) ($row['child_recipe_id'] ?? 0);
            $qty   = (float) ($row['qty'] ?? 0);
            $waste = (float) ($row['waste_pct'] ?? 0);

            if ($type === 'recipe') {
                if ($child > 0 && ! isset($recipeMap[$child])) {
                    $itemErrors[] = 'Sub-resep #' . $child . ' tidak ditemukan.';
                }
                if ($child <= 0 && $qty > 0) {
                    $itemErrors[] = 'Sub-resep belum dipilih untuk baris ' . ($idx + 1) . '.';
                }
                if ($child > 0 && $qty <= 0) {
                    $itemErrors[] = 'Qty untuk sub-resep baris ' . ($idx + 1) . ' harus lebih dari 0.';
                }
                if ($child > 0 && ($waste < 0 || $waste > 100)) {
                    $itemErrors[] = "Waste % baris " . ($idx + 1) . " harus di antara 0 sampai 100.";
                }
                if ($child > 0 && $qty > 0) {
                    $childIds[] = $child;
                    $items[] = [
                        'item_type'        => 'recipe',
                        'child_recipe_id'  => $child,
                        'raw_material_id'  => null,
                        'qty'              => $qty,
                        'waste_pct'        => round($waste, 3),
                        'note'             => $row['note'] ?? null,
                    ];
                }
            } else {
                if ($rawId > 0 && ($waste < 0 || $waste > 100)) {
                    $label = $materialName[$rawId] ?? 'baris ' . ($idx + 1);
                    $itemErrors[] = "Waste % untuk {$label} harus berada di antara 0 sampai 100.";
                }

                if ($rawId > 0 && $qty <= 0) {
                    $label = $materialName[$rawId] ?? 'baris ' . ($idx + 1);
                    $itemErrors[] = "Qty untuk {$label} harus lebih dari 0.";
                }

                if ($rawId > 0 && $qty > 0) {
                    $items[] = [
                        'item_type'       => 'raw',
                        'raw_material_id' => $rawId,
                        'child_recipe_id' => null,
                        'qty'             => $qty,
                        'waste_pct'       => round($waste, 3),
                        'note'            => $row['note'] ?? null,
                    ];
                }
            }
        }

        if (! empty($itemErrors)) {
            return redirect()->back()
                ->with('errors', $itemErrors)
                ->withInput();
        }

        if (empty($items)) {
            return redirect()->back()
                ->with('errors', ['items' => 'Minimal satu bahan baku atau sub-resep harus diisi.'])
                ->withInput();
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $recipeData = [
            'menu_id'    => (int) $this->request->getPost('menu_id'),
            'yield_qty'  => (float) $this->request->getPost('yield_qty'),
            'yield_unit' => $this->request->getPost('yield_unit') ?: 'porsi',
            'notes'      => $this->request->getPost('notes') ?: null,
        ];

        $recipeId = $this->recipeModel->insert($recipeData, true);

        // Cek siklus sub-recipe sebelum simpan detail
        if ($this->createsCycle($recipeId, $childIds)) {
            $db->transRollback();
            return redirect()->back()
                ->with('errors', ['Struktur sub-resep membentuk siklus. Periksa pilihan sub-resep.'])
                ->withInput();
        }

        foreach ($items as $item) {
            $item['recipe_id'] = $recipeId;
            $this->itemModel->insert($item);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan resep.')
                ->withInput();
        }

        $this->logRecipeChange($recipeId, 'create', $recipeData, $items);

        return redirect()->to(site_url('master/recipes'))
            ->with('message', 'Resep menu berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $recipe = $this->recipeModel->find($id);

        if (! $recipe) {
            return redirect()->to(site_url('master/recipes'))
                ->with('error', 'Data resep tidak ditemukan.');
        }

        $menu = $this->menuModel->find($recipe['menu_id']);

        $materials = $this->rawModel
            ->withUnit()
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $recipes = $this->getRecipeOptions($id);
        $items = $this->itemModel
            ->withMaterial()
            ->where('recipe_id', $id)
            ->findAll();

        // Hitung HPP untuk menu ini
        $hpp = $this->recipeModel->calculateHppForMenu($recipe['menu_id']);

        $data = [
            'title'     => 'Edit Resep Menu',
            'subtitle'  => 'Perbarui komposisi bahan baku menu',
            'mode'      => 'edit',
            'menus'     => [$menu],
            'materials' => $materials,
            'recipes'   => $recipes,
            'recipe'    => $recipe,
            'items'     => $items,
            'hpp'       => $hpp,
        ];

        return view('master/recipes_form', $data);
    }

    public function update(int $id)
    {
        $recipe = $this->recipeModel->find($id);

        if (! $recipe) {
            return redirect()->to(site_url('master/recipes'))
                ->with('error', 'Data resep tidak ditemukan.');
        }

        $rules = [
            'yield_qty' => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $itemsInput   = $this->request->getPost('items') ?? [];
        $items        = [];
        $itemErrors   = [];
        $materialName = $this->getMaterialNameMap();
        $childIds     = [];
        $recipeMap    = $this->getRecipeNameMap();

        foreach ($itemsInput as $idx => $row) {
            $type  = $row['item_type'] ?? 'raw';
            $rawId = (int) ($row['raw_material_id'] ?? 0);
            $child = (int) ($row['child_recipe_id'] ?? 0);
            $qty   = (float) ($row['qty'] ?? 0);
            $waste = (float) ($row['waste_pct'] ?? 0);

            if ($type === 'recipe') {
                if ($child > 0 && ! isset($recipeMap[$child])) {
                    $itemErrors[] = 'Sub-resep #' . $child . ' tidak ditemukan.';
                }
                if ($child === $id) {
                    $itemErrors[] = 'Sub-resep tidak boleh merujuk diri sendiri.';
                }
                if ($child <= 0 && $qty > 0) {
                    $itemErrors[] = 'Sub-resep belum dipilih untuk baris ' . ($idx + 1) . '.';
                }
                if ($child > 0 && $qty <= 0) {
                    $itemErrors[] = 'Qty untuk sub-resep baris ' . ($idx + 1) . ' harus lebih dari 0.';
                }
                if ($child > 0 && ($waste < 0 || $waste > 100)) {
                    $itemErrors[] = "Waste % baris " . ($idx + 1) . " harus di antara 0 sampai 100.";
                }
                if ($child > 0 && $qty > 0) {
                    $childIds[] = $child;
                    $items[] = [
                        'item_type'        => 'recipe',
                        'child_recipe_id'  => $child,
                        'raw_material_id'  => null,
                        'qty'              => $qty,
                        'waste_pct'        => round($waste, 3),
                        'note'             => $row['note'] ?? null,
                    ];
                }
            } else {
                if ($rawId > 0 && ($waste < 0 || $waste > 100)) {
                    $label = $materialName[$rawId] ?? 'baris ' . ($idx + 1);
                    $itemErrors[] = "Waste % untuk {$label} harus berada di antara 0 sampai 100.";
                }

                if ($rawId > 0 && $qty <= 0) {
                    $label = $materialName[$rawId] ?? 'baris ' . ($idx + 1);
                    $itemErrors[] = "Qty untuk {$label} harus lebih dari 0.";
                }

                if ($rawId > 0 && $qty > 0) {
                    $items[] = [
                        'item_type'       => 'raw',
                        'raw_material_id' => $rawId,
                        'child_recipe_id' => null,
                        'qty'             => $qty,
                        'waste_pct'       => round($waste, 3),
                        'note'            => $row['note'] ?? null,
                    ];
                }
            }
        }

        if (! empty($itemErrors)) {
            return redirect()->back()
                ->with('errors', $itemErrors)
                ->withInput();
        }

        if (empty($items)) {
            return redirect()->back()
                ->with('errors', ['items' => 'Minimal satu bahan baku atau sub-resep harus diisi.'])
                ->withInput();
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $updateData = [
            'yield_qty'  => (float) $this->request->getPost('yield_qty'),
            'yield_unit' => $this->request->getPost('yield_unit') ?: $recipe['yield_unit'],
            'notes'      => $this->request->getPost('notes') ?: null,
        ];

        $this->recipeModel->update($id, $updateData);

        // Hapus item lama, insert ulang (lebih simpel untuk skala kecil)
        $this->itemModel->where('recipe_id', $id)->delete();

        // Cek siklus sebelum simpan baru
        if ($this->createsCycle($id, $childIds)) {
            $db->transRollback();
            return redirect()->back()
                ->with('errors', ['Struktur sub-resep membentuk siklus. Periksa pilihan sub-resep.'])
                ->withInput();
        }

        foreach ($items as $item) {
            $item['recipe_id'] = $id;
            $this->itemModel->insert($item);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui resep.')
                ->withInput();
        }

        $this->logRecipeChange($id, 'update', $updateData, $items);

        return redirect()->to(site_url('master/recipes'))
            ->with('message', 'Resep menu berhasil diperbarui.');
    }

    /**
     * Peta id bahan -> nama untuk pesan error yang lebih informatif.
     */
    private function getMaterialNameMap(): array
    {
        $rows = $this->rawModel
            ->select('id, name')
            ->findAll();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['id']] = $row['name'];
        }

        return $map;
    }

    private function logRecipeChange(int $recipeId, string $action, array $data, array $items): void
    {
        $userId = (int) (session('user_id') ?? 0);

        $payload = [
            'recipe' => $data,
            'items'  => $items,
        ];

        $this->auditLogModel->insert([
            'entity_type' => 'recipe',
            'entity_id'   => $recipeId,
            'action'      => $action,
            'description' => 'Recipe ' . $action . ' for menu #' . ($data['menu_id'] ?? ''),
            'payload'     => json_encode($payload),
            'user_id'     => $userId > 0 ? $userId : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    private function getRecipeOptions(?int $excludeId = null): array
    {
        $rows = $this->recipeModel
            ->select('recipes.id, recipes.yield_qty, recipes.yield_unit, recipes.menu_id, menus.name AS menu_name')
            ->join('menus', 'menus.id = recipes.menu_id', 'left')
            ->orderBy('menu_name', 'ASC')
            ->findAll();

        $list = [];
        foreach ($rows as $row) {
            $id = (int) $row['id'];
            if ($excludeId !== null && $id === $excludeId) {
                continue;
            }
            $hpp = $this->recipeModel->calculateHppForMenu((int) ($row['menu_id'] ?? 0));
            $list[] = [
                'id'         => $id,
                'menu_name'  => $row['menu_name'] ?? ('Resep #' . $id),
                'yield_qty'  => $row['yield_qty'] ?? null,
                'yield_unit' => $row['yield_unit'] ?? 'porsi',
                'hpp_per_yield' => $hpp['hpp_per_yield'] ?? null,
            ];
        }

        return $list;
    }

    private function getRecipeNameMap(): array
    {
        $map = [];
        foreach ($this->getRecipeOptions() as $row) {
            $map[(int) $row['id']] = $row['menu_name'] ?? ('Resep #' . $row['id']);
        }
        return $map;
    }

    private function createsCycle(int $recipeId, array $childIds): bool
    {
        $edges = $this->getSubRecipeEdges();
        $edges[$recipeId] = array_values(array_unique(array_filter($childIds)));

        return $this->detectCycle($recipeId, $edges);
    }

    private function getSubRecipeEdges(): array
    {
        $rows = $this->itemModel
            ->select('recipe_id, child_recipe_id')
            ->where('child_recipe_id IS NOT NULL', null, false)
            ->where('item_type', 'recipe')
            ->findAll();

        $edges = [];
        foreach ($rows as $row) {
            $parent = (int) ($row['recipe_id'] ?? 0);
            $child  = (int) ($row['child_recipe_id'] ?? 0);
            if ($parent > 0 && $child > 0) {
                if (! isset($edges[$parent])) {
                    $edges[$parent] = [];
                }
                $edges[$parent][] = $child;
            }
        }

        return $edges;
    }

    private function detectCycle(int $start, array $edges): bool
    {
        $visited = [];
        $stack   = [];

        $dfs = function ($node) use (&$dfs, &$visited, &$stack, $edges): bool {
            if (isset($stack[$node]) && $stack[$node] === true) {
                return true;
            }

            if (isset($visited[$node])) {
                return false;
            }

            $visited[$node] = true;
            $stack[$node]   = true;

            foreach ($edges[$node] ?? [] as $child) {
                if ($dfs($child)) {
                    return true;
                }
            }

            $stack[$node] = false;
            return false;
        };

        return $dfs($start);
    }
}
