<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Laporan Pembelian per Bahan</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Bandingkan harga, kuantitas, dan total pembelian per bahan per supplier.
            </p>
        </div>
    </div>

    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">
        <div style="display:flex; flex-direction:column; font-size:12px; min-width:200px;">
            <label for="raw_material_id" style="margin-bottom:2px; color:var(--tr-muted-text);">Bahan (opsional)</label>
            <select name="raw_material_id" id="raw_material_id"
                    style="padding:6px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="">-- Semua bahan --</option>
                <?php foreach ($materials as $mat): ?>
                    <option value="<?= $mat['id']; ?>" <?= (string)$materialId === (string)$mat['id'] ? 'selected' : ''; ?>>
                        <?= esc($mat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display:flex; flex-direction:column; font-size:12px; min-width:200px;">
            <label for="supplier_id" style="margin-bottom:2px; color:var(--tr-muted-text);">Supplier (opsional)</label>
            <select name="supplier_id" id="supplier_id"
                    style="padding:6px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="">-- Semua supplier --</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['id']; ?>" <?= (string)$supplierId === (string)$s['id'] ? 'selected' : ''; ?>>
                        <?= esc($s['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_from" style="margin-bottom:2px; color:var(--tr-muted-text);">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from"
                   value="<?= esc($dateFrom); ?>"
                   style="padding:6px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>
        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_to" style="margin-bottom:2px; color:var(--tr-muted-text);">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to"
                   value="<?= esc($dateTo); ?>"
                   style="padding:6px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>
        <div style="display:flex; gap:6px; align-items:flex-end;">
            <button type="submit"
                    style="padding:7px 12px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:#fff; cursor:pointer;">
                Terapkan Filter
            </button>
            <a href="<?= site_url('reports/purchases/material'); ?>"
               style="padding:7px 12px; border-radius:999px; border:1px solid var(--tr-muted-text); font-size:12px; background:var(--tr-bg); color:var(--tr-muted-text); text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">
            Belum ada data pembelian pada periode/filter ini.
        </p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter bahan/supplier:</div>
            <input type="text" id="purchases-mat-filter" placeholder="Cari bahan/supplier..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:220px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Bahan</th>
                <th style="text-align:left; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Supplier</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Qty</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Avg Price</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Min Price</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Max Price</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Total Pembelian</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">#PO</th>
            </tr>
            </thead>
            <tbody id="purchases-mat-table-body">
            <?php foreach ($rows as $row): ?>
                <?php
                    $qty       = (float) ($row['total_qty'] ?? 0);
                    $avgPrice  = (float) ($row['avg_price'] ?? 0);
                    $minPrice  = (float) ($row['min_price'] ?? 0);
                    $maxPrice  = (float) ($row['max_price'] ?? 0);
                    $totalCost = (float) ($row['total_cost'] ?? 0);
                ?>
                <tr data-mat="<?= esc(strtolower($row['material_name'] ?? '-')); ?>" data-supp="<?= esc(strtolower($row['supplier_name'] ?? '-')); ?>">
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['material_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['supplier_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format($qty, 3, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($avgPrice, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($minPrice, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($maxPrice, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right; font-weight:600;">
                        Rp <?= number_format($totalCost, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format((float) ($row['purchase_count'] ?? 0), 0, ',', '.'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="6" style="padding:10px 8px; border-top:1px solid var(--tr-border); font-weight:700; text-align:right;">
                    GRAND TOTAL
                </td>
                <td style="padding:10px 8px; border-top:1px solid var(--tr-border); font-weight:700; text-align:right;">
                    Rp <?= number_format($grandTotal, 0, ',', '.'); ?>
                </td>
                <td style="padding:10px 8px; border-top:1px solid var(--tr-border);"></td>
            </tr>
            <tr id="purchases-mat-noresult" style="display:none;">
                <td colspan="8" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    (function() {
        function init() {
            if (!window.App || !App.setupFilter) {
                return setTimeout(init, 50);
            }
            App.setupFilter({
                input: '#purchases-mat-filter',
                rows: document.querySelectorAll('#purchases-mat-table-body tr:not(#purchases-mat-noresult)'),
                noResult: '#purchases-mat-noresult',
                fields: ['mat','supp'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
