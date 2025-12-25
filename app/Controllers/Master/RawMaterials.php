<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\RawMaterialModel;
use App\Models\RawMaterialVariantModel;
use App\Models\BrandModel;
use App\Models\UnitModel;
use App\Models\AuditLogModel;

class RawMaterials extends BaseController
{
    protected RawMaterialModel $rawModel;
    protected RawMaterialVariantModel $variantModel;
    protected BrandModel $brandModel;
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
        $this->variantModel  = new RawMaterialVariantModel();
        $this->brandModel    = new BrandModel();
        $this->unitModel     = new UnitModel();
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * List semua bahan baku beserta unit (untuk halaman index).
     */
    public function index()
    {
        $materials = $this->rawModel
            ->select('raw_materials.*, units.name AS unit_name, units.short_name AS unit_short, brands.name AS brand_name')
            ->join('units', 'units.id = raw_materials.unit_id', 'left')
            ->join('brands', 'brands.id = raw_materials.brand_id', 'left')
            ->orderBy('raw_materials.name', 'ASC')
            ->findAll();

        $variantsByMaterial = [];
        if (! empty($materials)) {
            $materialIds = array_column($materials, 'id');
            $variantRows = $this->variantModel
                ->select('raw_material_variants.*, brands.name AS brand_name')
                ->join('brands', 'brands.id = raw_material_variants.brand_id', 'left')
                ->whereIn('raw_material_variants.raw_material_id', $materialIds)
                ->where('raw_material_variants.is_active', 1)
                ->orderBy('brands.name', 'ASC')
                ->orderBy('raw_material_variants.variant_name', 'ASC')
                ->findAll();

            foreach ($variantRows as $row) {
                $materialId = (int) ($row['raw_material_id'] ?? 0);
                if ($materialId <= 0) {
                    continue;
                }
                if (! isset($variantsByMaterial[$materialId])) {
                    $variantsByMaterial[$materialId] = [];
                }
                $variantsByMaterial[$materialId][] = $row;
            }
        }

        return view('master/raw_materials_index', [
            'title'     => 'Master Bahan Baku',
            'subtitle'  => 'Daftar bahan baku untuk resep & stok',
            'materials' => $materials,
            'variantsByMaterial' => $variantsByMaterial,
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
            'variants'   => [],
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

        $hasVariants = $this->request->getPost('has_variants') ? 1 : 0;
        if ($hasVariants === 1 && ! $this->hasValidVariantRows()) {
            return redirect()->back()
                ->with('errors', ['Minimal satu baris varian harus diisi jika bahan memiliki varian.'])
                ->withInput();
        }

        $payload = $this->payloadFromRequest('create');

        $this->rawModel->insert($payload);
        $newId = (int) $this->rawModel->getInsertID();

        if ($hasVariants === 1) {
            $this->syncVariants($newId);
            $this->recalculateParentStock($newId);
        }

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
        $material = $this->rawModel
            ->select('raw_materials.*, brands.name AS brand_name')
            ->join('brands', 'brands.id = raw_materials.brand_id', 'left')
            ->where('raw_materials.id', $id)
            ->first();
        if (! $material) {
            return redirect()->to(site_url('master/raw-materials'))
                ->with('error', 'Bahan baku tidak ditemukan.');
        }

        return view('master/raw_materials_form', [
            'title'      => 'Edit Bahan Baku',
            'subtitle'   => 'Ubah data bahan baku',
            'units'      => $this->unitModel->getForDropdown(),
            'material'   => $material,
            'variants'   => $this->variantModel
                ->select('raw_material_variants.*, brands.name AS brand_name')
                ->join('brands', 'brands.id = raw_material_variants.brand_id', 'left')
                ->where('raw_material_variants.raw_material_id', $id)
                ->orderBy('brands.name', 'ASC')
                ->orderBy('raw_material_variants.variant_name', 'ASC')
                ->findAll(),
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

        $hasVariants = $this->request->getPost('has_variants') ? 1 : 0;
        if ($hasVariants === 1 && ! $this->hasValidVariantRows()) {
            return redirect()->back()
                ->with('errors', ['Minimal satu baris varian harus diisi jika bahan memiliki varian.'])
                ->withInput();
        }

        $payload = $this->payloadFromRequest('update', $material);

        $this->rawModel->update($id, $payload);
        if ($hasVariants === 1) {
            $this->syncVariants($id);
            $this->recalculateParentStock($id);
        }

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
            'qty_precision' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[3]',
            'min_stock' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'has_variants' => 'permit_empty|in_list[0,1]',
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
        $hasVariants = $this->request->getPost('has_variants') ? 1 : 0;
        $precision = (int) ($this->request->getPost('qty_precision') ?? 0);
        if ($precision < 0) {
            $precision = 0;
        }
        if ($precision > 3) {
            $precision = 3;
        }

        $isActive = $this->request->getPost('is_active') ? 1 : 0;
        $brandId = null;
        if ($hasVariants === 0) {
            $brandName = trim((string) $this->request->getPost('brand_name'));
            if ($brandName !== '') {
                $brandId = $this->findOrCreateBrand($brandName);
            }
        }

        if ($mode === 'create') {
            $initialStock = (float) ($this->request->getPost('initial_stock') ?: 0);
            $initialCost  = (float) ($this->request->getPost('initial_cost') ?: 0);

            return [
                'name'          => $name,
                'unit_id'       => $unitId,
                'qty_precision' => $precision,
                'has_variants'  => $hasVariants,
                'brand_id'      => $brandId,
                'current_stock' => $hasVariants ? 0 : $initialStock,
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
            'qty_precision' => $precision,
            'has_variants'  => $hasVariants,
            'brand_id'      => $brandId,
            'min_stock'     => $minStock,
            'current_stock' => $hasVariants ? $fallbackCurrent : $currentStock,
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

    private function syncVariants(int $materialId): void
    {
        $rows = $this->request->getPost('variants');
        if (! is_array($rows)) {
            return;
        }

        $existing = $this->variantModel
            ->where('raw_material_id', $materialId)
            ->findAll();

        $existingMap = [];
        foreach ($existing as $row) {
            $existingMap[(int) $row['id']] = $row;
        }

        foreach ($rows as $row) {
            $variantId = (int) ($row['id'] ?? 0);
            $brandName = trim((string) ($row['brand_name'] ?? ''));
            $name = trim((string) ($row['variant_name'] ?? ''));
            $sku  = trim((string) ($row['sku_code'] ?? ''));
            $isActive = ! empty($row['is_active']) ? 1 : 0;
            $currentStock = (float) ($row['current_stock'] ?? 0);
            $minStock = (float) ($row['min_stock'] ?? 0);

            if ($brandName === '' || $name === '') {
                continue;
            }

            $brandId = $this->findOrCreateBrand($brandName);
            if ($brandId <= 0) {
                continue;
            }

            $payload = [
                'brand_id'     => $brandId,
                'variant_name' => $name,
                'sku_code'     => $sku !== '' ? $sku : null,
                'current_stock'=> $currentStock,
                'min_stock'    => $minStock,
                'is_active'    => $isActive,
            ];

            if ($variantId > 0 && isset($existingMap[$variantId])) {
                $this->variantModel->update($variantId, $payload);
            } else {
                $payload['raw_material_id'] = $materialId;
                $this->variantModel->insert($payload);
            }
        }
    }

    private function recalculateParentStock(int $materialId): void
    {
        $total = $this->variantModel
            ->selectSum('current_stock', 'total_stock')
            ->where('raw_material_id', $materialId)
            ->get()
            ->getRowArray();

        $stock = (float) ($total['total_stock'] ?? 0);
        $this->rawModel->update($materialId, ['current_stock' => $stock]);
    }

    private function hasValidVariantRows(): bool
    {
        $rows = $this->request->getPost('variants');
        if (! is_array($rows)) {
            return false;
        }

        foreach ($rows as $row) {
            $brandName = trim((string) ($row['brand_name'] ?? ''));
            $name = trim((string) ($row['variant_name'] ?? ''));
            if ($brandName !== '' && $name !== '') {
                return true;
            }
        }

        return false;
    }

    private function findOrCreateBrand(string $name): int
    {
        $name = trim($name);
        if ($name === '') {
            return 0;
        }

        $existing = $this->brandModel->where('name', $name)->first();
        if ($existing) {
            return (int) ($existing['id'] ?? 0);
        }

        return (int) $this->brandModel->insert([
            'name'      => $name,
            'is_active' => 1,
        ], true);
    }
}
