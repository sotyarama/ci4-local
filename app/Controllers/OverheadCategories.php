<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OverheadCategoryModel;
use App\Services\AuditLogService;

class OverheadCategories extends BaseController
{
    protected OverheadCategoryModel $categoryModel;
    protected AuditLogService $auditService;

    public function __construct()
    {
        $this->categoryModel = new OverheadCategoryModel();
        $this->auditService  = new AuditLogService();
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

        $payload = [
            'name'      => $this->request->getPost('name'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->categoryModel->insert($payload);
        $insertId = $this->categoryModel->getInsertID();

        // Log overhead category creation
        $this->auditService->log('overhead_category', 'create', $insertId, $payload, 'Overhead category created: ' . $payload['name']);

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

        $oldData = $category;
        $payload = [
            'name'      => $this->request->getPost('name'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->categoryModel->update($id, $payload);

        // Log overhead category update
        $this->auditService->log('overhead_category', 'update', $id, [
            'before' => $oldData,
            'after'  => $payload,
        ], 'Overhead category updated: ' . $payload['name']);

        return redirect()->to(site_url('overhead-categories'))
            ->with('message', 'Kategori overhead berhasil diperbarui.');
    }

    public function toggle()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Invalid request',
            ]);
        }

        $id = (int) ($this->request->getPost('id') ?? 0);
        if ($id <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Invalid category id',
            ]);
        }

        $category = $this->categoryModel->find($id);
        if (! $category) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Kategori tidak ditemukan',
            ]);
        }

        $oldStatus = (int) ($category['is_active'] ?? 0);
        $newStatus = $oldStatus === 1 ? 0 : 1;
        $this->categoryModel->update($id, ['is_active' => $newStatus]);

        // Log toggle action
        $this->auditService->log('overhead_category', 'toggle', $id, [
            'name'       => $category['name'] ?? '',
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ], 'Overhead category ' . ($newStatus ? 'activated' : 'deactivated') . ': ' . ($category['name'] ?? ''));

        return $this->response->setJSON([
            'status'     => 'ok',
            'is_active'  => $newStatus,
            'message'    => 'Kategori diperbarui',
        ]);
    }
}
