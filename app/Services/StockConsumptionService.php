<?php

namespace App\Services;

use App\Models\RecipeModel;
use App\Models\RawMaterialModel;
use App\Models\RawMaterialVariantModel;
use App\Models\StockMovementModel;

class StockConsumptionService
{
    protected RecipeModel $recipeModel;
    protected RawMaterialModel $rawModel;
    protected RawMaterialVariantModel $variantModel;
    protected StockMovementModel $movementModel;

    public function __construct()
    {
        $this->recipeModel   = new RecipeModel();
        $this->rawModel      = new RawMaterialModel();
        $this->variantModel  = new RawMaterialVariantModel();
        $this->movementModel = new StockMovementModel();
    }
    /**
     * Konsumsi stok untuk 1 transaksi sale (resep + opsi menu).
     * Accepts an optional database connection so it can participate in an
     * external transaction (recommended).
     *
     * @param int $saleId
     * @param \CodeIgniter\Database\BaseConnection|null $db
     * @return array{ok:bool,errors:string[]}
     */
    public function consumeForOrder(int $saleId, ?\CodeIgniter\Database\BaseConnection $db = null): array
    {
        $db = $db ?? \Config\Database::connect();

        $items = $db->table('sale_items')
            ->where('sale_id', $saleId)
            ->get()
            ->getResultArray();

        if (empty($items)) {
            return [
                'ok' => false,
                'errors' => ['Tidak ada item transaksi untuk diproses.'],
            ];
        }

        $saleItemIds = array_map(static fn($row) => (int) $row['id'], $items);
        $menuIds     = array_values(array_unique(array_map(static fn($row) => (int) $row['menu_id'], $items)));

        $groupsByMenu = [];
        $groupIds     = [];
        if (! empty($menuIds)) {
            $groupRows = $db->table('menu_option_groups')
                ->whereIn('menu_id', $menuIds)
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($groupRows as $row) {
                $menuId = (int) ($row['menu_id'] ?? 0);
                $groupId = (int) ($row['id'] ?? 0);
                if ($menuId <= 0 || $groupId <= 0) {
                    continue;
                }
                if (! isset($groupsByMenu[$menuId])) {
                    $groupsByMenu[$menuId] = [];
                }
                $groupsByMenu[$menuId][$groupId] = $row;
                $groupIds[$groupId] = true;
            }
        }

        $optionById = [];
        if (! empty($groupIds)) {
            $optionRows = $db->table('menu_options')
                ->whereIn('group_id', array_keys($groupIds))
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($optionRows as $row) {
                $optionId = (int) ($row['id'] ?? 0);
                if ($optionId <= 0) {
                    continue;
                }
                $optionById[$optionId] = $row;
            }
        }

        $optionsBySaleItem = [];
        if (! empty($saleItemIds)) {
            $optionRows = $db->table('sale_item_options')
                ->whereIn('sale_item_id', $saleItemIds)
                ->get()
                ->getResultArray();

            foreach ($optionRows as $row) {
                $saleItemId = (int) ($row['sale_item_id'] ?? 0);
                if ($saleItemId <= 0) {
                    continue;
                }
                if (! isset($optionsBySaleItem[$saleItemId])) {
                    $optionsBySaleItem[$saleItemId] = [];
                }
                $optionsBySaleItem[$saleItemId][] = $row;
            }
        }

        $errors = [];

        foreach ($items as $item) {
            $menuId  = (int) ($item['menu_id'] ?? 0);
            $itemId  = (int) ($item['id'] ?? 0);
            $menuGroups = $groupsByMenu[$menuId] ?? [];
            if (empty($menuGroups)) {
                continue;
            }

            $selected = $optionsBySaleItem[$itemId] ?? [];
            $counts = [];

            foreach ($selected as $sel) {
                $optionId = (int) ($sel['option_id'] ?? 0);
                $opt = $optionById[$optionId] ?? null;
                if (! $opt) {
                    $errors[] = 'Opsi tidak valid pada transaksi #' . $saleId . '.';
                    continue;
                }
                $groupId = (int) ($opt['group_id'] ?? 0);
                if (! isset($menuGroups[$groupId])) {
                    $errors[] = 'Opsi tidak sesuai menu pada transaksi #' . $saleId . '.';
                    continue;
                }
                $counts[$groupId] = ($counts[$groupId] ?? 0) + 1;
            }

            foreach ($menuGroups as $groupId => $group) {
                $min = (int) ($group['min_select'] ?? 0);
                $max = (int) ($group['max_select'] ?? 0);
                if ($min <= 0 && (int) ($group['is_required'] ?? 0) === 1) {
                    $min = 1;
                }

                $selectedCount = (int) ($counts[$groupId] ?? 0);
                if ($min > 0 && $selectedCount < $min) {
                    $errors[] = 'Opsi wajib belum lengkap untuk transaksi #' . $saleId . '.';
                }
                if ($max > 0 && $selectedCount > $max) {
                    $errors[] = 'Jumlah opsi melebihi batas untuk transaksi #' . $saleId . '.';
                }
            }
        }

        if (! empty($errors)) {
            return [
                'ok' => false,
                'errors' => $errors,
            ];
        }

        $variantIds = [];
        foreach ($optionsBySaleItem as $selections) {
            foreach ($selections as $sel) {
                $variantId = (int) ($sel['variant_id_snapshot'] ?? 0);
                if ($variantId <= 0) {
                    $opt = $optionById[(int) ($sel['option_id'] ?? 0)] ?? null;
                    $variantId = (int) ($opt['variant_id'] ?? 0);
                }
                if ($variantId > 0) {
                    $variantIds[$variantId] = true;
                }
            }
        }

        $variantMap = [];
        if (! empty($variantIds)) {
            $variantRows = $db->table('raw_material_variants rmv')
                ->select('rmv.id, rmv.raw_material_id, rm.name AS raw_material_name')
                ->join('raw_materials rm', 'rm.id = rmv.raw_material_id', 'left')
                ->whereIn('rmv.id', array_keys($variantIds))
                ->get()
                ->getResultArray();

            foreach ($variantRows as $row) {
                $variantId = (int) ($row['id'] ?? 0);
                if ($variantId <= 0) {
                    continue;
                }
                $variantMap[$variantId] = $row;
            }
        }

        foreach ($items as $item) {
            $menuId = (int) ($item['menu_id'] ?? 0);
            $qty    = (float) ($item['qty'] ?? 0);
            $itemId = (int) ($item['id'] ?? 0);

            $hppData = $this->recipeModel->calculateHppForMenu($menuId);
            if (! $hppData || empty($hppData['raw_breakdown'])) {
                $selections = $optionsBySaleItem[$itemId] ?? [];
                if (empty($selections)) {
                    $errors[] = 'Resep menu tidak lengkap pada transaksi #' . $saleId . '.';
                    continue;
                }
                $hppData['raw_breakdown'] = [];
            }

            $recipe   = $hppData['recipe'] ?? [];
            $yieldQty = (float) ($recipe['yield_qty'] ?? 1);
            if ($yieldQty <= 0) {
                $yieldQty = 1;
            }

            $factor = $qty / $yieldQty;
            foreach ($hppData['raw_breakdown'] as $rawId => $qtyPerBatch) {
                $qtyToDeduct = $this->roundQty((float) $qtyPerBatch * $factor);
                if ($qtyToDeduct <= 0) {
                    continue;
                }
                $result = $this->deductRawMaterial(
                    (int) $rawId,
                    $qtyToDeduct,
                    'sale',
                    $saleId,
                    'Penjualan menu ID ' . $menuId . ' (sale_item ' . $itemId . ')'
                );
                if (! $result['ok']) {
                    $errors[] = $result['error'];
                }
            }

            $selections = $optionsBySaleItem[$itemId] ?? [];
            foreach ($selections as $sel) {
                $optionId = (int) ($sel['option_id'] ?? 0);
                $opt = $optionById[$optionId] ?? null;
                if (! $opt) {
                    $errors[] = 'Opsi tidak valid pada transaksi #' . $saleId . '.';
                    continue;
                }

                $variantId = (int) ($sel['variant_id_snapshot'] ?? 0);
                if ($variantId <= 0) {
                    $variantId = (int) ($opt['variant_id'] ?? 0);
                }
                if ($variantId <= 0 || ! isset($variantMap[$variantId])) {
                    $errors[] = 'Variant bahan baku untuk opsi tidak ditemukan pada transaksi #' . $saleId . '.';
                    continue;
                }

                $qtySelected  = (float) ($sel['qty_selected'] ?? 1);
                $qtyMultiplier= (float) ($opt['qty_multiplier'] ?? 1);
                $qtyToDeduct  = $this->roundQty($qty * $qtySelected * $qtyMultiplier);
                if ($qtyToDeduct <= 0) {
                    continue;
                }

                $note = 'Add-on ' . ($sel['option_name_snapshot'] ?? ($opt['name'] ?? 'option')) .
                    ' (sale_item ' . $itemId . ')';

                $result = $this->deductVariant(
                    $variantId,
                    $qtyToDeduct,
                    'sale',
                    $saleId,
                    $note
                );
                if (! $result['ok']) {
                    $errors[] = $result['error'];
                }
            }
        }

        if (! empty($errors)) {
            return [
                'ok' => false,
                'errors' => $errors,
            ];
        }

        return [
            'ok' => true,
            'errors' => [],
        ];
    }

    private function deductRawMaterial(int $rawId, float $qtyToDeduct, string $refType, int $refId, string $note): array
    {
        if ($rawId <= 0 || $qtyToDeduct <= 0) {
            return ['ok' => true];
        }

        $material = $this->rawModel->find($rawId);
        if (! $material) {
            return [
                'ok' => false,
                'error' => 'Bahan baku tidak ditemukan saat konsumsi stok.',
            ];
        }

        if ((int) ($material['has_variants'] ?? 0) === 1) {
            return [
                'ok' => false,
                'error' => 'Bahan baku <b>' . ($material['name'] ?? '-') .
                    '</b> memiliki varian. Pilih varian saat order agar stok bisa dipotong.',
            ];
        }

        $precision = (int) ($material['qty_precision'] ?? 0);
        if ($precision < 0) {
            $precision = 0;
        }
        if ($precision > 3) {
            $precision = 3;
        }

        $currentStock = $this->roundQty((float) ($material['current_stock'] ?? 0));
        if ($currentStock < $qtyToDeduct) {
            return [
                'ok' => false,
                'error' => 'Stok berubah saat penyimpanan. Bahan <b>' . ($material['name'] ?? '-') .
                    '</b> butuh ' . number_format($qtyToDeduct, $precision, ',', '.') . ' namun stok tersisa ' .
                    number_format($currentStock, $precision, ',', '.'),
            ];
        }

        $newStock = $this->roundQty($currentStock - $qtyToDeduct);
        if ($newStock < 0 && abs($newStock) < 0.000001) {
            $newStock = 0.0;
        }
        $this->rawModel->update($rawId, ['current_stock' => $newStock]);

        $this->movementModel->insert([
            'raw_material_id' => $rawId,
            'raw_material_variant_id' => null,
            'movement_type'   => 'OUT',
            'qty'             => $qtyToDeduct,
            'ref_type'        => $refType,
            'ref_id'          => $refId,
            'note'            => $note,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        return ['ok' => true];
    }

    private function deductVariant(int $variantId, float $qtyToDeduct, string $refType, int $refId, string $note): array
    {
        if ($variantId <= 0 || $qtyToDeduct <= 0) {
            return ['ok' => true];
        }

        $variant = $this->variantModel->find($variantId);
        if (! $variant) {
            return [
                'ok' => false,
                'error' => 'Varian bahan baku tidak ditemukan saat konsumsi stok.',
            ];
        }

        $rawId = (int) ($variant['raw_material_id'] ?? 0);
        $precision = 0;
        $material = null;
        if ($rawId > 0) {
            $material = $this->rawModel->find($rawId);
        }
        if ($material) {
            $precision = (int) ($material['qty_precision'] ?? 0);
            if ($precision < 0) {
                $precision = 0;
            }
            if ($precision > 3) {
                $precision = 3;
            }
            if ((int) ($material['has_variants'] ?? 0) === 0) {
                return $this->deductRawMaterial($rawId, $qtyToDeduct, $refType, $refId, $note);
            }
        }
        $currentStock = $this->roundQty((float) ($variant['current_stock'] ?? 0));
        if ($currentStock < $qtyToDeduct) {
            return [
                'ok' => false,
                'error' => 'Stok varian tidak mencukupi. Varian <b>' . ($variant['variant_name'] ?? '-') .
                    '</b> butuh ' . number_format($qtyToDeduct, $precision, ',', '.') . ' namun stok tersisa ' .
                    number_format($currentStock, $precision, ',', '.'),
            ];
        }

        $newStock = $this->roundQty($currentStock - $qtyToDeduct);
        if ($newStock < 0 && abs($newStock) < 0.000001) {
            $newStock = 0.0;
        }
        $this->variantModel->update($variantId, ['current_stock' => $newStock]);
        if ($rawId > 0) {
            $this->recalculateParentStock($rawId);
        }

        $this->movementModel->insert([
            'raw_material_id' => $rawId,
            'raw_material_variant_id' => $variantId,
            'movement_type'   => 'OUT',
            'qty'             => $qtyToDeduct,
            'ref_type'        => $refType,
            'ref_id'          => $refId,
            'note'            => $note,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        return ['ok' => true];
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

    private function roundQty(float $value): float
    {
        return round($value, 6);
    }
}
