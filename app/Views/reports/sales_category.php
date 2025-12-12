<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Penjualan per Kategori Menu</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Qty, omzet, HPP, dan margin per kategori untuk periode tertentu.
            </p>
        </div>
    </div>

    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">
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
            <a href="<?= site_url('reports/sales/category'); ?>"
               style="padding:7px 12px; border-radius:999px; border:1px solid var(--tr-muted-text); font-size:12px; background:var(--tr-bg); color:var(--tr-muted-text); text-decoration:none;">
                Reset
            </a>
            <a href="<?= current_url() . '?' . http_build_query(['date_from' => $dateFrom, 'date_to' => $dateTo, 'export' => 'csv']); ?>"
               style="padding:7px 12px; border-radius:999px; border:1px solid var(--tr-primary); font-size:12px; background:var(--tr-bg); color:var(--tr-primary); text-decoration:none;">
                Export CSV
            </a>
        </div>
        <div style="display:flex; flex-direction:column; font-size:12px; min-width:160px;">
            <label for="per_page" style="margin-bottom:2px; color:var(--tr-muted-text);">Baris per halaman</label>
            <select name="per_page" id="per_page"
                    style="padding:6px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <?php foreach ([20, 50, 100, 200] as $opt): ?>
                    <option value="<?= $opt; ?>" <?= (int)$perPage === (int)$opt ? 'selected' : ''; ?>><?= $opt; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">
            Belum ada data penjualan pada periode ini.
        </p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter kategori:</div>
            <input type="text" id="salescat-filter" placeholder="Cari kategori..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:200px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Kategori</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Qty</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Omzet</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">HPP</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Margin</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Margin %</th>
            </tr>
            </thead>
            <tbody id="salescat-table-body">
            <?php foreach ($rows as $row): ?>
                <?php
                    $qty    = (float) ($row['total_qty'] ?? 0);
                    $sales  = (float) ($row['total_sales'] ?? 0);
                    $cost   = (float) ($row['total_cost'] ?? 0);
                    $margin = $sales - $cost;
                    $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;
                    $marginColor = $margin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                ?>
                <tr data-cat="<?= esc(strtolower($row['category_name'])); ?>">
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['category_name']); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format($qty, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($sales, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($cost, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $marginColor; ?>;">
                        Rp <?= number_format($margin, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $marginColor; ?>;">
                        <?= number_format($marginPct, 1, ',', '.'); ?>%
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td style="padding:10px 8px; border-top:1px solid var(--tr-border); font-weight:700;">TOTAL</td>
                <td style="padding:10px 8px; border-top:1px solid var(--tr-border); font-weight:700; text-align:right;">
                    <?= number_format($totalQtyAll, 0, ',', '.'); ?>
                </td>
                <td style="padding:10px 8px; border-top:1px solid var(--tr-border); font-weight:700; text-align:right;">
                    Rp <?= number_format($totalSalesAll, 0, ',', '.'); ?>
                </td>
                <td style="padding:10px 8px; border-top:1px solid var(--tr-border); font-weight:700; text-align:right;">
                    Rp <?= number_format($totalCostAll, 0, ',', '.'); ?>
                </td>
                <?php
                    $totalMargin = $totalSalesAll - $totalCostAll;
                    $totalMarginPct = $totalSalesAll > 0 ? ($totalMargin / $totalSalesAll * 100.0) : 0;
                    $totalMarginColor = $totalMargin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                ?>
                <td style="padding:10px 8px; border-top:1px solid var(--tr-border); font-weight:700; text-align:right; color:<?= $totalMarginColor; ?>;">
                    Rp <?= number_format($totalMargin, 0, ',', '.'); ?>
                </td>
                <td style="padding:10px 8px; border-top:1px solid var(--tr-border); font-weight:700; text-align:right; color:<?= $totalMarginColor; ?>;">
                    <?= number_format($totalMarginPct, 1, ',', '.'); ?>%
                </td>
            </tr>
            <tr id="salescat-noresult" style="display:none;">
                <td colspan="6" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div style="margin-top:12px; display:flex; gap:8px; align-items:center; font-size:12px; color:var(--tr-muted-text);">
                <span>Halaman <?= $page; ?> dari <?= $totalPages; ?></span>
                <?php
                    $queryBase = [
                        'date_from' => $dateFrom,
                        'date_to'   => $dateTo,
                        'per_page'  => $perPage,
                    ];
                ?>
                <?php if ($page > 1): ?>
                    <a style="text-decoration:none; color:var(--tr-primary);" href="<?= current_url() . '?' . http_build_query(array_merge($queryBase, ['page' => $page - 1])); ?>">‹ Sebelumnya</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a style="text-decoration:none; color:var(--tr-primary);" href="<?= current_url() . '?' . http_build_query(array_merge($queryBase, ['page' => $page + 1])); ?>">Berikutnya ›</a>
                <?php endif; ?>
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
                input: '#salescat-filter',
                rows: document.querySelectorAll('#salescat-table-body tr:not(#salescat-noresult)'),
                noResult: '#salescat-noresult',
                fields: ['cat'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
