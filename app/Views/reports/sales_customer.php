<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Laporan Penjualan per Customer</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Ringkasan order, omzet, HPP, dan margin per customer untuk periode tertentu.
            </p>
        </div>
        <?php
            $csvParams = [
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'export'    => 'csv',
            ];
            $csvQuery = http_build_query(array_filter($csvParams, function($v) {
                return $v !== null && $v !== '';
            }));
            $csvUrl = current_url() . ($csvQuery ? '?' . $csvQuery : '');
        ?>
        <a href="<?= $csvUrl; ?>"
           style="font-size:11px; padding:6px 10px; border-radius:999px; border:1px solid var(--tr-muted-text); background:var(--tr-border); color:var(--tr-text); text-decoration:none;">
            Export CSV
        </a>
    </div>

    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_from" style="margin-bottom:2px; color:var(--tr-muted-text);">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from"
                   value="<?= esc($dateFrom); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_to" style="margin-bottom:2px; color:var(--tr-muted-text);">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to"
                   value="<?= esc($dateTo); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="per_page" style="margin-bottom:2px; color:var(--tr-muted-text);">Baris per halaman</label>
            <select name="per_page" id="per_page"
                    style="min-width:120px; padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <?php foreach ([20, 50, 100, 200] as $opt): ?>
                    <option value="<?= $opt; ?>" <?= ((int)$perPage === $opt) ? 'selected' : ''; ?>><?= $opt; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="mode" style="margin-bottom:2px; color:var(--tr-muted-text);">Tampilan</label>
            <select name="mode" id="mode"
                    style="min-width:140px; padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="full" <?= ($mode ?? 'full') === 'full' ? 'selected' : ''; ?>>Lengkap</option>
                <option value="compact" <?= ($mode ?? '') === 'compact' ? 'selected' : ''; ?>>Ringkas</option>
            </select>
        </div>

        <div style="display:flex; gap:6px;">
            <button type="submit"
                    style="margin-top:18px; padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:var(--tr-text); cursor:pointer;">
                Terapkan Filter
            </button>

            <a href="<?= site_url('reports/sales/customer'); ?>"
               style="margin-top:18px; padding:6px 10px; border-radius:999px; border:1px solid var(--tr-muted-text); font-size:12px; background:var(--tr-bg); color:var(--tr-muted-text); text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">
            Belum ada data penjualan untuk periode ini.
        </p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter customer:</div>
            <input type="text" id="salescustomer-filter" placeholder="Cari customer..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:200px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Customer</th>
                <?php if (($mode ?? 'full') === 'full'): ?>
                    <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Telepon</th>
                    <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Email</th>
                <?php endif; ?>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Order</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Item</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Omzet</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">HPP</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin %</th>
                <?php if (($mode ?? 'full') === 'full'): ?>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Hari Aktif</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Avg Order/Hari</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Order Terakhir</th>
                <?php endif; ?>
                <th style="text-align:center; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
            </thead>
            <tbody id="salescustomer-table-body">
            <?php foreach ($rows as $r): ?>
                <?php
                    $orders = (int) ($r['total_orders'] ?? 0);
                    $items  = (float) ($r['total_items'] ?? 0);
                    $sales  = (float) ($r['total_sales'] ?? 0);
                    $cost   = (float) ($r['total_cost'] ?? 0);
                    $margin = $sales - $cost;
                    $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;
                    $marginColor = $margin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                    $activeDays = (int) ($r['active_days'] ?? 0);
                    $avgOrders  = $activeDays > 0 ? ($orders / $activeDays) : 0;

                    $customerName = $r['customer_name'] ?? 'Tamu';
                    $customerPhone = $r['customer_phone'] ?? '';
                    $customerEmail = $r['customer_email'] ?? '';
                    $lastOrder = $r['last_order_date'] ?? '';
                    $customerId = (int) ($r['customer_id'] ?? 0);
                    $detailParams = [
                        'date_from' => $dateFrom,
                        'date_to'   => $dateTo,
                        'per_page'  => $perPage,
                        'mode'      => $mode,
                    ];
                    $detailQuery = http_build_query(array_filter($detailParams, function($v) {
                        return $v !== null && $v !== '';
                    }));
                    $detailUrl = $customerId > 0
                        ? site_url('reports/sales/customer/' . $customerId) . ($detailQuery ? '?' . $detailQuery : '')
                        : null;
                ?>
                <tr data-name="<?= esc(strtolower($customerName)); ?>" data-phone="<?= esc(strtolower($customerPhone)); ?>" data-email="<?= esc(strtolower($customerEmail)); ?>">
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($customerName); ?>
                    </td>
                    <?php if (($mode ?? 'full') === 'full'): ?>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?= esc($customerPhone !== '' ? $customerPhone : '-'); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?= esc($customerEmail !== '' ? $customerEmail : '-'); ?>
                        </td>
                    <?php endif; ?>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format($orders, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format($items, 2, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($sales, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($cost, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $marginColor; ?>;">
                        Rp <?= number_format($margin, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $marginColor; ?>;">
                        <?= number_format($marginPct, 1, ',', '.'); ?>%
                    </td>
                    <?php if (($mode ?? 'full') === 'full'): ?>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                            <?= number_format($activeDays, 0, ',', '.'); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                            <?= number_format($avgOrders, 2, ',', '.'); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                            <?= esc($lastOrder !== '' ? $lastOrder : '-'); ?>
                        </td>
                    <?php endif; ?>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <?php if ($detailUrl): ?>
                            <a href="<?= $detailUrl; ?>"
                               style="font-size:11px; padding:4px 8px; border-radius:999px; border:1px solid var(--tr-primary); color:var(--tr-primary); text-decoration:none;">
                                Detail
                            </a>
                        <?php else: ?>
                            <span style="color:var(--tr-muted-text);">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php
                $grandOrders = (int) $totalOrdersAll;
                $grandItems  = (float) $totalItemsAll;
                $grandSales  = (float) $totalSalesAll;
                $grandCost   = (float) $totalCostAll;
                $grandMargin = $grandSales - $grandCost;
                $grandMarginPct = $grandSales > 0 ? ($grandMargin / $grandSales * 100.0) : 0;
                $grandColor = $grandMargin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                $colSpan = (($mode ?? 'full') === 'full') ? 13 : 8;
            ?>
            <tr id="salescustomer-noresult" style="display:none;">
                <td colspan="<?= $colSpan; ?>" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
            <tr>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); font-weight:bold;">
                    TOTAL (filter)
                </td>
                <?php if (($mode ?? 'full') === 'full'): ?>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">-</td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">-</td>
                <?php endif; ?>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">
                    <?= number_format($grandOrders, 0, ',', '.'); ?>
                </td>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">
                    <?= number_format($grandItems, 2, ',', '.'); ?>
                </td>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">
                    Rp <?= number_format($grandSales, 0, ',', '.'); ?>
                </td>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">
                    Rp <?= number_format($grandCost, 0, ',', '.'); ?>
                </td>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold; color:<?= $grandColor; ?>;">
                    Rp <?= number_format($grandMargin, 0, ',', '.'); ?>
                </td>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold; color:<?= $grandColor; ?>;">
                    <?= number_format($grandMarginPct, 1, ',', '.'); ?>%
                </td>
                <?php if (($mode ?? 'full') === 'full'): ?>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">-</td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">-</td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">-</td>
                <?php endif; ?>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:center; font-weight:bold;">-</td>
            </tr>
            </tbody>
        </table>

        <?php
            $queryBase = [
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'per_page'  => $perPage,
                'mode'      => $mode,
            ];
            $buildUrl = static function(int $targetPage) use ($queryBase): string {
                $params = array_merge($queryBase, ['page' => $targetPage]);
                $params = array_filter($params, static function($v) {
                    return $v !== null && $v !== '';
                });
                $qs = http_build_query($params);
                return current_url() . ($qs ? '?' . $qs : '');
            };
            $startRow = ($page - 1) * $perPage + 1;
            $endRow   = min($startRow + $perPage - 1, $totalRows);
        ?>
        <div style="margin-top:12px; display:flex; justify-content:space-between; align-items:center; font-size:12px; color:var(--tr-muted-text);">
            <div>
                <?= $totalRows > 0
                    ? "Menampilkan {$startRow}-{$endRow} dari {$totalRows} customer"
                    : "Tidak ada data untuk filter ini"; ?>
            </div>
            <div style="display:flex; gap:6px;">
                <a href="<?= $buildUrl(max(1, $page - 1)); ?>"
                   style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:<?= $page > 1 ? 'var(--tr-border)' : 'var(--tr-secondary-beige)'; ?>; color:<?= $page > 1 ? 'var(--tr-text)' : 'var(--tr-muted-text)'; ?>; text-decoration:none; pointer-events:<?= $page > 1 ? 'auto' : 'none'; ?>;">
                    Prev
                </a>
                <span style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); color:var(--tr-text);">
                    Halaman <?= $page; ?> / <?= max(1, $totalPages); ?>
                </span>
                <a href="<?= $buildUrl(min($totalPages, $page + 1)); ?>"
                   style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:<?= $page < $totalPages ? 'var(--tr-border)' : 'var(--tr-secondary-beige)'; ?>; color:<?= $page < $totalPages ? 'var(--tr-text)' : 'var(--tr-muted-text)'; ?>; text-decoration:none; pointer-events:<?= $page < $totalPages ? 'auto' : 'none'; ?>;">
                    Next
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    (function() {
        function init() {
            if (!window.App || !App.setupFilter) {
                return setTimeout(init, 50);
            }
            App.setupFilter({
                input: '#salescustomer-filter',
                rows: document.querySelectorAll('#salescustomer-table-body tr:not(#salescustomer-noresult)'),
                noResult: '#salescustomer-noresult',
                fields: ['name', 'phone', 'email'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
