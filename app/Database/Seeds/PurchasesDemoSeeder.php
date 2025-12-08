<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PurchasesDemoSeeder extends Seeder
{
    public function run()
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        $supplier1 = $db->table('suppliers')->where('name', 'PT Maju Jaya')->get()->getRow('id');
        if (! $supplier1) {
            $db->table('suppliers')->insert([
                'name'       => 'PT Maju Jaya',
                'phone'      => '0812-3456-7890',
                'address'    => 'Jl. Kopi No. 1, Jakarta',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $supplier1 = $db->insertID();
        }

        $supplier2 = $db->table('suppliers')->where('name', 'CV Bahan Sejahtera')->get()->getRow('id');
        if (! $supplier2) {
            $db->table('suppliers')->insert([
                'name'       => 'CV Bahan Sejahtera',
                'phone'      => '0813-2222-3333',
                'address'    => 'Jl. Susu No. 5, Bandung',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $supplier2 = $db->insertID();
        }

        $materials = $db->table('raw_materials')->select('id, name')->get()->getResultArray();
        $matIndex = [];
        foreach ($materials as $m) {
            $matIndex[$m['name']] = $m['id'];
        }

        $purchases = [
            [
                'purchase_date' => '2025-12-07',
                'supplier_id'   => $supplier1,
                'invoice_no'    => 'PO-2025-001',
                'notes'         => 'Seed data pembelian awal',
                'items' => [
                    ['name' => 'Biji Kopi Arabica', 'qty' => 2000, 'unit_cost' => 0.12],
                    ['name' => 'Susu Fresh',        'qty' => 5000, 'unit_cost' => 0.03],
                    ['name' => 'Gula Pasir',        'qty' => 1000, 'unit_cost' => 0.02],
                ],
                'movement_time' => '08:30:00',
            ],
            [
                'purchase_date' => '2025-12-08',
                'supplier_id'   => $supplier2,
                'invoice_no'    => 'PO-2025-002',
                'notes'         => 'Seed data pembelian kedua',
                'items' => [
                    ['name' => 'Bubuk Cokelat',   'qty' => 2000, 'unit_cost' => 0.05],
                    ['name' => 'Kentang',         'qty' => 5000, 'unit_cost' => 0.01],
                    ['name' => 'Minyak Goreng',   'qty' => 3000, 'unit_cost' => 0.02],
                ],
                'movement_time' => '09:15:00',
            ],
        ];

        foreach ($purchases as $po) {
            $existing = $db->table('purchases')->where('invoice_no', $po['invoice_no'])->countAllResults();
            if ($existing > 0) {
                continue;
            }

            $total = 0;
            foreach ($po['items'] as $it) {
                $total += $it['qty'] * $it['unit_cost'];
            }

            $purchaseId = $db->table('purchases')->insert([
                'supplier_id'   => $po['supplier_id'],
                'purchase_date' => $po['purchase_date'],
                'invoice_no'    => $po['invoice_no'],
                'total_amount'  => $total,
                'notes'         => $po['notes'],
                'created_at'    => $now,
                'updated_at'    => $now,
            ], true);

            foreach ($po['items'] as $it) {
                $rawId = $matIndex[$it['name']] ?? null;
                if (! $rawId) {
                    continue;
                }

                $qty       = (float) $it['qty'];
                $unitCost  = (float) $it['unit_cost'];
                $totalCost = $qty * $unitCost;

                $db->table('purchase_items')->insert([
                    'purchase_id'     => $purchaseId,
                    'raw_material_id' => $rawId,
                    'qty'             => $qty,
                    'unit_cost'       => $unitCost,
                    'total_cost'      => $totalCost,
                ]);

                // Update stok + costing (weighted average sederhana)
                $rm = $db->table('raw_materials')->where('id', $rawId)->get()->getRowArray();
                $prevStock = (float) ($rm['current_stock'] ?? 0);
                $prevAvg   = (float) ($rm['cost_avg'] ?? 0);
                $newStock  = $prevStock + $qty;
                if ($newStock <= 0 || $prevStock <= 0) {
                    $newAvg = $unitCost;
                } else {
                    $newAvg = (($prevStock * $prevAvg) + $totalCost) / $newStock;
                }

                $db->table('raw_materials')->where('id', $rawId)->update([
                    'current_stock' => $newStock,
                    'cost_last'     => $unitCost,
                    'cost_avg'      => $newAvg,
                    'updated_at'    => $now,
                ]);

                // Stock movement IN
                $db->table('stock_movements')->insert([
                    'raw_material_id' => $rawId,
                    'movement_type'   => 'IN',
                    'qty'             => $qty,
                    'ref_type'        => 'purchase',
                    'ref_id'          => $purchaseId,
                    'note'            => 'Seed purchase ' . $po['invoice_no'],
                    'created_at'      => $po['purchase_date'] . ' ' . $po['movement_time'],
                ]);
            }
        }
    }
}
