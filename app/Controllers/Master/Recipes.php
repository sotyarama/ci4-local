<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\RecipeModel;
use App\Models\RecipeItemModel;
use App\Models\RawMaterialModel;

class Recipes extends BaseController
{
    protected MenuModel $menuModel;
    protected RecipeModel $recipeModel;
    protected RecipeItemModel $itemModel;
    protected RawMaterialModel $rawModel;

    public function __construct()
    {
        $this->menuModel   = new MenuModel();
        $this->recipeModel = new RecipeModel();
        $this->itemModel   = new RecipeItemModel();
        $this->rawModel    = new RawMaterialModel();
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

        $data = [
            'title'    => 'Master Resep Menu',
            'subtitle' => 'Mapping menu ke bahan baku (BOM)',
            'menus'    => $menus,
        ];

        return view('master/recipes_index', $data);
    }

    public function create()
    {
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

        $data = [
            'title'     => 'Tambah Resep Menu',
            'subtitle'  => 'Definisikan komposisi bahan baku per menu',
            'mode'      => 'create',
            'menus'     => $menus,
            'materials' => $materials,
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

        $itemsInput = $this->request->getPost('items') ?? [];
        $items      = [];

        foreach ($itemsInput as $row) {
            $rawId = (int) ($row['raw_material_id'] ?? 0);
            $qty   = (float) ($row['qty'] ?? 0);
            $waste = (float) ($row['waste_pct'] ?? 0);

            if ($rawId > 0 && $qty > 0) {
                $items[] = [
                    'raw_material_id' => $rawId,
                    'qty'             => $qty,
                    'waste_pct'       => $waste,
                    'note'            => $row['note'] ?? null,
                ];
            }
        }

        if (empty($items)) {
            return redirect()->back()
                ->with('errors', ['items' => 'Minimal satu bahan baku harus diisi.'])
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

        $items = $this->itemModel
            ->withMaterial()
            ->where('recipe_id', $id)
            ->findAll();

        // 🔎 Hitung HPP untuk menu ini
        $hpp = $this->recipeModel->calculateHppForMenu($recipe['menu_id']);

        $data = [
            'title'     => 'Edit Resep Menu',
            'subtitle'  => 'Perbarui komposisi bahan baku menu',
            'mode'      => 'edit',
            'menus'     => [$menu],
            'materials' => $materials,
            'recipe'    => $recipe,
            'items'     => $items,
            'hpp'       => $hpp, // ⬅️ kirim ke view
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

        $itemsInput = $this->request->getPost('items') ?? [];
        $items      = [];

        foreach ($itemsInput as $row) {
            $rawId = (int) ($row['raw_material_id'] ?? 0);
            $qty   = (float) ($row['qty'] ?? 0);
            $waste = (float) ($row['waste_pct'] ?? 0);

            if ($rawId > 0 && $qty > 0) {
                $items[] = [
                    'raw_material_id' => $rawId,
                    'qty'             => $qty,
                    'waste_pct'       => $waste,
                    'note'            => $row['note'] ?? null,
                ];
            }
        }

        if (empty($items)) {
            return redirect()->back()
                ->with('errors', ['items' => 'Minimal satu bahan baku harus diisi.'])
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

        return redirect()->to(site_url('master/recipes'))
            ->with('message', 'Resep menu berhasil diperbarui.');
    }
}
