<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\MenuCategoryModel;
use App\Models\AuditLogModel;

class Products extends BaseController
{
    protected MenuModel $menuModel;
    protected MenuCategoryModel $categoryModel;
    protected AuditLogModel $auditLogModel;

    /**
     * Toggle audit log untuk aksi DELETE.
     * - true  : delete akan dicatat di audit_log (recommended untuk tracking)
     * - false : perilaku kembali seperti sebelumnya (tanpa log delete)
     */
    private bool $logDelete = true;

    public function __construct()
    {
        $this->menuModel     = new MenuModel();
        $this->categoryModel = new MenuCategoryModel();
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * List semua menu/produk beserta kategori (untuk halaman index).
     */
    public function index()
    {
        $menus = $this->menuModel
            ->withCategory()
            ->orderBy('menu_categories.name', 'ASC')
            ->orderBy('menus.name', 'ASC')
            ->findAll();

        return view('master/products_index', [
            'title'    => 'Master Produk',
            'subtitle' => 'Daftar menu makanan & minuman',
            'menus'    => $menus,
        ]);
    }

    /**
     * Tampilkan form create produk.
     */
    public function create()
    {
        return view('master/products_form', [
            'title'      => 'Tambah Produk',
            'subtitle'   => 'Buat menu baru untuk POS',
            'categories' => $this->categoryModel->getForDropdown(),
            'menu'       => null,
            'formAction' => site_url('master/products/store'),
        ]);
    }

    /**
     * Simpan produk baru.
     */
    public function store()
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        // Payload insert/update disentralisasi supaya tidak duplikasi mapping.
        $payload = $this->payloadFromRequest();

        $this->menuModel->insert($payload);
        $newId = (int) $this->menuModel->getInsertID();

        // Audit log create
        $this->logMenuChange($newId, 'create', $payload);

        return redirect()->to(site_url('master/products'))
            ->with('message', 'Produk berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit produk.
     */
    public function edit(int $id)
    {
        $menu = $this->menuModel->find($id);
        if (! $menu) {
            return redirect()->to(site_url('master/products'))
                ->with('error', 'Produk tidak ditemukan.');
        }

        return view('master/products_form', [
            'title'      => 'Edit Produk',
            'subtitle'   => 'Ubah data menu',
            'categories' => $this->categoryModel->getForDropdown(),
            'menu'       => $menu,
            'formAction' => site_url('master/products/update/' . $id),
        ]);
    }

    /**
     * Update produk.
     */
    public function update(int $id)
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $menu = $this->menuModel->find($id);
        if (! $menu) {
            return redirect()->to(site_url('master/products'))
                ->with('error', 'Produk tidak ditemukan.');
        }

        $payload = $this->payloadFromRequest();

        $this->menuModel->update($id, $payload);

        // Audit log update
        $this->logMenuChange($id, 'update', $payload);

        return redirect()->to(site_url('master/products'))
            ->with('message', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus produk.
     *
     * Catatan:
     * - Delete di CI4 Model biasanya soft delete kalau model/table dikonfigurasi demikian.
     * - Audit log delete bersifat opsional dan bisa dimatikan lewat $logDelete di atas.
     */
    public function delete(int $id)
    {
        $menu = $this->menuModel->find($id);
        if (! $menu) {
            return redirect()->to(site_url('master/products'))
                ->with('error', 'Produk tidak ditemukan.');
        }

        // Snapshot data lama untuk kebutuhan audit log delete (kalau diaktifkan).
        $snapshot = [
            'name'             => $menu['name'] ?? null,
            'menu_category_id' => $menu['menu_category_id'] ?? null,
            'sku'              => $menu['sku'] ?? null,
            'price'            => $menu['price'] ?? null,
            'is_active'        => $menu['is_active'] ?? null,
        ];

        $this->menuModel->delete($id);

        // OPTIONAL: log delete (bisa dimatikan via toggle)
        if ($this->logDelete) {
            $this->logMenuChange($id, 'delete', $snapshot);
        }

        return redirect()->to(site_url('master/products'))
            ->with('message', 'Produk berhasil dihapus.');
    }

    /**
     * Rules validasi (dipakai store & update).
     */
    private function rules(): array
    {
        return [
            'name'             => 'required|min_length[3]',
            'menu_category_id' => 'required|integer',
            'price'            => 'required|numeric',
            'sku'              => 'permit_empty|max_length[50]',
        ];
    }

    /**
     * Ambil payload dari POST untuk insert/update.
     *
     * Alasan dipisah:
     * - Menghindari duplikasi mapping array di store/update
     * - Normalisasi ringan (trim) untuk name/sku
     */
    private function payloadFromRequest(): array
    {
        $name = trim((string) $this->request->getPost('name'));
        $sku  = trim((string) ($this->request->getPost('sku') ?? ''));

        return [
            'name'             => $name,
            'menu_category_id' => (int) $this->request->getPost('menu_category_id'),
            'sku'              => ($sku !== '') ? $sku : null,
            'price'            => (float) $this->request->getPost('price'),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    /**
     * Audit log untuk perubahan menu (create/update/delete).
     *
     * Payload disimpan dalam JSON agar mudah audit/trace.
     * JSON_UNESCAPED_UNICODE -> supaya karakter non-ascii (Indonesia) tidak jadi \uXXXX.
     */
    private function logMenuChange(int $menuId, string $action, array $payload): void
    {
        $userId = (int) (session('user_id') ?? 0);

        $this->auditLogModel->insert([
            'entity_type' => 'menu',
            'entity_id'   => $menuId,
            'action'      => $action,
            'description' => 'Menu ' . $action . ' #' . $menuId,
            'payload'     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'user_id'     => $userId > 0 ? $userId : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
