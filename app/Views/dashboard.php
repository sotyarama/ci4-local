<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    $todayMarginColor  = (($todayStats['margin'] ?? 0) >= 0) ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
    $monthMarginColor  = (($monthStats['margin'] ?? 0) >= 0) ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
    $weekMarginColor   = (($weekStats['margin'] ?? 0) >= 0) ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
    $monthDeltaColor   = ($monthDeltaPct ?? 0) >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
    $monthDeltaLabel   = is_null($monthDeltaPct) ? 'n/a' : number_format($monthDeltaPct, 1, ',', '.') . '%';
?>

<div class="card" style="padding:16px 18px; margin-bottom:14px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 style="margin: 0; font-size: 20px; color:var(--tr-text);">Dashboard</h1>
            <p style="margin: 4px 0 0; font-size: 12px; color:var(--tr-muted-text);">
                Pantau performa penjualan, pembelian, serta bahan yang perlu diperhatikan.
            </p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
            <span style="padding:6px 10px; border-radius:999px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); font-size:12px; color:var(--tr-text);">
                Hari ini: <?= esc($today); ?>
            </span>
            <span style="padding:6px 10px; border-radius:999px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); font-size:12px; color:var(--tr-text);">
                7 hari: <?= esc($weekStart); ?> - <?= esc($today); ?>
            </span>
        </div>
    </div>

    <div style="margin-top:14px; display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
        <div style="padding:12px 14px; border:1px solid var(--tr-border); border-radius:14px; background:#fff;">
            <div style="font-size:12px; color:var(--tr-muted-text); text-transform:uppercase; letter-spacing:.06em;">Penjualan Hari Ini</div>
            <div style="font-size:22px; font-weight:700; margin:6px 0 2px;">Rp <?= number_format((float) ($todayStats['sales'] ?? 0), 0, ',', '.'); ?></div>
            <div style="font-size:12px; color:<?= $todayMarginColor; ?>;">
                Margin: Rp <?= number_format((float) ($todayStats['margin'] ?? 0), 0, ',', '.'); ?>
                <span style="color:var(--tr-muted-text);"> (<?= number_format((float) ($todayStats['margin_pct'] ?? 0), 1, ',', '.'); ?>%)</span>
            </div>
            <div style="font-size:12px; color:var(--tr-muted-text); margin-top:6px;">
                <?= (int) ($todayStats['tx'] ?? 0); ?> transaksi | <?= number_format((float) ($todayStats['items'] ?? 0), 0, ',', '.'); ?> item | Avg ticket Rp <?= number_format((float) ($todayStats['avg_ticket'] ?? 0), 0, ',', '.'); ?>
            </div>
        </div>

        <div style="padding:12px 14px; border:1px solid var(--tr-border); border-radius:14px; background:#fff;">
            <div style="font-size:12px; color:var(--tr-muted-text); text-transform:uppercase; letter-spacing:.06em;">Bulan Ini (<?= esc($monthLabel); ?>)</div>
            <div style="font-size:22px; font-weight:700; margin:6px 0 2px;">Rp <?= number_format((float) ($monthStats['sales'] ?? 0), 0, ',', '.'); ?></div>
            <div style="font-size:12px; color:<?= $monthMarginColor; ?>;">
                Margin: Rp <?= number_format((float) ($monthStats['margin'] ?? 0), 0, ',', '.'); ?>
                <span style="color:var(--tr-muted-text);"> (<?= number_format((float) ($monthStats['margin_pct'] ?? 0), 1, ',', '.'); ?>%)</span>
            </div>
            <div style="font-size:12px; color:var(--tr-muted-text); margin-top:6px;">
                vs bulan lalu: <span style="color:<?= $monthDeltaColor; ?>; font-weight:600;"><?= $monthDeltaLabel; ?></span>
            </div>
        </div>

        <div style="padding:12px 14px; border:1px solid var(--tr-border); border-radius:14px; background:#fff;">
            <div style="font-size:12px; color:var(--tr-muted-text); text-transform:uppercase; letter-spacing:.06em;">7 Hari Terakhir</div>
            <div style="font-size:22px; font-weight:700; margin:6px 0 2px;">Rp <?= number_format((float) ($weekStats['sales'] ?? 0), 0, ',', '.'); ?></div>
            <div style="font-size:12px; color:<?= $weekMarginColor; ?>;">
                Margin: Rp <?= number_format((float) ($weekStats['margin'] ?? 0), 0, ',', '.'); ?>
                <span style="color:var(--tr-muted-text);"> (<?= number_format((float) ($weekStats['margin_pct'] ?? 0), 1, ',', '.'); ?>%)</span>
            </div>
            <div style="font-size:12px; color:var(--tr-muted-text); margin-top:6px;">
                <?= (int) ($weekStats['tx'] ?? 0); ?> transaksi | <?= number_format((float) ($weekStats['items'] ?? 0), 0, ',', '.'); ?> item
            </div>
        </div>
    </div>

    <div style="margin-top:12px; display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:10px;">
        <div style="padding:10px 12px; border:1px dashed var(--tr-border); border-radius:12px; background:var(--tr-secondary-beige);">
            <div style="font-size:11px; color:var(--tr-muted-text); text-transform:uppercase; letter-spacing:.06em;">Transaksi Hari Ini</div>
            <div style="font-size:18px; font-weight:700; margin-top:4px; color:var(--tr-text);"><?= (int) ($todayStats['tx'] ?? 0); ?> trx</div>
            <div style="font-size:12px; color:var(--tr-muted-text);">Avg ticket Rp <?= number_format((float) ($todayStats['avg_ticket'] ?? 0), 0, ',', '.'); ?></div>
        </div>
        <div style="padding:10px 12px; border:1px dashed var(--tr-border); border-radius:12px; background:var(--tr-secondary-beige);">
            <div style="font-size:11px; color:var(--tr-muted-text); text-transform:uppercase; letter-spacing:.06em;">Item Terjual (Hari Ini)</div>
            <div style="font-size:18px; font-weight:700; margin-top:4px; color:var(--tr-text);"><?= number_format((float) ($todayStats['items'] ?? 0), 0, ',', '.'); ?> item</div>
            <div style="font-size:12px; color:var(--tr-muted-text);">Rata-rata <?= ($todayStats['tx'] ?? 0) > 0 ? number_format((float) ($todayStats['items'] / max(1, $todayStats['tx'])), 1, ',', '.') : '0.0'; ?> / transaksi</div>
        </div>
        <div style="padding:10px 12px; border:1px dashed var(--tr-border); border-radius:12px; background:var(--tr-secondary-beige);">
            <div style="font-size:11px; color:var(--tr-muted-text); text-transform:uppercase; letter-spacing:.06em;">Pembelian Bulan Ini</div>
            <div style="font-size:18px; font-weight:700; margin-top:4px; color:var(--tr-text);">Rp <?= number_format((float) $purchaseMonth, 0, ',', '.'); ?></div>
            <div style="font-size:12px; color:var(--tr-muted-text);">Periode <?= esc($monthLabel); ?></div>
        </div>
        <div style="padding:10px 12px; border:1px dashed var(--tr-border); border-radius:12px; background:var(--tr-secondary-beige);">
            <div style="font-size:11px; color:var(--tr-muted-text); text-transform:uppercase; letter-spacing:.06em;">Biaya Bulan Ini</div>
            <div style="font-size:18px; font-weight:700; margin-top:4px; color:var(--tr-text);">Rp <?= number_format((float) $overheadMonth, 0, ',', '.'); ?></div>
            <div style="font-size:12px; color:var(--tr-muted-text);">
                Operasional: Rp <?= number_format((float) ($overheadBreakdown['operational'] ?? 0), 0, ',', '.'); ?> | Payroll: Rp <?= number_format((float) ($overheadBreakdown['payroll'] ?? 0), 0, ',', '.'); ?>
            </div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(360px, 1fr)); gap:12px; align-items:start;">
    <div class="card" style="padding:14px 16px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px; gap:8px;">
            <div>
                <div style="font-size:16px; font-weight:700; margin:0; color:var(--tr-text);">Top Menu 7 Hari</div>
                <div style="font-size:12px; color:var(--tr-muted-text);">Periode <?= esc($weekStart); ?> - <?= esc($today); ?></div>
            </div>
            <a href="<?= site_url('reports/sales/menu'); ?>" style="font-size:12px; color:var(--tr-primary); text-decoration:none; border:1px solid var(--tr-primary); padding:6px 10px; border-radius:999px; background:rgba(122,154,108,0.12);">Laporan</a>
        </div>

        <?php if (empty($topMenus)): ?>
            <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">Belum ada data penjualan 7 hari terakhir.</p>
        <?php else: ?>
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Menu</th>
                        <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Qty</th>
                        <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Omzet</th>
                        <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topMenus as $row): ?>
                        <?php
                            $sales  = (float) ($row['total_sales'] ?? 0);
                            $cost   = (float) ($row['total_cost'] ?? 0);
                            $margin = $sales - $cost;
                            $marginPct = $sales > 0 ? ($margin / $sales * 100) : 0;
                            $rowColor = $margin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                        ?>
                        <tr>
                            <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);"><?= esc($row['menu_name'] ?? 'Menu'); ?></td>
                            <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;"><?= number_format((float) ($row['total_qty'] ?? 0), 0, ',', '.'); ?></td>
                            <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">Rp <?= number_format($sales, 0, ',', '.'); ?></td>
                            <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $rowColor; ?>;"><?= number_format($marginPct, 1, ',', '.'); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card" style="padding:14px 16px; display:flex; flex-direction:column; gap:12px;">
        <div>
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                <div>
                    <div style="font-size:16px; font-weight:700; margin:0; color:var(--tr-text);">Stok Mendekati Min</div>
                    <div style="font-size:12px; color:var(--tr-muted-text);">Raw material dengan current_stock di bawah min_stock.</div>
                </div>
                <a href="<?= site_url('inventory/stock-card'); ?>" style="font-size:12px; color:var(--tr-primary); text-decoration:none;">Kartu Stok</a>
            </div>
            <?php if (empty($lowStocks)): ?>
                <p style="font-size:12px; color:var(--tr-muted-text); margin:10px 0 0;">Tidak ada bahan di bawah batas minimum.</p>
            <?php else: ?>
                <div style="margin-top:10px; display:flex; flex-direction:column; gap:10px;">
                    <?php foreach ($lowStocks as $raw): ?>
                        <?php
                            $current = (float) ($raw['current_stock'] ?? 0);
                            $min     = (float) ($raw['min_stock'] ?? 0);
                            $ratio   = $min > 0 ? max(0, min(1, $current / $min)) : 0;
                        ?>
                        <div style="padding:10px 12px; border:1px solid var(--tr-border); border-radius:10px; background:#fff;">
                            <div style="display:flex; justify-content:space-between; gap:8px; align-items:center; margin-bottom:6px;">
                                <div style="font-size:13px; font-weight:700; color:var(--tr-text);"><?= esc($raw['name'] ?? '-'); ?></div>
                                <span style="font-size:11px; color:var(--tr-accent-brown); background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); border-radius:999px; padding:2px 8px;">
                                    Butuh restock
                                </span>
                            </div>
                            <div style="font-size:12px; color:var(--tr-muted-text); margin-bottom:6px;">
                                Stok: <?= number_format($current, 3, ',', '.'); ?> <?= esc($raw['unit_short'] ?? ''); ?> / Min: <?= number_format($min, 3, ',', '.'); ?> <?= esc($raw['unit_short'] ?? ''); ?>
                            </div>
                            <div style="height:6px; background:var(--tr-border); border-radius:6px; overflow:hidden;">
                                <div style="height:100%; width:<?= $ratio * 100; ?>%; background:linear-gradient(90deg, var(--tr-primary), var(--tr-secondary-green));"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="border-top:1px solid var(--tr-border); padding-top:8px;">
            <div style="font-size:16px; font-weight:700; color:var(--tr-text); margin-bottom:6px;">Beban Bulanan</div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:8px;">
                <div style="padding:10px 12px; border:1px solid var(--tr-border); border-radius:10px; background:var(--tr-secondary-beige);">
                    <div style="font-size:11px; color:var(--tr-muted-text);">Operasional</div>
                    <div style="font-size:16px; font-weight:700; color:var(--tr-text); margin-top:2px;">Rp <?= number_format((float) ($overheadBreakdown['operational'] ?? 0), 0, ',', '.'); ?></div>
                </div>
                <div style="padding:10px 12px; border:1px solid var(--tr-border); border-radius:10px; background:var(--tr-secondary-beige);">
                    <div style="font-size:11px; color:var(--tr-muted-text);">Payroll</div>
                    <div style="font-size:16px; font-weight:700; color:var(--tr-text); margin-top:2px;">Rp <?= number_format((float) ($overheadBreakdown['payroll'] ?? 0), 0, ',', '.'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="padding:14px 16px; margin-top:12px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px; gap:8px;">
        <div>
            <div style="font-size:16px; font-weight:700; color:var(--tr-text);">Transaksi Terbaru</div>
            <div style="font-size:12px; color:var(--tr-muted-text);">5 transaksi non-void terakhir.</div>
        </div>
        <a href="<?= site_url('transactions/sales'); ?>" style="font-size:12px; color:var(--tr-primary); text-decoration:none;">Lihat semua</a>
    </div>

    <?php if (empty($recentSales)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">Belum ada transaksi.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Tanggal</th>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Invoice</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Total</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin %</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentSales as $sale): ?>
                    <?php
                        $total  = (float) ($sale['total_amount'] ?? 0);
                        $cost   = (float) ($sale['total_cost'] ?? 0);
                        $margin = $total - $cost;
                        $marginPct = $total > 0 ? ($margin / $total * 100) : 0;
                        $color  = $margin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                    ?>
                    <tr>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);"><?= esc($sale['sale_date'] ?? '-'); ?></td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);"><?= esc($sale['invoice_no'] ?? '-'); ?></td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">Rp <?= number_format($total, 0, ',', '.'); ?></td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $color; ?>;">Rp <?= number_format($margin, 0, ',', '.'); ?></td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $color; ?>;"><?= number_format($marginPct, 1, ',', '.'); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
