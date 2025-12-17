<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\RawMaterialModel;
use App\Models\UnitModel;
use App\Models\AuditLogModel;

class RawMaterials extends BaseController
{
    protected RawMaterialModel $rawModel;
    protected UnitModel $unitModel;
    protected AuditLogModel $auditLogModel;

    /**
     * OPTIONAL:
     * - auditCreateUpdate: true  => log create + update ke audit_log
     * - auditDelete:      true  => log snapshot sebelum delete ke audit_log
     *
     * Default dibuat "false" untuk menjaga perilaku lama (kalau sebelumnya belum audit).
     */
    private bool $auditCreateUpdate = false;
    private bool $auditDelete = false;

    public function __construct()
    {
        $this->rawModel      = new RawMaterialModel();
        $this->unitModel     = new UnitModel();
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * List semua bahan baku beserta unit (untuk halaman index).
     */
    public function index()
    {
        $materials = $this->rawModel
            ->withUnit()
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('master/raw_materials_index', [
            'title'     => 'Master Bahan Baku',
            'subtitle'  => 'Daftar bahan baku untuk resep & stok',
            'materials' => $materials,
        ]);
    }

    /**
     * Tampilkan form create bahan baku.
     */
    public function create()
    {
        return view('master/raw_materials_form', [
            'title'      => 'Tambah Bahan Baku',
            'subtitle'   => 'Daftarkan bahan baku baru',
            'units'      => $this->unitModel->getForDropdown(),
            'material'   => null,
            'formAction' => site_url('master/raw-materials/store'),
        ]);
    }

    /**
     * Simpan bahan baku baru.
     *
     * Behavior:
     * - current_stock = initial_stock
     * - cost_last     = initial_cost
     * - cost_avg      = (initial_stock > 0) ? initial_cost : 0
     *
     * Catatan: cost_avg di sini sengaja dibuat simple mengikuti perilaku sebelumnya.
     * Nanti kalau mau akurat (weighted average), itu lebih cocok dihitung dari transaksi pembelian.
     */
    public function store()
    {
        if (! $this->validate($this->rules('create'))) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $payload = $this->payloadFromRequest('create');

        $this->rawModel->insert($payload);
        $newId = (int) $this->rawModel->getInsertID();

        if ($this->auditCreateUpdate) {
            $this->logRawMaterialChange($newId, 'create', $payload);
        }

        return redirect()->to(site_url('master/raw-materials'))
            ->with('message', 'Bahan baku berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit bahan baku.
     */
    public function edit(int $id)
    {
        $material = $this->rawModel->find($id);
        if (! $material) {
            return redirect()->to(site_url('master/raw-materials'))
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        return view('master/raw_materials_form', [
            'title'      => 'Edit Bahan Baku',
            'subtitle'   => 'Ubah data bahan baku',
            'units'      => $this->unitModel->getForDropdown(),
            'material'   => $material,
            'formAction' => site_url('master/raw-materials/update/' . $id),
        ]);
    }

    /**
     * Update bahan baku.
     *
     * Catatan:
     * - current_stock boleh diedit (sesuai perilaku sekarang).
     * - cost_last / cost_avg tidak disentuh di sini (umumnya berubah lewat pembelian / stock movement).
     */
    public function update(int $id)
    {
        if (! $this->validate($this->rules('update'))) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $material = $this->rawModel->find($id);
        if (! $material) {
            return redirect()->to(site_url('master/raw-materials'))
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        $payload = $this->payloadFromRequest('update', $material);

        $this->rawModel->update($id, $payload);

        if ($this->auditCreateUpdate) {
            $this->logRawMaterialChange($id, 'update', $payload);
        }

        return redirect()->to(site_url('master/raw-materials'))
            ->with('message', 'Bahan baku berhasil diperbarui.');
    }

    /**
     * Hapus bahan baku.
     *
     * Catatan:
     * - Default TIDAK audit delete (untuk jaga perilaku lama)
     * - Kalau mau: ubah $auditDelete = true.
     */
    public function delete(int $id)
    {
        $material = $this->rawModel->find($id);
        if (! $material) {
            return redirect()->to(site_url('master/raw-materials'))
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        if ($this->auditDelete) {
            // snapshot sebelum delete supaya ada jejak
            $this->logRawMaterialChange($id, 'delete', [
                'snapshot' => $material,
            ]);
        }

        $this->rawModel->delete($id);

        return redirect()->to(site_url('master/raw-materials'))
            ->with('message', 'Bahan baku berhasil dihapus.');
    }

    /**
     * Rules validasi (dipakai store & update).
     * - create: menerima initial_stock & initial_cost
     * - update: menerima current_stock
     *
     * NOTE: numeric + greater_than_equal_to[0] untuk mencegah nilai minus.
     */
    private function rules(string $mode = 'create'): array
    {
        $base = [
            'name'      => 'required|min_length[3]',
            'unit_id'   => 'required|integer',
            'min_stock' => 'permit_empty|numeric|greater_than_equal_to[0]',
        ];

        if ($mode === 'create') {
            $base['initial_stock'] = 'permit_empty|numeric|greater_than_equal_to[0]';
            $base['initial_cost']  = 'permit_empty|numeric|greater_than_equal_to[0]';
        } else {
            $base['current_stock'] = 'permit_empty|numeric|greater_than_equal_to[0]';
        }

        return $base;
    }

    /**
     * Ambil payload dari POST untuk insert/update.
     * Tujuan: menghindari duplikasi mapping.
     *
     * - create:
     *   - current_stock = initial_stock
     *   - cost_last = initial_cost
     *   - cost_avg = (initial_stock > 0) ? initial_cost : 0
     * - update:
     *   - current_stock tetap bisa diedit
     *   - kalau current_stock kosong => fallback ke nilai existing
     */
    private function payloadFromRequest(string $mode = 'create', ?array $existing = null): array
    {
        $name     = trim((string) $this->request->getPost('name'));
        $unitId   = (int) $this->request->getPost('unit_id');
        $minStock = (float) ($this->request->getPost('min_stock') ?: 0);

        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if ($mode === 'create') {
            $initialStock = (float) ($this->request->getPost('initial_stock') ?: 0);
            $initialCost  = (float) ($this->request->getPost('initial_cost') ?: 0);

            return [
                'name'          => $name,
                'unit_id'       => $unitId,
                'current_stock' => $initialStock,
                'min_stock'     => $minStock,
                'cost_last'     => $initialCost,
                'cost_avg'      => $initialStock > 0 ? $initialCost : 0,
                'is_active'     => $isActive,
            ];
        }

        // update
        $fallbackCurrent = (float) ($existing['current_stock'] ?? 0);
        $postedCurrent   = $this->request->getPost('current_stock');

        $currentStock = ($postedCurrent === null || $postedCurrent === '')
            ? $fallbackCurrent
            : (float) $postedCurrent;

        return [
            'name'          => $name,
            'unit_id'       => $unitId,
            'min_stock'     => $minStock,
            'current_stock' => $currentStock,
            'is_active'     => $isActive,
        ];
    }

    /**
     * Audit log perubahan bahan baku (create/update/delete optional).
     */
    private function logRawMaterialChange(int $materialId, string $action, array $payload): void
    {
        $userId = (int) (session('user_id') ?? 0);

        $this->auditLogModel->insert([
            'entity_type' => 'raw_material',
            'entity_id'   => $materialId,
            'action'      => $action,
            'description' => 'Raw Material ' . $action . ' #' . $materialId,
            'payload'     => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'user_id'     => $userId > 0 ? $userId : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
