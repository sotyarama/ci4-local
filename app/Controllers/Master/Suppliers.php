<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\SupplierModel;
use App\Models\AuditLogModel;

class Suppliers extends BaseController
{
    protected SupplierModel $supplierModel;
    protected AuditLogModel $auditLogModel;

    /**
     * OPTIONAL:
     * - true  => delete supplier juga dicatat di audit log (dengan snapshot sebelum delete)
     * - false => perilaku lama (delete tanpa audit log)
     */
    private bool $auditDelete = false;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * List semua supplier (untuk halaman index).
     */
    public function index()
    {
        $suppliers = $this->supplierModel
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('master/suppliers_index', [
            'title'     => 'Master Supplier',
            'subtitle'  => 'Daftar supplier bahan baku',
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Tampilkan form create supplier.
     */
    public function create()
    {
        return view('master/suppliers_form', [
            'title'      => 'Tambah Supplier',
            'subtitle'   => 'Daftarkan supplier baru',
            'supplier'   => null,
            'formAction' => site_url('master/suppliers/store'),
        ]);
    }

    /**
     * Simpan supplier baru.
     */
    public function store()
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $payload = $this->payloadFromRequest();

        $this->supplierModel->insert($payload);
        $newId = (int) $this->supplierModel->getInsertID();

        // Audit: create
        $this->logSupplierChange($newId, 'create', $payload);

        return redirect()->to(site_url('master/suppliers'))
            ->with('message', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit supplier.
     */
    public function edit(int $id)
    {
        $supplier = $this->supplierModel->find($id);
        if (! $supplier) {
            return redirect()->to(site_url('master/suppliers'))
                ->with('error', 'Supplier tidak ditemukan.');
        }

        return view('master/suppliers_form', [
            'title'      => 'Edit Supplier',
            'subtitle'   => 'Ubah data supplier',
            'supplier'   => $supplier,
            'formAction' => site_url('master/suppliers/update/' . $id),
        ]);
    }

    /**
     * Update supplier.
     */
    public function update(int $id)
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $supplier = $this->supplierModel->find($id);
        if (! $supplier) {
            return redirect()->to(site_url('master/suppliers'))
                ->with('error', 'Supplier tidak ditemukan.');
        }

        $payload = $this->payloadFromRequest();

        $this->supplierModel->update($id, $payload);

        // Audit: update
        $this->logSupplierChange($id, 'update', $payload);

        return redirect()->to(site_url('master/suppliers'))
            ->with('message', 'Supplier berhasil diperbarui.');
    }

    /**
     * Hapus supplier.
     *
     * Catatan:
     * - Default TIDAK audit delete (untuk jaga perilaku lama)
     * - Kalau mau: ubah $auditDelete = true.
     */
    public function delete(int $id)
    {
        $supplier = $this->supplierModel->find($id);
        if (! $supplier) {
            return redirect()->to(site_url('master/suppliers'))
                ->with('error', 'Supplier tidak ditemukan.');
        }

        // OPTIONAL: log snapshot sebelum delete (supaya ada jejak data yang dihapus)
        if ($this->auditDelete) {
            $this->logSupplierChange($id, 'delete', [
                'snapshot' => $supplier,
            ]);
        }

        $this->supplierModel->delete($id);

        return redirect()->to(site_url('master/suppliers'))
            ->with('message', 'Supplier berhasil dihapus.');
    }

    /**
     * Rules validasi (dipakai store & update).
     */
    private function rules(): array
    {
        return [
            // tambah max_length biar aman (opsional tapi recommended)
            'name'    => 'required|min_length[3]|max_length[150]',
            'phone'   => 'permit_empty|max_length[50]',
            'address' => 'permit_empty',
        ];
    }

    /**
     * Ambil payload dari POST untuk insert/update.
     * Tujuan: menghindari duplikasi mapping di store/update.
     */
    private function payloadFromRequest(): array
    {
        // trim name supaya input rapi (tanpa spasi depan/belakang)
        $name = trim((string) $this->request->getPost('name'));

        return [
            'name'      => $name,
            'phone'     => $this->request->getPost('phone') ?: null,
            'address'   => $this->request->getPost('address') ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    /**
     * Audit log untuk perubahan supplier (create/update/delete optional).
     */
    private function logSupplierChange(int $supplierId, string $action, array $payload): void
    {
        $userId = (int) (session('user_id') ?? 0);

        $this->auditLogModel->insert([
            'entity_type' => 'supplier',
            'entity_id'   => $supplierId,
            'action'      => $action,
            'description' => 'Supplier ' . $action . ' #' . $supplierId,
            'payload'     => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'user_id'     => $userId > 0 ? $userId : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
