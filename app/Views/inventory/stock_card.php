<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Kartu Stok per Bahan</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Lihat kronologi IN/OUT dan saldo berjalan untuk satu bahan baku.
            </p>
        </div>
        <a href="<?= site_url('inventory/stock-movements'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:var(--tr-border); color:var(--tr-text); text-decoration:none;">
            Riwayat IN/OUT
        </a>
    </div>

    <form method="get" action="<?= site_url('inventory/stock-card'); ?>" style="margin-bottom:12px;">
        <label style="font-size:11px; color:var(--tr-muted-text); display:block; margin-bottom:6px;">Filter</label>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:8px; align-items:end;">
            <div>
                <div style="font-size:11px; color:var(--tr-muted-text); margin-bottom:4px;">Pilih bahan baku</div>
                <select name="raw_material_id"
                        required
                        style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                    <option value="">-- pilih bahan --</option>
                    <?php foreach ($materials as $mat): ?>
                        <option value="<?= $mat['id']; ?>" <?= isset($selectedMaterial['id']) && $selectedMaterial['id'] == $mat['id'] ? 'selected' : ''; ?>>
                            <?= esc($mat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <div style="font-size:11px; color:var(--tr-muted-text); margin-bottom:4px;">Dari tanggal</div>
                <input type="date"
                       name="date_from"
                       value="<?= esc($filterFrom ?? ''); ?>"
                       style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
            </div>
            <div>
                <div style="font-size:11px; color:var(--tr-muted-text); margin-bottom:4px;">Sampai tanggal</div>
                <input type="date"
                       name="date_to"
                       value="<?= esc($filterTo ?? ''); ?>"
                       style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
            </div>
            <div>
                <div style="font-size:11px; color:var(--tr-muted-text); margin-bottom:4px;">Opening balance (opsional)</div>
                <input type="number"
                       step="0.001"
                       name="opening_balance"
                       value="<?= esc($openingBalance ?? ''); ?>"
                       placeholder="contoh: 0"
                       style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
            </div>
            <div>
                <button type="submit"
                        style="font-size:12px; padding:8px 12px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; cursor:pointer; width:100%;">
                    Tampilkan
                </button>
            </div>
        </div>
    </form>

    <?php if ($selectedMaterial): ?>
        <?php
            $currentStock = (float) ($selectedMaterial['current_stock'] ?? 0);
            $minStock     = (float) ($selectedMaterial['min_stock'] ?? 0);
            $isLow        = $minStock > 0 && $currentStock < $minStock;
        ?>
        <div style="padding:10px; border:1px solid var(--tr-border); border-radius:10px; background:var(--tr-secondary-beige); margin-bottom:12px;">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:8px;">
                <div>
                    <div style="font-size:11px; color:var(--tr-muted-text);">Bahan</div>
                    <div style="font-size:14px; color:var(--tr-text); font-weight:600; margin-top:2px;"><?= esc($selectedMaterial['name']); ?></div>
                </div>
                <div>
                    <div style="font-size:11px; color:var(--tr-muted-text);">Stok saat ini (raw_materials)</div>
                    <div style="font-size:14px; color:var(--tr-text); font-weight:600; margin-top:2px;">
                        <?= number_format($currentStock, 3, ',', '.'); ?>
                        <?php if ($isLow): ?>
                            <span style="margin-left:6px; padding:2px 8px; border-radius:999px; background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown); font-size:10px; font-weight:700;">
                                Low
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <div style="font-size:11px; color:var(--tr-muted-text);">Min stok</div>
                    <div style="font-size:14px; color:var(--tr-text); font-weight:600; margin-top:2px;">
                        <?= number_format($minStock, 3, ',', '.'); ?>
                    </div>
                </div>
                <?php if (! empty($runningBalance)): ?>
                    <?php
                        $openingShown = $openingBalance ?? 0.0;
                        $calcBalance = end($runningBalance);
                        $delta = (float) ($selectedMaterial['current_stock'] ?? 0) - (float) $calcBalance;
                        $deltaColor = abs($delta) < 0.001 ? 'var(--tr-secondary-green)' : 'var(--tr-accent-brown)';
                    ?>
                    <div>
                        <div style="font-size:11px; color:var(--tr-muted-text);">Opening balance (awal perhitungan)</div>
                        <div style="font-size:14px; color:var(--tr-text); font-weight:600; margin-top:2px;">
                            <?= number_format($openingShown, 3, ',', '.'); ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:var(--tr-muted-text);">Saldo berjalan (hasil kartu stok)</div>
                        <div style="font-size:14px; color:var(--tr-text); font-weight:600; margin-top:2px;">
                            <?= number_format($calcBalance, 3, ',', '.'); ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:var(--tr-muted-text);">Selisih vs current_stock</div>
                        <div style="font-size:14px; font-weight:600; margin-top:2px; color:<?= $deltaColor; ?>;">
                            <?= number_format($delta, 3, ',', '.'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($movements)): ?>
            <p style="font-size:12px; color:var(--tr-muted-text);">Belum ada pergerakan stok untuk filter ini.</p>
        <?php else: ?>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <div style="font-size:12px; color:var(--tr-muted-text);">Filter ref/catatan/tipe:</div>
                <input type="text" id="stockcard-filter" placeholder="Cari di kartu stok..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:220px;">
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                    <tr>
                        <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Tanggal</th>
                        <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Tipe</th>
                        <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Qty</th>
                        <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Saldo</th>
                        <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Ref</th>
                        <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Catatan</th>
                    </tr>
                    </thead>
                    <tbody id="stockcard-table-body">
                    <?php foreach ($movements as $idx => $mv): ?>
                        <?php
                            $badgeColor = strtoupper($mv['movement_type']) === 'IN' ? 'var(--tr-primary)' : 'var(--tr-accent-brown)';
                            $badgeBg = strtoupper($mv['movement_type']) === 'IN' ? 'rgba(122,154,108,0.14)' : 'var(--tr-secondary-beige)';
                            $balance = $runningBalance[$idx] ?? 0;
                            $refStr = trim(($mv['ref_type'] ?? '-') . ' #' . ($mv['ref_id'] ?? '-'));
                        ?>
                        <tr data-ref="<?= esc(strtolower($refStr)); ?>" data-note="<?= esc(strtolower($mv['note'] ?? '')); ?>" data-type="<?= esc(strtolower($mv['movement_type'] ?? '')); ?>">
                            <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                                <?= esc($mv['created_at'] ?? '-'); ?>
                            </td>
                            <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                                <span style="padding:2px 8px; border-radius:999px; background:<?= $badgeBg; ?>; color:<?= $badgeColor; ?>; font-weight:600; font-size:11px;">
                                    <?= esc($mv['movement_type']); ?>
                                </span>
                            </td>
                            <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                                <?= number_format((float) $mv['qty'], 3, ',', '.'); ?>
                            </td>
                            <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; font-weight:600;">
                                <?= number_format($balance, 3, ',', '.'); ?>
                            </td>
                            <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                                <?= esc($mv['ref_type'] ?? '-'); ?> #<?= esc($mv['ref_id'] ?? '-'); ?>
                            </td>
                            <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                                <?= esc($mv['note'] ?? ''); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr id="stockcard-noresult" style="display:none;">
                        <td colspan="6" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    (function() {
        function init() {
            if (!window.App || !App.setupFilter) {
                return setTimeout(init, 50);
            }
            App.setupFilter({
                input: '#stockcard-filter',
                rows: document.querySelectorAll('#stockcard-table-body tr:not(#stockcard-noresult)'),
                noResult: '#stockcard-noresult',
                fields: ['ref','note','type'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
