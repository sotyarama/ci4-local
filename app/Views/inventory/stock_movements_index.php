<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Riwayat Pergerakan Stok</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                IN/OUT dari pembelian, penjualan, dan aktivitas lain yang memengaruhi stok bahan baku.
            </p>
        </div>
    </div>

    <!-- Filter -->
    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="raw_material_id" style="margin-bottom:2px; color:#d1d5db;">Bahan Baku</label>
            <select name="raw_material_id" id="raw_material_id"
                    style="min-width:220px; padding:5px 8px; border-radius:6px; border:1px solid #374151; background:#020617; color:#e5e7eb; font-size:12px;">
                <option value="0">-- Semua Bahan --</option>
                <?php foreach ($materials as $m): ?>
                    <option value="<?= $m['id']; ?>" <?= ($filterRawId == $m['id']) ? 'selected' : ''; ?>>
                        <?= esc($m['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_from" style="margin-bottom:2px; color:#d1d5db;">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from"
                   value="<?= esc($filterFrom); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid #374151; background:#020617; color:#e5e7eb; font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_to" style="margin-bottom:2px; color:#d1d5db;">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to"
                   value="<?= esc($filterTo); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid #374151; background:#020617; color:#e5e7eb; font-size:12px;">
        </div>
        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="opening_balance" style="margin-bottom:2px; color:#d1d5db;">Opening Balance (opsional)</label>
            <input type="number" step="0.001" name="opening_balance" id="opening_balance"
                   value="<?= esc($openingBalance); ?>"
                   placeholder="mis: 0"
                   style="padding:5px 8px; border-radius:6px; border:1px solid #374151; background:#020617; color:#e5e7eb; font-size:12px;">
        </div>

        <div style="display:flex; gap:6px;">
            <button type="submit"
                    style="margin-top:18px; padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:#2563eb; color:#e5e7eb; cursor:pointer;">
                Terapkan Filter
            </button>

            <a href="<?= site_url('inventory/stock-movements'); ?>"
               style="margin-top:18px; padding:6px 10px; border-radius:999px; border:1px solid #4b5563; font-size:12px; background:#020617; color:#9ca3af; text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <?php if (empty($movements)): ?>
        <p style="font-size:12px; color:#9ca3af; margin:8px 0 0;">
            Belum ada data pergerakan stok untuk filter yang dipilih.
        </p>
    <?php else: ?>
        <?php if ($openingBalance !== null && $filterRawId > 0): ?>
            <div style="font-size:11px; color:#9ca3af; margin-bottom:6px;">
                Opening balance: <span style="color:#e5e7eb; font-weight:600;"><?= number_format((float)$openingBalance, 3, ',', '.'); ?></span>
            </div>
        <?php endif; ?>
        <div style="overflow:auto; max-height: calc(80vh - 120px); border:1px solid #111827; border-radius:10px;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Waktu</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Bahan Baku</th>
                <th style="text-align:center;padding:6px 8px; border-bottom:1px solid #111827;">Tipe</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Qty</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Saldo</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Referensi</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Catatan</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($movements as $row): ?>
                <?php
                    $type      = strtoupper($row['movement_type'] ?? '');
                    $isIn      = ($type === 'IN');
                    $typeLabel = $isIn ? 'IN' : 'OUT';
                    $typeBg    = $isIn ? '#022c22' : '#3f1f1f';
                    $typeBorder= $isIn ? '#16a34a' : '#b91c1c';
                    $typeColor = $isIn ? '#6ee7b7' : '#fecaca';

                    $qty   = (float) ($row['qty'] ?? 0);
                    $unit  = $row['unit_short'] ?? '';
                    $ref   = trim(($row['ref_type'] ?? '') . ' #' . ($row['ref_id'] ?? ''));
                    $balance = $runningBalanceMap[$row['id']] ?? null;
                ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['created_at']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['material_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:center;">
                        <span style="display:inline-block; padding:2px 8px; border-radius:999px;
                                     background:<?= $typeBg ?>;
                                     border:1px solid <?= $typeBorder ?>;
                                     color:<?= $typeColor ?>;">
                            <?= $typeLabel; ?>
                        </span>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        <?= number_format($qty, 3, ',', '.'); ?>
                        <?php if ($unit): ?>
                            <span style="color:#9ca3af; font-size:11px; margin-left:2px;"><?= esc($unit); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right; font-weight:600;">
                        <?php if ($balance !== null): ?>
                            <?= number_format($balance, 3, ',', '.'); ?>
                        <?php else: ?>
                            <span style="color:#6b7280;">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($ref ?: '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['note'] ?? ''); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>

    <div style="margin-top:10px; font-size:11px; color:#6b7280;">
        Sumber data: <code>stock_movements</code> (join <code>raw_materials</code> & <code>units</code>).  
        Saldo ditampilkan jika filter satu bahan dipilih; perhitungan mengikuti urutan data terfilter dan opening balance (jika diisi).
    </div>
</div>

<?= $this->endSection() ?>
