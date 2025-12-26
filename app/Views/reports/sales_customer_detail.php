<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Detail Penjualan Customer</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Rincian transaksi dan ringkasan penjualan per customer.
            </p>
        </div>
        <?php
            $backParams = [
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'per_page'  => $perPage,
                'mode'      => $mode,
            ];
            $backQuery = http_build_query(array_filter($backParams, function($v) {
                return $v !== null && $v !== '';
            }));
            $backUrl = site_url('reports/sales/customer') . ($backQuery ? '?' . $backQuery : '');
        ?>
        <a href="<?= $backUrl; ?>"
           style="font-size:11px; padding:6px 10px; border-radius:999px; background:var(--tr-border); color:var(--tr-text); text-decoration:none;">
            Kembali ke laporan
        </a>
    </div>

    <?php
        $customerName = $customer['name'] ?? 'Tamu';
        $customerPhone = $customer['phone'] ?? '';
        $customerEmail = $customer['email'] ?? '';

        $totalOrders = (int) ($summary['total_orders'] ?? 0);
        $totalItems  = (float) ($summary['total_items'] ?? 0);
        $totalSales  = (float) ($summary['total_sales'] ?? 0);
        $totalCost   = (float) ($summary['total_cost'] ?? 0);
        $activeDays  = (int) ($summary['active_days'] ?? 0);
        $lastOrder   = $summary['last_order_date'] ?? '';

        $margin = $totalSales - $totalCost;
        $marginPct = $totalSales > 0 ? ($margin / $totalSales * 100.0) : 0;
        $avgOrders = $activeDays > 0 ? ($totalOrders / $activeDays) : 0;
        $marginColor = $margin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
    ?>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px; margin-bottom:14px; font-size:12px; color:var(--tr-text);">
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Customer</div>
            <div style="font-weight:600;"><?= esc($customerName); ?></div>
            <div style="color:var(--tr-muted-text); font-size:11px;">
                <?= esc($customerPhone !== '' ? $customerPhone : '-'); ?>
                <?= $customerEmail !== '' ? ' / ' . esc($customerEmail) : ''; ?>
            </div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Total Order</div>
            <div style="font-weight:700;"><?= number_format($totalOrders, 0, ',', '.'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Total Item</div>
            <div style="font-weight:700;"><?= number_format($totalItems, 2, ',', '.'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:rgba(122,154,108,0.14); border:1px solid var(--tr-primary);">
            <div style="color:var(--tr-secondary-green); font-size:11px;">Total Omzet</div>
            <div style="font-weight:700;">Rp <?= number_format($totalSales, 0, ',', '.'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Total HPP</div>
            <div style="font-weight:700;">Rp <?= number_format($totalCost, 0, ',', '.'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Margin</div>
            <div style="font-weight:700; color:<?= $marginColor; ?>;">
                Rp <?= number_format($margin, 0, ',', '.'); ?>
                <span style="color:var(--tr-muted-text); font-size:11px;">
                    (<?= number_format($marginPct, 1, ',', '.'); ?>%)
                </span>
            </div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Hari Aktif</div>
            <div style="font-weight:700;"><?= number_format($activeDays, 0, ',', '.'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Avg Order/Hari</div>
            <div style="font-weight:700;"><?= number_format($avgOrders, 2, ',', '.'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Order Terakhir</div>
            <div style="font-weight:700;"><?= esc($lastOrder !== '' ? $lastOrder : '-'); ?></div>
        </div>
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
                    <option value="<?= $opt; ?>" <?= ((int) $perPage === $opt) ? 'selected' : ''; ?>><?= $opt; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (! empty($mode)): ?>
            <input type="hidden" name="mode" value="<?= esc($mode); ?>">
        <?php endif; ?>

        <div style="display:flex; gap:6px;">
            <button type="submit"
                    style="margin-top:18px; padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:var(--tr-text); cursor:pointer;">
                Terapkan Filter
            </button>

            <a href="<?= current_url(); ?>"
               style="margin-top:18px; padding:6px 10px; border-radius:999px; border:1px solid var(--tr-muted-text); font-size:12px; background:var(--tr-bg); color:var(--tr-muted-text); text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">
            Belum ada transaksi untuk customer ini pada periode tersebut.
        </p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter transaksi:</div>
            <input type="text" id="salescustomerdetail-filter" placeholder="Cari invoice/metode..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:200px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Tanggal</th>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Invoice</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Item</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Omzet</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">HPP</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin %</th>
                <th style="text-align:center; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Metode</th>
                <th style="text-align:center; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Dapur</th>
                <th style="text-align:center; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
            </thead>
            <tbody id="salescustomerdetail-table-body">
            <?php foreach ($rows as $row): ?>
                <?php
                    $items = (float) ($row['total_items'] ?? 0);
                    $sales = (float) ($row['total_amount'] ?? 0);
                    $cost  = (float) ($row['total_cost'] ?? 0);
                    $rowMargin = $sales - $cost;
                    $rowMarginPct = $sales > 0 ? ($rowMargin / $sales * 100.0) : 0;
                    $rowMarginColor = $rowMargin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                    $method = strtolower((string) ($row['payment_method'] ?? 'cash'));
                    $methodLabel = $method === 'qris' ? 'QRIS' : 'Cash';
                    $kitchen = strtolower((string) ($row['kitchen_status'] ?? 'open'));
                    $kitchenLabel = $kitchen === 'done' ? 'Done' : 'Open';
                ?>
                <tr data-invoice="<?= esc(strtolower((string) ($row['invoice_no'] ?? ''))); ?>"
                    data-method="<?= esc(strtolower($methodLabel)); ?>"
                    data-status="<?= esc(strtolower($kitchenLabel)); ?>">
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['sale_date'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['invoice_no'] ?? '-'); ?>
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
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $rowMarginColor; ?>;">
                        Rp <?= number_format($rowMargin, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $rowMarginColor; ?>;">
                        <?= number_format($rowMarginPct, 1, ',', '.'); ?>%
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <?= esc($methodLabel); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <?= esc($kitchenLabel); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <a href="<?= site_url('transactions/sales/detail/' . $row['id']); ?>"
                           style="font-size:11px; padding:4px 8px; border-radius:999px; border:1px solid var(--tr-primary); color:var(--tr-primary); text-decoration:none;">
                            Detail
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr id="salescustomerdetail-noresult" style="display:none;">
                <td colspan="10" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
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
                    ? "Menampilkan {$startRow}-{$endRow} dari {$totalRows} transaksi"
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
                input: '#salescustomerdetail-filter',
                rows: document.querySelectorAll('#salescustomerdetail-table-body tr:not(#salescustomerdetail-noresult)'),
                noResult: '#salescustomerdetail-noresult',
                fields: ['invoice', 'method', 'status'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
