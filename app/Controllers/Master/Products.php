<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\MenuCategoryModel;

class Products extends BaseController
{
    protected MenuModel $menuModel;
    protected MenuCategoryModel $categoryModel;

    public function __construct()
    {
        $this->menuModel     = new MenuModel();
        $this->categoryModel = new MenuCategoryModel();
    }

    public function index()
    {
        $menus = $this->menuModel
            ->withCategory()
            ->orderBy('menu_categories.sort_order', 'ASC')
            ->orderBy('menus.name', 'ASC')
            ->findAll();

        $data = [
            'title'    => 'Master Produk',
            'subtitle' => 'Daftar menu makanan & minuman',
            'menus'    => $menus,
        ];

        return view('master/products_index', $data);
    }

    public function create()
    {
        $categories = $this->categoryModel->getForDropdown();

        $data = [
            'title'      => 'Tambah Produk',
            'subtitle'   => 'Buat menu baru untuk POS',
            'categories' => $categories,
            'menu'       => null,
            'formAction' => site_url('master/products/store'),
        ];

        return view('master/products_form', $data);
    }

    public function store()
    {
        $rules = [
            'name'             => 'required|min_length[3]',
            'menu_category_id' => 'required|integer',
            'price'            => 'required|numeric',
            'sku'              => 'permit_empty|max_length[50]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $data = [
            'name'             => $this->request->getPost('name'),
            'menu_category_id' => (int) $this->request->getPost('menu_category_id'),
            'sku'              => $this->request->getPost('sku') ?: null,
            'price'            => (float) $this->request->getPost('price'),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->menuModel->insert($data);

        return redirect()->to(site_url('master/products'))
            ->with('message', 'Produk berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $menu = $this->menuModel->find($id);

        if (! $menu) {
            return redirect()->to(site_url('master/products'))
                ->with('error', 'Produk tidak ditemukan.');
        }

        $categories = $this->categoryModel->getForDropdown();

        $data = [
            'title'      => 'Edit Produk',
            'subtitle'   => 'Ubah data menu',
            'categories' => $categories,
            'menu'       => $menu,
            'formAction' => site_url('master/products/update/' . $id),
        ];

        return view('master/products_form', $data);
    }

    public function update(int $id)
    {
        $rules = [
            'name'             => 'required|min_length[3]',
            'menu_category_id' => 'required|integer',
            'price'            => 'required|numeric',
            'sku'              => 'permit_empty|max_length[50]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $menu = $this->menuModel->find($id);
        if (! $menu) {
            return redirect()->to(site_url('master/products'))
                ->with('error', 'Produk tidak ditemukan.');
        }

        $data = [
            'name'             => $this->request->getPost('name'),
            'menu_category_id' => (int) $this->request->getPost('menu_category_id'),
            'sku'              => $this->request->getPost('sku') ?: null,
            'price'            => (float) $this->request->getPost('price'),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->menuModel->update($id, $data);

        return redirect()->to(site_url('master/products'))
            ->with('message', 'Produk berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $menu = $this->menuModel->find($id);
        if (! $menu) {
            return redirect()->to(site_url('master/products'))
                ->with('error', 'Produk tidak ditemukan.');
        }

        $this->menuModel->delete($id);

        return redirect()->to(site_url('master/products'))
            ->with('message', 'Produk berhasil dihapus.');
    }
}
