<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Kartu Stok per Bahan</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                Lihat kronologi IN/OUT dan saldo berjalan untuk satu bahan baku.
            </p>
        </div>
        <a href="<?= site_url('inventory/stock-movements'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:#111827; color:#e5e7eb; text-decoration:none;">
            Riwayat IN/OUT
        </a>
    </div>

    <form method="get" action="<?= site_url('inventory/stock-card'); ?>" style="margin-bottom:12px;">
        <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:6px;">Filter</label>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:8px; align-items:end;">
            <div>
                <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Pilih bahan baku</div>
                <select name="raw_material_id"
                        required
                        style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
                    <option value="">-- pilih bahan --</option>
                    <?php foreach ($materials as $mat): ?>
                        <option value="<?= $mat['id']; ?>" <?= isset($selectedMaterial['id']) && $selectedMaterial['id'] == $mat['id'] ? 'selected' : ''; ?>>
                            <?= esc($mat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Dari tanggal</div>
                <input type="date"
                       name="date_from"
                       value="<?= esc($filterFrom ?? ''); ?>"
                       style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
            </div>
            <div>
                <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Sampai tanggal</div>
                <input type="date"
                       name="date_to"
                       value="<?= esc($filterTo ?? ''); ?>"
                       style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
            </div>
            <div>
                <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Opening balance (opsional)</div>
                <input type="number"
                       step="0.001"
                       name="opening_balance"
                       value="<?= esc($openingBalance ?? ''); ?>"
                       placeholder="contoh: 0"
                       style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
            </div>
            <div>
                <button type="submit"
                        style="font-size:12px; padding:8px 12px; border-radius:999px; border:none; background:#22c55e; color:#022c22; cursor:pointer; width:100%;">
                    Tampilkan
                </button>
            </div>
        </div>
    </form>

    <?php if ($selectedMaterial): ?>
        <div style="padding:10px; border:1px solid #1f2937; border-radius:10px; background:#0b1220; margin-bottom:12px;">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:8px;">
                <div>
                    <div style="font-size:11px; color:#9ca3af;">Bahan</div>
                    <div style="font-size:14px; color:#e5e7eb; font-weight:600; margin-top:2px;"><?= esc($selectedMaterial['name']); ?></div>
                </div>
                <div>
                    <div style="font-size:11px; color:#9ca3af;">Stok saat ini (raw_materials)</div>
                    <div style="font-size:14px; color:#e5e7eb; font-weight:600; margin-top:2px;">
                        <?= number_format((float) ($selectedMaterial['current_stock'] ?? 0), 3, ',', '.'); ?>
                    </div>
                </div>
                <?php if (! empty($runningBalance)): ?>
                    <?php
                        $openingShown = $openingBalance ?? 0.0;
                        $calcBalance = end($runningBalance);
                        $delta = (float) ($selectedMaterial['current_stock'] ?? 0) - (float) $calcBalance;
                        $deltaColor = abs($delta) < 0.001 ? '#bbf7d0' : '#fcd34d';
                    ?>
                    <div>
                        <div style="font-size:11px; color:#9ca3af;">Opening balance (awal perhitungan)</div>
                        <div style="font-size:14px; color:#e5e7eb; font-weight:600; margin-top:2px;">
                            <?= number_format($openingShown, 3, ',', '.'); ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:#9ca3af;">Saldo berjalan (hasil kartu stok)</div>
                        <div style="font-size:14px; color:#e5e7eb; font-weight:600; margin-top:2px;">
                            <?= number_format($calcBalance, 3, ',', '.'); ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:#9ca3af;">Selisih vs current_stock</div>
                        <div style="font-size:14px; font-weight:600; margin-top:2px; color:<?= $deltaColor; ?>;">
                            <?= number_format($delta, 3, ',', '.'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($movements)): ?>
            <p style="font-size:12px; color:#9ca3af;">Belum ada pergerakan stok untuk filter ini.</p>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                    <tr>
                        <th style="text-align:left; padding:6px 8px; border-bottom:1px solid #111827;">Tanggal</th>
                        <th style="text-align:left; padding:6px 8px; border-bottom:1px solid #111827;">Tipe</th>
                        <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Qty</th>
                        <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Saldo</th>
                        <th style="text-align:left; padding:6px 8px; border-bottom:1px solid #111827;">Ref</th>
                        <th style="text-align:left; padding:6px 8px; border-bottom:1px solid #111827;">Catatan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($movements as $idx => $mv): ?>
                        <?php
                            $badgeColor = strtoupper($mv['movement_type']) === 'IN' ? '#22c55e' : '#ef4444';
                            $badgeBg = strtoupper($mv['movement_type']) === 'IN' ? '#062f1f' : '#3f1f1f';
                            $balance = $runningBalance[$idx] ?? 0;
                        ?>
                        <tr>
                            <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                                <?= esc($mv['created_at'] ?? '-'); ?>
                            </td>
                            <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                                <span style="padding:2px 8px; border-radius:999px; background:<?= $badgeBg; ?>; color:<?= $badgeColor; ?>; font-weight:600; font-size:11px;">
                                    <?= esc($mv['movement_type']); ?>
                                </span>
                            </td>
                            <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                                <?= number_format((float) $mv['qty'], 3, ',', '.'); ?>
                            </td>
                            <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right; font-weight:600;">
                                <?= number_format($balance, 3, ',', '.'); ?>
                            </td>
                            <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                                <?= esc($mv['ref_type'] ?? '-'); ?> #<?= esc($mv['ref_id'] ?? '-'); ?>
                            </td>
                            <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                                <?= esc($mv['note'] ?? ''); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
