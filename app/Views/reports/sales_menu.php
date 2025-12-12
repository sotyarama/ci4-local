<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Laporan Penjualan per Menu</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Ringkasan qty, omzet, HPP, dan margin per menu untuk periode tertentu.
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

    <!-- Filter -->
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

        <div style="display:flex; gap:6px;">
            <button type="submit"
                    style="margin-top:18px; padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:var(--tr-text); cursor:pointer;">
                Terapkan Filter
            </button>

            <a href="<?= site_url('reports/sales/menu'); ?>"
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
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter nama menu:</div>
            <input type="text" id="salesmenu-filter" placeholder="Cari menu..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:200px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Menu</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Qty</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Omzet</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">HPP</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin %</th>
            </tr>
            </thead>
            <tbody id="salesmenu-table-body">
            <?php foreach ($rows as $r): ?>
                <?php
                    $qty    = (float) ($r['total_qty'] ?? 0);
                    $sales  = (float) ($r['total_sales'] ?? 0);
                    $cost   = (float) ($r['total_cost'] ?? 0);
                    $margin = $sales - $cost;
                    $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;

                    $marginColor = $margin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                ?>
                <tr data-name="<?= esc(strtolower($r['menu_name'] ?? 'menu')); ?>">
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($r['menu_name'] ?? 'Menu'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format($qty, 2, ',', '.'); ?>
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
                </tr>
            <?php endforeach; ?>

            <?php
                $grandQty   = (float) $totalQtyAll;
                $grandSales = (float) $totalSalesAll;
                $grandCost  = (float) $totalCostAll;
                $grandMargin = $grandSales - $grandCost;
                $grandMarginPct = $grandSales > 0 ? ($grandMargin / $grandSales * 100.0) : 0;
                $grandColor = $grandMargin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
            ?>
            <tr id="salesmenu-noresult" style="display:none;">
                <td colspan="6" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
            <tr>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); font-weight:bold;">
                    TOTAL (filter)
                </td>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">
                    <?= number_format($grandQty, 2, ',', '.'); ?>
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
            </tr>
            </tbody>
        </table>

        <?php
            $queryBase = [
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'per_page'  => $perPage,
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
                    ? "Menampilkan {$startRow}-{$endRow} dari {$totalRows} menu"
                    : "Tidak ada data untuk filter ini"; ?>
            </div>
            <div style="display:flex; gap:6px;">
                <a href="<?= $buildUrl(max(1, $page - 1)); ?>"
                   style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:<?= $page > 1 ? 'var(--tr-border)' : 'var(--tr-secondary-beige)'; ?>; color:<?= $page > 1 ? 'var(--tr-text)' : 'var(--tr-muted-text)'; ?>; text-decoration:none; pointer-events:<?= $page > 1 ? 'auto' : 'none'; ?>;">
                    ‹ Prev
                </a>
                <span style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); color:var(--tr-text);">
                    Halaman <?= $page; ?> / <?= max(1, $totalPages); ?>
                </span>
                <a href="<?= $buildUrl(min($totalPages, $page + 1)); ?>"
                   style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:<?= $page < $totalPages ? 'var(--tr-border)' : 'var(--tr-secondary-beige)'; ?>; color:<?= $page < $totalPages ? 'var(--tr-text)' : 'var(--tr-muted-text)'; ?>; text-decoration:none; pointer-events:<?= $page < $totalPages ? 'auto' : 'none'; ?>;">
                    Next ›
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
                input: '#salesmenu-filter',
                rows: document.querySelectorAll('#salesmenu-table-body tr:not(#salesmenu-noresult)'),
                noResult: '#salesmenu-noresult',
                fields: ['name'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
