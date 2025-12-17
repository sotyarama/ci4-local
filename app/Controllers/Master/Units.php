<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\UnitModel;
use App\Models\AuditLogModel;

class Units extends BaseController
{
    protected UnitModel $unitModel;
    protected AuditLogModel $auditLogModel;

    /**
     * OPTIONAL: set true kalau kamu ingin delete juga tercatat di audit log.
     * Default: false (biar perilaku sebelumnya tidak berubah).
     */
    private bool $auditDelete = false;

    public function __construct()
    {
        $this->unitModel     = new UnitModel();
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * List semua unit.
     */
    public function index()
    {
        $units = $this->unitModel
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('master/units_index', [
            'title'    => 'Master Satuan',
            'subtitle' => 'Daftar satuan untuk bahan baku & resep',
            'units'    => $units,
        ]);
    }

    /**
     * Form create unit.
     */
    public function create()
    {
        return view('master/units_form', [
            'title'      => 'Tambah Satuan',
            'subtitle'   => 'Buat satuan baru (mis: gram, ml, pcs)',
            'unit'       => null,
            'formAction' => site_url('master/units/store'),
        ]);
    }

    /**
     * Simpan unit baru.
     */
    public function store()
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $payload = $this->payloadFromRequest();

        $this->unitModel->insert($payload);
        $newId = (int) $this->unitModel->getInsertID();

        $this->logUnitChange($newId, 'create', $payload);

        return redirect()->to(site_url('master/units'))
            ->with('message', 'Satuan berhasil ditambahkan.');
    }

    /**
     * Form edit unit.
     */
    public function edit(int $id)
    {
        $unit = $this->unitModel->find($id);
        if (! $unit) {
            return redirect()->to(site_url('master/units'))
                ->with('error', 'Satuan tidak ditemukan.');
        }

        return view('master/units_form', [
            'title'      => 'Edit Satuan',
            'subtitle'   => 'Ubah data satuan',
            'unit'       => $unit,
            'formAction' => site_url('master/units/update/' . $id),
        ]);
    }

    /**
     * Update unit.
     */
    public function update(int $id)
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $unit = $this->unitModel->find($id);
        if (! $unit) {
            return redirect()->to(site_url('master/units'))
                ->with('error', 'Satuan tidak ditemukan.');
        }

        $payload = $this->payloadFromRequest();

        $this->unitModel->update($id, $payload);
        $this->logUnitChange($id, 'update', $payload);

        return redirect()->to(site_url('master/units'))
            ->with('message', 'Satuan berhasil diperbarui.');
    }

    /**
     * Delete unit.
     * Catatan:
     * - Default TIDAK audit delete (untuk jaga perilaku lama)
     * - Kalau mau: ubah $auditDelete = true.
     */
    public function delete(int $id)
    {
        $unit = $this->unitModel->find($id);
        if (! $unit) {
            return redirect()->to(site_url('master/units'))
                ->with('error', 'Satuan tidak ditemukan.');
        }

        if ($this->auditDelete) {
            $this->logUnitChange($id, 'delete', [
                'snapshot' => $unit,
            ]);
        }

        $this->unitModel->delete($id);

        return redirect()->to(site_url('master/units'))
            ->with('message', 'Satuan berhasil dihapus.');
    }

    /**
     * Rules validasi.
     */
    private function rules(): array
    {
        return [
            'name'       => 'required|min_length[1]|max_length[50]',
            'short_name' => 'required|min_length[1]|max_length[20]',
        ];
    }

    /**
     * Ambil payload dari POST untuk insert/update.
     */
    private function payloadFromRequest(): array
    {
        return [
            'name'       => (string) $this->request->getPost('name'),
            'short_name' => (string) $this->request->getPost('short_name'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    /**
     * Audit log perubahan unit (create/update/delete optional).
     */
    private function logUnitChange(int $unitId, string $action, array $payload): void
    {
        $userId = (int) (session('user_id') ?? 0);

        $this->auditLogModel->insert([
            'entity_type' => 'unit',
            'entity_id'   => $unitId,
            'action'      => $action,
            'description' => 'Unit ' . $action . ' #' . $unitId,
            'payload'     => json_encode($payload),
            'user_id'     => $userId > 0 ? $userId : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
