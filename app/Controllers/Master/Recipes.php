<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\MenuModel;
use App\Models\RawMaterialModel;
use App\Models\RecipeItemModel;
use App\Models\RecipeModel;

/**
 * Master Recipes Controller
 *
 * Tanggung jawab:
 * - Mapping Menu → Recipe (BOM)
 * - Support sub-recipe (nested recipe)
 * - Hitung HPP via RecipeModel
 *
 * Catatan penting:
 * - Update recipe = delete all items → insert ulang (simple & safe for small scale)
 * - Cycle detection WAJIB untuk mencegah infinite recursion HPP
 */
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

    // =====================================================
    // INDEX
    // =====================================================
    public function index()
    {
        $db = \Config\Database::connect();

        $menus = $db->table('menus m')
            ->select('m.*, c.name AS category_name, r.id AS recipe_id')
            ->join('menu_categories c', 'c.id = m.menu_category_id', 'left')
            ->join('recipes r', 'r.menu_id = m.id', 'left')
            ->orderBy('c.name', 'ASC')
            ->orderBy('m.name', 'ASC')
            ->get()
            ->getResultArray();

        // Tambahkan info HPP jika sudah punya resep
        foreach ($menus as &$menu) {
            if (! empty($menu['recipe_id'])) {
                $hpp = $this->recipeModel->calculateHppForMenu((int) $menu['id']);
                $menu['hpp_per_yield'] = $hpp['hpp_per_yield'] ?? null;
                $menu['yield_unit']    = $hpp['recipe']['yield_unit'] ?? null;
            } else {
                $menu['hpp_per_yield'] = null;
                $menu['yield_unit']    = null;
            }
        }
        unset($menu);

        // Cek apakah masih ada menu tanpa resep
        $totalMenu       = $this->menuModel->countAllResults();
        $menuWithRecipe  = $this->recipeModel->select('menu_id')->distinct()->countAllResults();
        $canCreateRecipe = $totalMenu > $menuWithRecipe;

        return view('master/recipes_index', [
            'title'           => 'Master Resep Menu',
            'subtitle'        => 'Mapping menu ke bahan baku (BOM)',
            'menus'           => $menus,
            'canCreateRecipe' => $canCreateRecipe,
        ]);
    }

    // =====================================================
    // CREATE
    // =====================================================
    public function create()
    {
        // Guard: semua menu sudah punya resep
        $totalMenu      = $this->menuModel->countAllResults();
        $menuWithRecipe = $this->recipeModel->select('menu_id')->distinct()->countAllResults();

        if ($totalMenu <= $menuWithRecipe) {
            return redirect()->to(site_url('master/recipes'))
                ->with('error', 'Semua menu sudah memiliki resep.');
        }

        // Menu yang BELUM punya resep
        $db = \Config\Database::connect();
        $menus = $db->table('menus m')
            ->select('m.*')
            ->join('recipes r', 'r.menu_id = m.id', 'left')
            ->where('r.id IS NULL', null, false)
            ->orderBy('m.name', 'ASC')
            ->get()
            ->getResultArray();

        $materials = $this->rawModel
            ->withUnit()
            ->where('raw_materials.is_active', 1)
            ->orderBy('raw_materials.name', 'ASC')
            ->findAll();

        return view('master/recipes_form', [
            'title'     => 'Tambah Resep Menu',
            'subtitle'  => 'Definisikan komposisi bahan baku per menu',
            'mode'      => 'create',
            'menus'     => $menus,
            'materials' => $materials,
            'recipes'   => $this->getRecipeOptions(),
            'recipe'    => null,
            'items'     => [],
        ]);
    }

    // =====================================================
    // STORE
    // =====================================================
    public function store()
    {
        if (! $this->validate([
            'menu_id'   => 'required|integer',
            'yield_qty' => 'required|numeric|greater_than[0]',
        ])) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        [$items, $childIds, $itemErrors] = $this->parseItemsFromRequest();

        if (! empty($itemErrors)) {
            return redirect()->back()->with('errors', $itemErrors)->withInput();
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

        if ($this->createsCycle($recipeId, $childIds)) {
            $db->transRollback();
            return redirect()->back()
                ->with('errors', ['Struktur sub-resep membentuk siklus.'])
                ->withInput();
        }

        foreach ($items as $item) {
            $item['recipe_id'] = $recipeId;
            $this->itemModel->insert($item);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal menyimpan resep.')->withInput();
        }

        $this->logRecipeChange($recipeId, 'create', $recipeData, $items);

        return redirect()->to(site_url('master/recipes'))
            ->with('message', 'Resep menu berhasil disimpan.');
    }

    // =====================================================
    // EDIT
    // =====================================================
    public function edit(int $id)
    {
        $recipe = $this->recipeModel->find($id);
        if (! $recipe) {
            return redirect()->to(site_url('master/recipes'))
                ->with('error', 'Data resep tidak ditemukan.');
        }

        return view('master/recipes_form', [
            'title'     => 'Edit Resep Menu',
            'subtitle'  => 'Perbarui komposisi bahan baku menu',
            'mode'      => 'edit',
            'menus'     => [$this->menuModel->find($recipe['menu_id'])],
            'materials' => $this->rawModel
                ->withUnit()
                ->where('raw_materials.is_active', 1)
                ->orderBy('raw_materials.name', 'ASC')
                ->findAll(),

            'recipes'   => $this->getRecipeOptions($id),
            'recipe'    => $recipe,
            'items'     => $this->itemModel->withMaterial()->where('recipe_id', $id)->findAll(),
            'hpp'       => $this->recipeModel->calculateHppForMenu($recipe['menu_id']),
        ]);
    }

    // =====================================================
    // UPDATE
    // =====================================================
    public function update(int $id)
    {
        $recipe = $this->recipeModel->find($id);
        if (! $recipe) {
            return redirect()->to(site_url('master/recipes'))
                ->with('error', 'Data resep tidak ditemukan.');
        }

        if (! $this->validate([
            'yield_qty' => 'required|numeric|greater_than[0]',
        ])) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        [$items, $childIds, $itemErrors] = $this->parseItemsFromRequest($id);

        if (! empty($itemErrors)) {
            return redirect()->back()->with('errors', $itemErrors)->withInput();
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $updateData = [
            'yield_qty'  => (float) $this->request->getPost('yield_qty'),
            'yield_unit' => $this->request->getPost('yield_unit') ?: $recipe['yield_unit'],
            'notes'      => $this->request->getPost('notes') ?: null,
        ];

        $this->recipeModel->update($id, $updateData);
        $this->itemModel->where('recipe_id', $id)->delete();

        if ($this->createsCycle($id, $childIds)) {
            $db->transRollback();
            return redirect()->back()->with('errors', ['Struktur sub-resep membentuk siklus.'])->withInput();
        }

        foreach ($items as $item) {
            $item['recipe_id'] = $id;
            $this->itemModel->insert($item);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal memperbarui resep.')->withInput();
        }

        $this->logRecipeChange($id, 'update', $updateData, $items);

        return redirect()->to(site_url('master/recipes'))
            ->with('message', 'Resep menu berhasil diperbarui.');
    }

    // =====================================================
    // HELPERS
    // =====================================================

    /**
     * Parse & validasi item resep dari POST (raw material & sub-recipe)
     */
    private function parseItemsFromRequest(?int $selfId = null): array
    {
        $itemsInput   = $this->request->getPost('items') ?? [];
        $items        = [];
        $childIds     = [];
        $errors       = [];

        $materialMap  = $this->getMaterialNameMap();
        $materialMeta = $this->getMaterialMetaMap();
        $recipeMap    = $this->getRecipeNameMap();

        foreach ($itemsInput as $i => $row) {
            $type  = $row['item_type'] ?? 'raw';
            $qty   = (float) ($row['qty'] ?? 0);
            $waste = (float) ($row['waste_pct'] ?? 0);

            if ($waste < 0 || $waste > 100) {
                $errors[] = "Waste % baris " . ($i + 1) . " harus 0–100.";
                continue;
            }

            if ($type === 'recipe') {
                $child = (int) ($row['child_recipe_id'] ?? 0);

                if ($child <= 0 && $qty <= 0) {
                    continue; // baris kosong
                }

                if ($child <= 0 || ! isset($recipeMap[$child])) {
                    $errors[] = "Sub-resep baris " . ($i + 1) . " tidak valid.";
                    continue;
                }

                if ($selfId !== null && $child === $selfId) {
                    $errors[] = "Sub-resep tidak boleh merujuk diri sendiri.";
                    continue;
                }

                if ($qty <= 0) {
                    $errors[] = "Qty sub-resep baris " . ($i + 1) . " harus > 0.";
                    continue;
                }

                $childIds[] = $child;
                $items[] = [
                    'item_type'       => 'recipe',
                    'child_recipe_id' => $child,
                    'raw_material_id' => null,
                    'qty'             => $qty,
                    'waste_pct'       => round($waste, 3),
                    'note'            => $row['note'] ?? null,
                ];
            } else {
                $rawId = (int) ($row['raw_material_id'] ?? 0);

                if ($rawId <= 0 && $qty <= 0) {
                    continue; // baris kosong
                }

                if ($rawId <= 0 || $qty <= 0) {
                    $label = $materialMap[$rawId] ?? 'baris ' . ($i + 1);
                    $errors[] = "Qty untuk {$label} harus > 0.";
                    continue;
                }

                $meta = $materialMeta[$rawId] ?? null;
                if ($meta && (int) ($meta['has_variants'] ?? 0) === 1) {
                    $errors[] = "Bahan {$meta['name']} memiliki varian. Pilih varian lewat opsi menu, bukan di resep.";
                    continue;
                }

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

        return [$items, $childIds, $errors];
    }

    private function getMaterialNameMap(): array
    {
        return array_column(
            $this->rawModel->select('id, name')->findAll(),
            'name',
            'id'
        );
    }

    private function getMaterialMetaMap(): array
    {
        $rows = $this->rawModel->select('id, name, has_variants')->findAll();
        $map = [];
        foreach ($rows as $row) {
            $id = (int) ($row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $map[$id] = $row;
        }

        return $map;
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

            $hpp = $this->recipeModel->calculateHppForMenu((int) $row['menu_id']);

            $list[] = [
                'id'            => $id,
                'menu_name'     => $row['menu_name'] ?? ('Resep #' . $id),
                'yield_qty'     => $row['yield_qty'],
                'yield_unit'    => $row['yield_unit'] ?? 'porsi',
                'hpp_per_yield' => $hpp['hpp_per_yield'] ?? null,
            ];
        }

        return $list;
    }

    private function getRecipeNameMap(): array
    {
        return array_column($this->getRecipeOptions(), 'menu_name', 'id');
    }

    private function createsCycle(int $recipeId, array $childIds): bool
    {
        $edges = $this->getSubRecipeEdges();
        $edges[$recipeId] = array_unique($childIds);
        return $this->detectCycle($recipeId, $edges);
    }

    private function getSubRecipeEdges(): array
    {
        $edges = [];
        $rows = $this->itemModel
            ->select('recipe_id, child_recipe_id')
            ->where('item_type', 'recipe')
            ->where('child_recipe_id IS NOT NULL', null, false)
            ->findAll();

        foreach ($rows as $row) {
            $edges[(int) $row['recipe_id']][] = (int) $row['child_recipe_id'];
        }

        return $edges;
    }

    private function detectCycle(int $start, array $edges): bool
    {
        $visited = [];
        $stack   = [];

        $dfs = function ($node) use (&$dfs, &$visited, &$stack, $edges): bool {
            if ($stack[$node] ?? false) return true;
            if ($visited[$node] ?? false) return false;

            $visited[$node] = true;
            $stack[$node]   = true;

            foreach ($edges[$node] ?? [] as $child) {
                if ($dfs($child)) return true;
            }

            $stack[$node] = false;
            return false;
        };

        return $dfs($start);
    }

    private function logRecipeChange(int $recipeId, string $action, array $data, array $items): void
    {
        $this->auditLogModel->insert([
            'entity_type' => 'recipe',
            'entity_id'   => $recipeId,
            'action'      => $action,
            'description' => "Recipe {$action} #{$recipeId}",
            'payload'     => json_encode(['recipe' => $data, 'items' => $items]),
            'user_id'     => session('user_id') ?: null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
