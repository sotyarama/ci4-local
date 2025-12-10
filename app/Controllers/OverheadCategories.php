<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OverheadCategoryModel;

class OverheadCategories extends BaseController
{
    protected OverheadCategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new OverheadCategoryModel();
    }

    public function index()
    {
        $rows = $this->categoryModel
            ->orderBy('is_active', 'DESC')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'    => 'Kategori Overhead',
            'subtitle' => 'Kelola kategori biaya overhead (non gaji)',
            'rows'     => $rows,
        ];

        return view('overhead_categories/overhead_categories_index', $data);
    }

    public function create()
    {
        $data = [
            'title'    => 'Tambah Kategori Overhead',
            'subtitle' => 'Masukkan nama kategori (mis: sewa, listrik, internet)',
            'mode'     => 'create',
            'category' => null,
        ];

        return view('overhead_categories/overhead_categories_form', $data);
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

        $this->categoryModel->insert([
            'name'      => $this->request->getPost('name'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(site_url('overhead-categories'))
            ->with('message', 'Kategori overhead berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->to(site_url('overhead-categories'))
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Kategori Overhead',
            'subtitle' => 'Perbarui nama atau nonaktifkan kategori.',
            'mode'     => 'edit',
            'category' => $category,
        ];

        return view('overhead_categories/overhead_categories_form', $data);
    }

    public function update(int $id)
    {
        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->to(site_url('overhead-categories'))
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $rules = [
            'name' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $this->categoryModel->update($id, [
            'name'      => $this->request->getPost('name'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(site_url('overhead-categories'))
            ->with('message', 'Kategori overhead berhasil diperbarui.');
    }
}
