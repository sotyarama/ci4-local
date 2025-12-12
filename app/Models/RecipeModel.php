<?php

namespace App\Models;

use CodeIgniter\Model;

class RecipeModel extends Model
{
    protected $table         = 'recipes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';

    protected $allowedFields = [
        'menu_id',
        'yield_qty',
        'yield_unit',
        'notes',
    ];

    protected $useTimestamps = true;

    /**
     * Hitung HPP untuk 1 menu (mendukung sub-recipe).
     */
    public function calculateHppForMenu(int $menuId): ?array
    {
        $recipe = $this->where('menu_id', $menuId)->first();
        if (! $recipe) {
            return null; // belum ada resep
        }

        $cache = [];
        return $this->computeRecipeCost((int) $recipe['id'], $cache, []);
    }

    public function getRecipeItems(int $recipeId): array
    {
        // Ambil semua baris recipe_items untuk satu resep
        return $this->db->table('recipe_items')
            ->where('recipe_id', $recipeId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Rekursi perhitungan HPP, mendukung sub-recipe dengan guard siklus.
     * Mengembalikan breakdown HPP per batch (sesuai yield_qty resep).
     */
    private function computeRecipeCost(int $recipeId, array &$cache = [], array $stack = []): ?array
    {
        if (isset($cache[$recipeId])) {
            return $cache[$recipeId];
        }

        $recipe = $this->find($recipeId);
        if (! $recipe) {
            return null;
        }

        $db = \Config\Database::connect();

        $items = $db->table('recipe_items ri')
            ->select('
                ri.*,
                COALESCE(ri.item_type, "raw") AS item_type,
                rm.name      AS material_name,
                rm.cost_avg  AS material_cost_avg,
                u.short_name AS unit_short,
                cr.id        AS child_recipe_exists,
                cm.name      AS child_menu_name
            ')
            ->join('raw_materials rm', 'rm.id = ri.raw_material_id', 'left')
            ->join('units u', 'u.id = rm.unit_id', 'left')
            ->join('recipes cr', 'cr.id = ri.child_recipe_id', 'left')
            ->join('menus cm', 'cm.id = cr.menu_id', 'left')
            ->where('ri.recipe_id', $recipeId)
            ->orderBy('ri.id', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($items)) {
            $result = [
                'recipe'        => $recipe,
                'items'         => [],
                'total_cost'    => 0,
                'hpp_per_yield' => 0,
                'raw_breakdown' => [],
                'warnings'      => [],
            ];
            $cache[$recipeId] = $result;
            return $result;
        }

        $totalCost      = 0;
        $itemsWithCost  = [];
        $rawBreakdown   = []; // per batch resep (sesuai yield_qty)
        $warnings       = [];
        $stackWithSelf  = array_merge($stack, [$recipeId]);

        foreach ($items as $row) {
            $itemType = $row['item_type'] ?? 'raw';
            $qty      = (float) ($row['qty'] ?? 0);
            $wastePct = $this->clampWastePct((float) ($row['waste_pct'] ?? 0));
            $effectiveQty = $this->roundQty($qty * (1 + $wastePct / 100.0));

            if ($effectiveQty <= 0) {
                continue;
            }

            if ($itemType === 'recipe') {
                $childId = (int) ($row['child_recipe_id'] ?? 0);

                if ($childId <= 0) {
                    $warnings[] = 'Baris sub-recipe tanpa child_recipe_id diabaikan.';
                    continue;
                }

                if (in_array($childId, $stackWithSelf, true)) {
                    $warnings[] = 'Siklus sub-resep terdeteksi, baris diabaikan.';
                    continue;
                }

                $childCost = $this->computeRecipeCost($childId, $cache, $stackWithSelf);
                if (! $childCost) {
                    $warnings[] = 'Sub-resep #' . $childId . ' tidak ditemukan, baris diabaikan.';
                    continue;
                }

                $childHpp   = (float) ($childCost['hpp_per_yield'] ?? 0);
                $lineCost   = $this->roundQty($effectiveQty * $childHpp);
                $totalCost  += $lineCost;

                foreach ($childCost['raw_breakdown'] as $rawId => $rawQtyPerBatch) {
                    if (! isset($rawBreakdown[$rawId])) {
                        $rawBreakdown[$rawId] = 0.0;
                    }
                    $rawBreakdown[$rawId] = $this->roundQty($rawBreakdown[$rawId] + ($rawQtyPerBatch * $effectiveQty));
                }

                $row['effective_qty'] = $effectiveQty;
                $row['unit_cost']     = $childHpp;
                $row['line_cost']     = $lineCost;
                $itemsWithCost[]      = $row;
            } else {
                $rawId    = (int) ($row['raw_material_id'] ?? 0);
                $unitCost = (float) ($row['material_cost_avg'] ?? 0);
                if ($rawId <= 0) {
                    continue;
                }

                $lineCost  = $this->roundQty($effectiveQty * $unitCost);
                $totalCost += $lineCost;

                if (! isset($rawBreakdown[$rawId])) {
                    $rawBreakdown[$rawId] = 0.0;
                }
                $rawBreakdown[$rawId] = $this->roundQty($rawBreakdown[$rawId] + $effectiveQty);

                $row['effective_qty'] = $effectiveQty;
                $row['unit_cost']     = $unitCost;
                $row['line_cost']     = $lineCost;
                $itemsWithCost[]      = $row;
            }
        }

        $yieldQty = (float) ($recipe['yield_qty'] ?? 1);
        if ($yieldQty <= 0) {
            $yieldQty = 1; // guard supaya tidak dibagi nol
        }

        $totalCost   = $this->roundQty($totalCost);
        $hppPerYield = $this->roundQty($totalCost / $yieldQty);

        $result = [
            'recipe'        => $recipe,
            'items'         => $itemsWithCost,
            'total_cost'    => $totalCost,
            'hpp_per_yield' => $hppPerYield,
            'raw_breakdown' => $rawBreakdown,
            'warnings'      => $warnings,
        ];

        $cache[$recipeId] = $result;

        return $result;
    }

    private function clampWastePct(float $waste): float
    {
        return max(0.0, min(100.0, $waste));
    }

    private function roundQty(float $qty): float
    {
        return round($qty, 6);
    }
}
