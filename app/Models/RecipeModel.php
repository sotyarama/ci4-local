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
     * Hitung HPP untuk 1 menu berdasarkan resep & cost_avg bahan baku.
     *
     * @param int $menuId
     * @return array|null
     *
     * Return contoh:
     * [
     *   'recipe'        => [...],
     *   'items'         => [...],              // daftar bahan + cost line
     *   'total_cost'    => 1234.56,           // total biaya 1 batch resep
     *   'hpp_per_yield' => 12.34,             // HPP per yield (mis: per porsi)
     * ]
     */
    public function calculateHppForMenu(int $menuId): ?array
    {
        $db = \Config\Database::connect();

        // Ambil resep untuk menu ini
        $recipe = $this->where('menu_id', $menuId)->first();
        if (! $recipe) {
            return null; // belum ada resep
        }

        // Ambil item + cost_avg bahan
        $builder = $db->table('recipe_items ri')
            ->select('
                ri.*,
                rm.name       AS material_name,
                rm.cost_avg   AS material_cost_avg,
                u.short_name  AS unit_short
            ')
            ->join('raw_materials rm', 'rm.id = ri.raw_material_id', 'left')
            ->join('units u', 'u.id = rm.unit_id', 'left')
            ->where('ri.recipe_id', $recipe['id']);

        $items = $builder->get()->getResultArray();

        if (empty($items)) {
            return [
                'recipe'        => $recipe,
                'items'         => [],
                'total_cost'    => 0,
                'hpp_per_yield' => 0,
            ];
        }

        $totalCost = 0;
        $itemsWithCost = [];

        foreach ($items as $row) {
            $qty       = (float) ($row['qty'] ?? 0);
            $wastePct  = (float) ($row['waste_pct'] ?? 0);
            $unitCost  = (float) ($row['material_cost_avg'] ?? 0);

            // Qty efektif dengan waste (versi sederhana: qty × (1 + waste%))
            $effectiveQty = $qty * (1 + ($wastePct / 100.0));

            $lineCost = $effectiveQty * $unitCost;
            $totalCost += $lineCost;

            $row['effective_qty'] = $effectiveQty;
            $row['unit_cost']     = $unitCost;
            $row['line_cost']     = $lineCost;

            $itemsWithCost[] = $row;
        }

        $yieldQty = (float) ($recipe['yield_qty'] ?? 1);
        if ($yieldQty <= 0) {
            $yieldQty = 1; // guard supaya tidak dibagi nol
        }

        $hppPerYield = $totalCost / $yieldQty;

        return [
            'recipe'        => $recipe,
            'items'         => $itemsWithCost,
            'total_cost'    => $totalCost,
            'hpp_per_yield' => $hppPerYield,
        ];
    }

}
