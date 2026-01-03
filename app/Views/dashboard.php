<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="dashboard-page">

    <style>
        .dashboard-page .db-attention-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .dashboard-page .db-attention-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <?php
    /**
     * Dashboard View
     *
     * Refactor goals:
     * - Mengurangi inline style (pindah ke CSS)
     * - HTML lebih bersih + mudah di-maintain
     * - Tidak mengubah key data dari controller dan rumus perhitungan
     */

    // ------------------------------------------------------
    // Helpers lokal (scope hanya view ini)
    // ------------------------------------------------------
    $fmtMoney = static fn($v): string => number_format((float) ($v ?? 0), 0, ',', '.');
    $fmtNum0  = static fn($v): string => number_format((float) ($v ?? 0), 0, ',', '.');
    $fmtNum1  = static fn($v): string => number_format((float) ($v ?? 0), 1, ',', '.');
    $fmtQty3  = static fn($v): string => number_format((float) ($v ?? 0), 3, ',', '.');

    $marginColor = static function ($margin): string {
        return ((float) ($margin ?? 0)) >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
    };

    // ------------------------------------------------------
    // Pre-calc warna & label (supaya HTML bersih)
    // ------------------------------------------------------
    $todayMarginColor = $marginColor($todayStats['margin'] ?? 0);
    $monthMarginColor = $marginColor($monthStats['margin'] ?? 0);
    $weekMarginColor  = $marginColor($weekStats['margin'] ?? 0);

    // Range info provided by controller; ensure safe fallbacks
    $rangeLabel = $rangeLabel ?? ('Periode: ' . date('d/m/Y', strtotime($dateFrom ?? $weekStart)) . ' - ' . date('d/m/Y', strtotime($dateTo ?? $today)));
    $rangeDays  = $rangeDays ?? null;
    $rangeStats = $rangeStats ?? null;
    $effectiveStats = $rangeStats ?? ($todayStats ?? []);

    $monthDeltaColor = (($monthDeltaPct ?? 0) >= 0) ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
    $monthDeltaLabel = is_null($monthDeltaPct) ? 'n/a' : number_format((float) $monthDeltaPct, 1, ',', '.') . '%';

    // Item per transaksi (hari ini)
    $todayTx    = (int) ($todayStats['tx'] ?? 0);
    $todayItems = (float) ($todayStats['items'] ?? 0);
    $itemsPerTx = $todayTx > 0 ? number_format((float) ($todayItems / max(1, $todayTx)), 1, ',', '.') : '0.0';
    ?>

    <!-- ======================================================
     Header + KPI Cards
======================================================= -->
    <div class="card db-card-pad-tight">
        <div class="db-header">
            <div>
                <h1 class="db-title">Dashboard</h1>
                <p class="db-desc">Gambaran singkat hasil bisnis pada periode yang sedang dipilih.</p>
            </div>

            <div class="db-pills">
                <span class="db-pill">Range aktif: <?= esc(str_replace('Periode: ', '', $rangeLabel)); ?><?= $rangeDays ? ' (' . (int)$rangeDays . ' hari)' : ''; ?></span>
            </div>

            <div style="margin-left:12px;">
                <?= $this->include('partials/date_range_picker', ['mode' => 'date']) ?>

                <?php
                // Preset links: use server-side $today (Y-m-d) as reference
                $presetToday = $today ?? date('Y-m-d');
                $preset7Start = date('Y-m-d', strtotime($presetToday . ' -6 days'));
                $presetMonthStart = date('Y-m-01', strtotime($presetToday));
                $dashUrl = site_url('dashboard');
                ?>

                <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap;">
                    <a href="<?= esc($dashUrl . '?' . http_build_query(['start' => $presetToday, 'end' => $presetToday])); ?>" class="db-pill">Today</a>
                    <a href="<?= esc($dashUrl . '?' . http_build_query(['start' => $preset7Start, 'end' => $presetToday])); ?>" class="db-pill">7 Hari</a>
                    <a href="<?= esc($dashUrl . '?' . http_build_query(['start' => $presetMonthStart, 'end' => $presetToday])); ?>" class="db-pill">Bulan Ini</a>
                    <a href="<?= esc($dashUrl); ?>" class="db-pill">Reset</a>
                </div>
            </div>
        </div>

    </div>

    <div class="card db-card-pad">
        <div class="db-card-top">
            <div>
                <div class="db-card-title">Ringkasan Performa</div>
                <div class="db-card-subtitle">Gambaran hasil bisnis pada periode yang sedang dipilih.</div>
            </div>
        </div>

        <?php
        $marginRp = $effectiveStats['margin'] ?? 0;
        $marginPct = $effectiveStats['margin_pct'] ?? 0;
        $marginColorCard = $marginColor($marginRp);
        ?>

        <div class="db-kpi-grid">
            <div class="db-kpi-card">
                <div class="db-kpi-label">Penjualan</div>
                <div class="db-kpi-value">Rp <?= $fmtMoney($effectiveStats['sales'] ?? 0); ?></div>
                <div class="db-kpi-sub">Total nilai penjualan dalam periode aktif.</div>
            </div>

            <div class="db-kpi-card">
                <div class="db-kpi-label">Transaksi</div>
                <div class="db-kpi-value"><?= (int) ($effectiveStats['tx'] ?? 0); ?> trx</div>
                <div class="db-kpi-sub">Jumlah transaksi yang tercatat pada periode ini.</div>
            </div>

            <div class="db-kpi-card">
                <div class="db-kpi-label">Avg Ticket</div>
                <div class="db-kpi-value">Rp <?= $fmtMoney($effectiveStats['avg_ticket'] ?? 0); ?></div>
                <div class="db-kpi-sub">Rata-rata nilai penjualan per transaksi.</div>
            </div>

            <div class="db-kpi-card">
                <div class="db-kpi-label">Item Terjual</div>
                <div class="db-kpi-value"><?= $fmtNum0($effectiveStats['items'] ?? 0); ?> item</div>
                <div class="db-kpi-sub">Total item yang terjual dalam periode aktif.</div>
            </div>

            <div class="db-kpi-card">
                <div class="db-kpi-label">Margin / Profit</div>
                <div class="db-kpi-value" style="color: <?= $marginColorCard; ?>;">Rp <?= $fmtMoney($marginRp); ?></div>
                <div class="db-kpi-sub">Selisih pendapatan dan biaya dari penjualan pada periode ini.</div>
                <div class="db-text-muted" style="margin-top:6px; color: <?= $marginColorCard; ?>; font-size:12px;">(<?= $fmtNum1($marginPct); ?>%)</div>
            </div>
        </div>
    </div>

    <!-- ======================================================
     Middle Row: Perlu Perhatian (vertical) + Insight + Top Menu
    ======================================================= -->

    <div class="db-section-header">
        <div class="db-section-title">Perlu Perhatian</div>
        <div class="db-section-subtitle">Beberapa hal yang sebaiknya dicek agar operasional tetap lancar.</div>
    </div>

    <div class="db-section db-attention-section">
        <div class="db-attention-grid">
            <div class="card db-card-pad db-card-flexcol">
                <div>
                    <div class="db-card-top" style="margin-bottom: 0;">
                        <div>
                            <div class="db-card-title">Stok Mendekati Minimum</div>
                            <div class="db-card-subtitle">Item yang jumlahnya hampir mencapai batas minimum.</div>
                        </div>
                        <a href="<?= site_url('inventory/stock-card'); ?>" class="db-link">Kartu Stok</a>
                    </div>

                    <?php if (empty($lowStocks)): ?>
                        <p class="db-card-subtitle" style="margin: 10px 0 0;">Tidak ada item yang mendekati batas minimum pada periode ini.</p>
                    <?php else: ?>
                        <div class="db-stock-list">
                            <?php foreach ($lowStocks as $raw): ?>
                                <?php
                                $current   = (float) ($raw['current_stock'] ?? 0);
                                $min       = (float) ($raw['min_stock'] ?? 0);
                                $unitShort = (string) ($raw['unit_short'] ?? '');
                                $ratio     = $min > 0 ? max(0, min(1, $current / $min)) : 0;
                                ?>
                                <div class="db-stock-item">
                                    <div class="db-stock-top">
                                        <div class="db-stock-name"><?= esc($raw['name'] ?? '-'); ?></div>
                                        <span class="db-stock-badge">Butuh restock</span>
                                    </div>

                                    <div class="db-stock-meta">
                                        Stok: <?= $fmtQty3($current); ?> <?= esc($unitShort); ?> /
                                        Min: <?= $fmtQty3($min); ?> <?= esc($unitShort); ?>
                                    </div>

                                    <div class="db-progress">
                                        <div class="db-progress-bar" style="width: <?= $ratio * 100; ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card db-card-pad">
                <div class="db-card-top">
                    <div>
                        <div class="db-card-title">Margin Ekstrem (Transaksi)</div>
                        <div class="db-card-subtitle">Transaksi dengan hasil margin yang tidak wajar dan perlu dicek.</div>
                    </div>
                </div>
                <p class="db-card-subtitle" style="margin: 10px 0 0;">
                    Belum ada transaksi yang terdeteksi memiliki margin ekstrem pada periode ini.
                </p>
            </div>
        </div>
    </div>

    <div class="db-section-header">
        <div class="db-section-title">Insight Periode Ini</div>
        <div class="db-section-subtitle">Pola dan kecenderungan yang menonjol dari periode ini.</div>
    </div>

    <!-- Top Menu (moved below Insight header) -->
    <div class="card db-card-pad">
        <div class="db-card-top">
            <div>
                <div class="db-card-title">Top Menu</div>
                <div class="db-card-subtitle">Periode <?= esc($dateFrom ?? $weekStart); ?> - <?= esc($dateTo ?? $today); ?></div>
            </div>
            <a href="<?= site_url('reports/sales/menu'); ?>" class="db-link-pill">Laporan</a>
        </div>

        <?php if (empty($topMenus)): ?>
            <p class="db-card-subtitle" style="margin: 8px 0 0;">Belum ada data penjualan 7 hari terakhir.</p>
        <?php else: ?>
            <table class="db-table">
                <thead>
                    <tr>
                        <th class="db-th-left">Menu</th>
                        <th class="db-th-right">Qty</th>
                        <th class="db-th-right">Omzet</th>
                        <th class="db-th-right">Margin %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topMenus as $row): ?>
                        <?php
                        $sales     = (float) ($row['total_sales'] ?? 0);
                        $cost      = (float) ($row['total_cost'] ?? 0);
                        $margin    = $sales - $cost;
                        $marginPct = $sales > 0 ? ($margin / $sales * 100) : 0;
                        $rowColor  = $marginColor($margin);
                        ?>
                        <tr>
                            <td><?= esc($row['menu_name'] ?? 'Menu'); ?></td>
                            <td class="db-td-right"><?= $fmtNum0($row['total_qty'] ?? 0); ?></td>
                            <td class="db-td-right">Rp <?= $fmtMoney($sales); ?></td>
                            <td class="db-td-right" style="color: <?= $rowColor; ?>;"><?= $fmtNum1($marginPct); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- ======================================================
     Recent Transactions
======================================================= -->
    <div class="card db-card-pad" style="margin-top: 12px;">
        <div class="db-card-top">
            <div>
                <div class="db-card-title">Transaksi Terbaru</div>
                <div class="db-card-subtitle">5 transaksi non-void terakhir. Menampilkan transaksi dalam range aktif.</div>
            </div>
            <a href="<?= site_url('transactions/sales'); ?>" class="db-link">Lihat semua</a>
        </div>

        <?php if (empty($recentSales)): ?>
            <p class="db-card-subtitle" style="margin: 8px 0 0;">Belum ada transaksi.</p>
        <?php else: ?>
            <table class="db-table">
                <thead>
                    <tr>
                        <th class="db-th-left">Tanggal</th>
                        <th class="db-th-left">Invoice</th>
                        <th class="db-th-right">Total</th>
                        <th class="db-th-right">Margin</th>
                        <th class="db-th-right">Margin %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentSales as $sale): ?>
                        <?php
                        $total     = (float) ($sale['total_amount'] ?? 0);
                        $cost      = (float) ($sale['total_cost'] ?? 0);
                        $margin    = $total - $cost;
                        $marginPct = $total > 0 ? ($margin / $total * 100) : 0;
                        $color     = $marginColor($margin);
                        ?>
                        <tr>
                            <td><?= esc($sale['sale_date'] ?? '-'); ?></td>
                            <td><?= esc($sale['invoice_no'] ?? '-'); ?></td>
                            <td class="db-td-right">Rp <?= $fmtMoney($total); ?></td>
                            <td class="db-td-right" style="color: <?= $color; ?>;">Rp <?= $fmtMoney($margin); ?></td>
                            <td class="db-td-right" style="color: <?= $color; ?>;"><?= $fmtNum1($marginPct); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div> <!-- /.card Recent Transactions -->

</div> <!-- /.dashboard-page -->

<?= $this->endSection() ?>