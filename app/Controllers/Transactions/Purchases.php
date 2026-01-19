<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\PurchaseModel;
use App\Models\PurchaseItemModel;
use App\Models\SupplierModel;
use App\Models\RawMaterialModel;
use App\Models\RawMaterialVariantModel;
use App\Models\StockMovementModel;
use App\Services\AuditLogService;

class Purchases extends BaseController
{
    protected PurchaseModel $purchaseModel;
    protected PurchaseItemModel $itemModel;
    protected SupplierModel $supplierModel;
    protected RawMaterialModel $rawModel;
    protected RawMaterialVariantModel $variantModel;
    protected StockMovementModel $movementModel;
    protected AuditLogService $auditService;

    public function __construct()
    {
        $this->purchaseModel = new PurchaseModel();
        $this->itemModel     = new PurchaseItemModel();
        $this->supplierModel = new SupplierModel();
        $this->rawModel      = new RawMaterialModel();
        $this->variantModel  = new RawMaterialVariantModel();
        $this->movementModel = new StockMovementModel();
        $this->auditService  = new AuditLogService();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('purchases p')
            ->select('p.*, s.name AS supplier_name')
            ->join('suppliers s', 's.id = p.supplier_id', 'left')
            ->orderBy('p.purchase_date', 'DESC')
            ->orderBy('p.id', 'DESC');

        $purchases = $builder->get()->getResultArray();

        $data = [
            'title'     => 'Pembelian Bahan Baku',
            'subtitle'  => 'Riwayat pembelian',
            'purchases' => $purchases,
        ];

        return view('transactions/purchases_index', $data);
    }

    public function create()
    {
        $suppliers = $this->supplierModel->getActive();
        $materials = $this->rawModel
            ->withUnit()
            ->where('raw_materials.is_active', 1)
            ->orderBy('raw_materials.name', 'ASC')
            ->findAll();

        $variants = $this->variantModel
            ->select('raw_material_variants.*, raw_materials.name AS raw_material_name, units.short_name AS unit_short, brands.name AS brand_name')
            ->join('raw_materials', 'raw_materials.id = raw_material_variants.raw_material_id', 'left')
            ->join('brands', 'brands.id = raw_material_variants.brand_id', 'left')
            ->join('units', 'units.id = raw_materials.unit_id', 'left')
            ->where('raw_material_variants.is_active', 1)
            ->where('raw_materials.is_active', 1)
            ->orderBy('raw_materials.name', 'ASC')
            ->orderBy('brands.name', 'ASC')
            ->orderBy('raw_material_variants.variant_name', 'ASC')
            ->findAll();

        $db = \Config\Database::connect();
        $brands = $db->table('raw_material_variants rmv')
            ->distinct()
            ->select('b.id, b.name, rmv.raw_material_id')
            ->join('brands b', 'b.id = rmv.brand_id', 'left')
            ->join('raw_materials rm', 'rm.id = rmv.raw_material_id', 'left')
            ->where('rmv.is_active', 1)
            ->where('rm.is_active', 1)
            ->where('b.is_active', 1)
            ->orderBy('b.name', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title'     => 'Tambah Pembelian',
            'subtitle'  => 'Input pembelian bahan baku',
            'suppliers' => $suppliers,
            'materials' => $materials,
            'variants'  => $variants,
            'brands'    => $brands,
        ];

        return view('transactions/purchases_form', $data);
    }

    public function store()
    {
        $rules = [
            'supplier_id'   => 'required|integer',
            'purchase_date' => 'required|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $itemsInput = $this->request->getPost('items') ?? [];
        $items      = [];
        $variantIds = [];
        $rawIds     = [];

        // Bersihkan item kosong
        foreach ($itemsInput as $row) {
            $rawId = (int) ($row['raw_material_id'] ?? 0);
            $variantId = (int) ($row['raw_material_variant_id'] ?? 0);
            $qty   = (float) ($row['qty'] ?? 0);
            $cost  = (float) ($row['unit_cost'] ?? 0);

            if ($rawId > 0 && $qty > 0 && $cost >= 0) {
                $items[] = [
                    'raw_material_id' => $rawId,
                    'raw_material_variant_id' => $variantId > 0 ? $variantId : null,
                    'qty'             => $qty,
                    'unit_cost'       => $cost,
                    'total_cost'      => $qty * $cost,
                ];
                if ($variantId > 0) {
                    $variantIds[$variantId] = true;
                }
                $rawIds[$rawId] = true;
            }
        }

        if (empty($items)) {
            return redirect()->back()
                ->with('errors', ['items' => 'Minimal satu baris item pembelian diisi.'])
                ->withInput();
        }

        $db = \Config\Database::connect();
        $variantMap = [];
        if (! empty($variantIds)) {
            $variantRows = $this->variantModel
                ->select('raw_material_variants.id, raw_material_variants.raw_material_id')
                ->whereIn('raw_material_variants.id', array_keys($variantIds))
                ->findAll();

            foreach ($variantRows as $row) {
                $variantMap[(int) $row['id']] = (int) ($row['raw_material_id'] ?? 0);
            }
        }

        $rawVariantFlags = [];
        if (! empty($rawIds)) {
            $rawRows = $this->rawModel
                ->select('id, has_variants')
                ->whereIn('id', array_keys($rawIds))
                ->findAll();

            foreach ($rawRows as $row) {
                $rawVariantFlags[(int) $row['id']] = (int) ($row['has_variants'] ?? 0);
            }
        }

        $errors = [];
        foreach ($items as &$item) {
            $variantId = (int) ($item['raw_material_variant_id'] ?? 0);
            if ($variantId <= 0) {
                $rawId = (int) ($item['raw_material_id'] ?? 0);
                if (($rawVariantFlags[$rawId] ?? 0) === 1) {
                    $errors[] = 'Varian wajib dipilih untuk bahan baku yang memiliki varian.';
                }
                continue;
            }
            $rawId = (int) ($item['raw_material_id'] ?? 0);
            $variantRawId = (int) ($variantMap[$variantId] ?? 0);
            if ($variantRawId <= 0 || $variantRawId !== $rawId) {
                $errors[] = 'Varian tidak sesuai bahan baku yang dipilih.';
                $item['raw_material_variant_id'] = null;
            }
        }
        unset($item);

        if (! empty($errors)) {
            return redirect()->back()
                ->with('errors', $errors)
                ->withInput();
        }

        $db->transStart();

        // Hitung total
        $total = array_sum(array_column($items, 'total_cost'));

        // Insert header
        $purchaseData = [
            'supplier_id'   => (int) $this->request->getPost('supplier_id'),
            'purchase_date' => $this->request->getPost('purchase_date'),
            'invoice_no'    => $this->request->getPost('invoice_no') ?: null,
            'total_amount'  => $total,
            'notes'         => $this->request->getPost('notes') ?: null,
        ];

        $purchaseId = $this->purchaseModel->insert($purchaseData, true);

        // Insert items + update stok / cost + catat stock movement
        foreach ($items as $item) {
            $item['purchase_id'] = $purchaseId;
            $this->itemModel->insert($item);

            // Update stok & cost raw material
            $material = $this->rawModel->find($item['raw_material_id']);
            if ($material) {
                $prevStock = (float) ($material['current_stock'] ?? 0);
                $prevAvg   = (float) ($material['cost_avg'] ?? 0);

                $newQty   = (float) $item['qty'];
                $unitCost = (float) $item['unit_cost'];

                $newStock = $prevStock + $newQty;

                if ($newStock <= 0) {
                    // Edge case sangat jarang, tapi untuk jaga-jaga
                    $newAvg = $unitCost;
                } elseif ($prevStock <= 0) {
                    // Jika stok sebelumnya nol atau negatif, pakai harga baru
                    $newAvg = $unitCost;
                } else {
                    // Weighted average cost
                    $totalValueBefore = $prevStock * $prevAvg;
                    $totalValueNew    = $newQty * $unitCost;
                    $newAvg           = ($totalValueBefore + $totalValueNew) / $newStock;
                }

                $hasVariants = (int) ($material['has_variants'] ?? 0) === 1;
                if ($hasVariants) {
                    $this->updateVariantStock(
                        (int) ($item['raw_material_variant_id'] ?? 0),
                        $newQty
                    );
                    $this->rawModel->update($material['id'], [
                        'cost_last' => $unitCost,
                        'cost_avg'  => $newAvg,
                    ]);
                    $this->recalculateParentStock($material['id']);
                } else {
                    $this->rawModel->update($material['id'], [
                        'current_stock' => $newStock,
                        'cost_last'     => $unitCost,
                        'cost_avg'      => $newAvg,
                    ]);
                }

                // Catat pergerakan stok (IN)
                $this->movementModel->insert([
                    'raw_material_id' => $material['id'],
                    'raw_material_variant_id' => $hasVariants ? ($item['raw_material_variant_id'] ?? null) : null,
                    'movement_type'   => 'IN',
                    'qty'             => $newQty,
                    'ref_type'        => 'purchase',
                    'ref_id'          => $purchaseId,
                    'note'            => 'Pembelian dari supplier ID ' . $purchaseData['supplier_id'],
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pembelian.')
                ->withInput();
        }

        // Log purchase creation
        $supplier = $this->supplierModel->find($purchaseData['supplier_id']);
        $this->auditService->log('purchase', 'create', (int) $purchaseId, [
            'purchase_date' => $purchaseData['purchase_date'],
            'invoice_no'    => $purchaseData['invoice_no'],
            'supplier_id'   => $purchaseData['supplier_id'],
            'supplier_name' => $supplier['name'] ?? null,
            'total_amount'  => $total,
            'items_count'   => count($items),
        ], 'Purchase created: ' . ($purchaseData['invoice_no'] ?? '#' . $purchaseId));

        return redirect()->to(site_url('purchases'))
            ->with('message', 'Pembelian berhasil disimpan.');
    }

    public function detail(int $id)
    {
        $db = \Config\Database::connect();

        $header = $db->table('purchases p')
            ->select('p.*, s.name AS supplier_name, s.phone, s.address')
            ->join('suppliers s', 's.id = p.supplier_id', 'left')
            ->where('p.id', $id)
            ->get()->getRowArray();

        if (! $header) {
            return redirect()->to(site_url('purchases'))
                ->with('error', 'Data pembelian tidak ditemukan.');
        }

        $items = $db->table('purchase_items i')
            ->select('i.*, r.name AS material_name, u.short_name AS unit_short, rmv.variant_name AS variant_name, b.name AS brand_name')
            ->join('raw_materials r', 'r.id = i.raw_material_id', 'left')
            ->join('units u', 'u.id = r.unit_id', 'left')
            ->join('raw_material_variants rmv', 'rmv.id = i.raw_material_variant_id', 'left')
            ->join('brands b', 'b.id = rmv.brand_id', 'left')
            ->where('i.purchase_id', $id)
            ->get()->getResultArray();

        $data = [
            'title'    => 'Detail Pembelian',
            'subtitle' => 'Rincian pembelian bahan baku',
            'header'   => $header,
            'items'    => $items,
        ];

        return view('transactions/purchases_detail', $data);
    }

    private function updateVariantStock(int $variantId, float $qty): void
    {
        if ($variantId <= 0 || $qty <= 0) {
            return;
        }

        $variant = $this->variantModel->find($variantId);
        if (! $variant) {
            return;
        }

        $current = (float) ($variant['current_stock'] ?? 0);
        $newStock = $current + $qty;

        $this->variantModel->update($variantId, [
            'current_stock' => $newStock,
        ]);
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
}
