<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\MenuCategoryModel;
use App\Models\AuditLogModel;

/**
 * Master Menu Categories
 *
 * Perubahan utama:
 * - Rapikan struktur: rules() + payloadFromRequest() + backWithErrors()
 * - Tetap pakai route existing: master/categories/*
 * - Opsional audit log untuk create/update/delete (toggle via $enableAuditLog)
 */
class MenuCategories extends BaseController
{
    protected MenuCategoryModel $categoryModel;
    protected AuditLogModel $auditLogModel;

    /**
     * Opsional: audit log kategori.
     * - true  => catat create/update/delete ke audit_logs
     * - false => tidak mencatat (behavior lebih simpel)
     */
    private bool $enableAuditLog = true;

    public function __construct()
    {
        $this->categoryModel = new MenuCategoryModel();
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * List kategori.
     */
    public function index()
    {
        $rows = $this->categoryModel
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('master/menu_categories_index', [
            'title'    => 'Kategori Menu',
            'subtitle' => 'Kelola kategori untuk menu/produk',
            'rows'     => $rows,
        ]);
    }

    /**
     * Form create.
     */
    public function create()
    {
        return view('master/menu_categories_form', [
            'title'    => 'Tambah Kategori Menu',
            'subtitle' => 'Buat kategori baru untuk mengelompokkan menu',
            'mode'     => 'create',
            'category' => null,
        ]);
    }

    /**
     * Store kategori baru.
     */
    public function store()
    {
        if (! $this->validate($this->rules())) {
            return $this->backWithErrors();
        }

        $payload = $this->payloadFromRequest();

        $this->categoryModel->insert($payload);
        $newId = (int) $this->categoryModel->getInsertID();

        $this->logCategoryChange($newId, 'create', $payload);

        return redirect()->to(site_url('master/categories'))
            ->with('message', 'Kategori menu berhasil ditambahkan.');
    }

    /**
     * Form edit.
     */
    public function edit(int $id)
    {
        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->to(site_url('master/categories'))
                ->with('error', 'Kategori tidak ditemukan.');
        }

        return view('master/menu_categories_form', [
            'title'    => 'Edit Kategori Menu',
            'subtitle' => 'Perbarui kategori menu/produk',
            'mode'     => 'edit',
            'category' => $category,
        ]);
    }

    /**
     * Update kategori.
     */
    public function update(int $id)
    {
        if (! $this->validate($this->rules())) {
            return $this->backWithErrors();
        }

        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->to(site_url('master/categories'))
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $payload = $this->payloadFromRequest();

        $this->categoryModel->update($id, $payload);

        $this->logCategoryChange($id, 'update', $payload);

        return redirect()->to(site_url('master/categories'))
            ->with('message', 'Kategori menu berhasil diperbarui.');
    }

    /**
     * Delete kategori.
     *
     * Catatan opsional:
     * - Kalau kamu mau "block delete" saat kategori sudah dipakai menu,
     *   nanti kita tambah guard (butuh query count di MenuModel).
     */
    public function delete(int $id)
    {
        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->to(site_url('master/categories'))
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $this->categoryModel->delete($id);

        // log payload pakai data terakhir sebelum delete (lebih informatif)
        $this->logCategoryChange($id, 'delete', [
            'name'        => (string) ($category['name'] ?? ''),
            'description' => $category['description'] ?? null,
        ]);

        return redirect()->to(site_url('master/categories'))
            ->with('message', 'Kategori menu berhasil dihapus.');
    }

    // ======================================================
    // Helpers
    // ======================================================

    /**
     * Rules validasi (dipakai store & update).
     */
    private function rules(): array
    {
        return [
            // kamu sebelumnya hanya required.
            // Ini versi lebih aman tapi tetap longgar.
            'name'        => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[255]',
        ];
    }

    /**
     * Payload dari request (untuk insert/update).
     * Tujuan: hindari duplikasi mapping.
     */
    private function payloadFromRequest(): array
    {
        return [
            'name'        => trim((string) $this->request->getPost('name')),
            'description' => $this->request->getPost('description') ?: null,
        ];
    }

    /**
     * Redirect back + errors + input.
     */
    private function backWithErrors()
    {
        return redirect()->back()
            ->with('errors', $this->validator->getErrors())
            ->withInput();
    }

    /**
     * Audit log untuk kategori.
     */
    private function logCategoryChange(int $categoryId, string $action, array $payload): void
    {
        if (! $this->enableAuditLog) {
            return;
        }

        $userId = (int) (session('user_id') ?? 0);

        $this->auditLogModel->insert([
            'entity_type' => 'menu_category',
            'entity_id'   => $categoryId,
            'action'      => $action,
            'description' => 'Menu Category ' . $action . ' #' . $categoryId,
            'payload'     => json_encode($payload),
            'user_id'     => $userId > 0 ? $userId : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
