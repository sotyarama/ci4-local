<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Riwayat Pergerakan Stok</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                IN/OUT dari pembelian, penjualan, dan aktivitas lain yang memengaruhi stok bahan baku.
            </p>
        </div>
    </div>

    <!-- Filter -->
    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="raw_material_id" style="margin-bottom:2px; color:var(--tr-muted-text);">Bahan Baku</label>
            <select name="raw_material_id" id="raw_material_id"
                    style="min-width:220px; padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="0">-- Semua Bahan --</option>
                <?php foreach ($materials as $m): ?>
                    <option value="<?= $m['id']; ?>" <?= ($filterRawId == $m['id']) ? 'selected' : ''; ?>>
                        <?= esc($m['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_from" style="margin-bottom:2px; color:var(--tr-muted-text);">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from"
                   value="<?= esc($filterFrom); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_to" style="margin-bottom:2px; color:var(--tr-muted-text);">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to"
                   value="<?= esc($filterTo); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>
        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="opening_balance" style="margin-bottom:2px; color:var(--tr-muted-text);">Opening Balance (opsional)</label>
            <input type="number" step="0.001" name="opening_balance" id="opening_balance"
                   value="<?= esc($openingBalance); ?>"
                   placeholder="mis: 0"
                   style="padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>

        <div style="display:flex; gap:6px;">
            <button type="submit"
                    style="margin-top:18px; padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:var(--tr-text); cursor:pointer;">
                Terapkan Filter
            </button>

            <a href="<?= site_url('inventory/stock-movements'); ?>"
               style="margin-top:18px; padding:6px 10px; border-radius:999px; border:1px solid var(--tr-muted-text); font-size:12px; background:var(--tr-bg); color:var(--tr-muted-text); text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <?php if (empty($movements)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">
            Belum ada data pergerakan stok untuk filter yang dipilih.
        </p>
    <?php else: ?>
        <?php if ($openingBalance !== null && $filterRawId > 0): ?>
            <div style="font-size:11px; color:var(--tr-muted-text); margin-bottom:6px;">
                Opening balance: <span style="color:var(--tr-text); font-weight:600;"><?= number_format((float)$openingBalance, 3, ',', '.'); ?></span>
            </div>
        <?php endif; ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter bahan/ref/catatan:</div>
            <input type="text" id="mov-filter" placeholder="Cari movement..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:220px;">
        </div>
        <div class="table-scroll-wrap" style="overflow:auto; max-height: calc(80vh - 120px); border:1px solid var(--tr-border); border-radius:10px;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Waktu</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Bahan Baku</th>
                <th style="text-align:center;padding:6px 8px; border-bottom:1px solid var(--tr-border);">Tipe</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Qty</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Saldo</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Referensi</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Catatan</th>
            </tr>
            </thead>
            <tbody id="mov-table-body">
            <?php foreach ($movements as $row): ?>
                <?php
                    $type      = strtoupper($row['movement_type'] ?? '');
                    $isIn      = ($type === 'IN');
                    $typeLabel = $isIn ? 'IN' : 'OUT';
                    $typeBg    = $isIn ? 'rgba(122,154,108,0.14)' : 'var(--tr-secondary-beige)';
                    $typeBorder= $isIn ? 'var(--tr-primary)' : 'var(--tr-accent-brown)';
                    $typeColor = $isIn ? 'var(--tr-secondary-green)' : 'var(--tr-accent-brown)';

                    $qty   = (float) ($row['qty'] ?? 0);
                    $unit  = $row['unit_short'] ?? '';
                    $ref   = trim(($row['ref_type'] ?? '') . ' #' . ($row['ref_id'] ?? ''));
                    $balance = $runningBalanceMap[$row['id']] ?? null;
                ?>
                <tr data-material="<?= esc(strtolower($row['material_name'] ?? '-')); ?>" data-ref="<?= esc(strtolower($ref)); ?>" data-note="<?= esc(strtolower($row['note'] ?? '')); ?>" data-type="<?= esc(strtolower($typeLabel)); ?>">
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['created_at']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['material_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <span style="display:inline-block; padding:2px 8px; border-radius:999px;
                                     background:<?= $typeBg ?>;
                                     border:1px solid <?= $typeBorder ?>;
                                     color:<?= $typeColor ?>;">
                            <?= $typeLabel; ?>
                        </span>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format($qty, 3, ',', '.'); ?>
                        <?php if ($unit): ?>
                            <span style="color:var(--tr-muted-text); font-size:11px; margin-left:2px;"><?= esc($unit); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; font-weight:600;">
                        <?php if ($balance !== null): ?>
                            <?= number_format($balance, 3, ',', '.'); ?>
                        <?php else: ?>
                            <span style="color:var(--tr-muted-text);">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($ref ?: '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['note'] ?? ''); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr id="mov-noresult" style="display:none;">
                <td colspan="7" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
            </tbody>
        </table>
        </div>
    <?php endif; ?>

    <div style="margin-top:10px; font-size:11px; color:var(--tr-muted-text);">
        Sumber data: <code>stock_movements</code> (join <code>raw_materials</code> & <code>units</code>).  
        Saldo ditampilkan jika filter satu bahan dipilih; perhitungan mengikuti urutan data terfilter dan opening balance (jika diisi).
    </div>
</div>

<script>
    (function() {
        function init() {
            if (!window.App || !App.setupFilter) {
                return setTimeout(init, 50);
            }
            App.setupFilter({
                input: '#mov-filter',
                rows: document.querySelectorAll('#mov-table-body tr:not(#mov-noresult)'),
                noResult: '#mov-noresult',
                fields: ['material','ref','note','type'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>

