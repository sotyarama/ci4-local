<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\MenuCategoryModel;

class MenuCategories extends BaseController
{
    protected MenuCategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new MenuCategoryModel();
    }

    public function index()
    {
        $rows = $this->categoryModel
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'    => 'Kategori Menu',
            'subtitle' => 'Kelola kategori untuk menu/produk',
            'rows'     => $rows,
        ];

        return view('master/menu_categories_index', $data);
    }

    public function create()
    {
        $data = [
            'title'    => 'Tambah Kategori Menu',
            'subtitle' => 'Buat kategori baru untuk mengelompokkan menu',
            'mode'     => 'create',
            'category' => null,
        ];

        return view('master/menu_categories_form', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
        ];

        $this->categoryModel->insert($data);

        return redirect()->to(site_url('master/categories'))
            ->with('message', 'Kategori menu berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->to(site_url('master/categories'))
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Kategori Menu',
            'subtitle' => 'Perbarui kategori menu/produk',
            'mode'     => 'edit',
            'category' => $category,
        ];

        return view('master/menu_categories_form', $data);
    }

    public function update(int $id)
    {
        $rules = [
            'name' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->to(site_url('master/categories'))
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
        ];

        $this->categoryModel->update($id, $data);

        return redirect()->to(site_url('master/categories'))
            ->with('message', 'Kategori menu berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->to(site_url('master/categories'))
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $this->categoryModel->delete($id);

        return redirect()->to(site_url('master/categories'))
            ->with('message', 'Kategori menu berhasil dihapus.');
    }
}
