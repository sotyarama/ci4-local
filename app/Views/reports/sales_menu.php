<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Laporan Penjualan per Menu</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
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
           style="font-size:11px; padding:6px 10px; border-radius:999px; border:1px solid #4b5563; background:#111827; color:#e5e7eb; text-decoration:none;">
            Export CSV
        </a>
    </div>

    <!-- Filter -->
    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_from" style="margin-bottom:2px; color:#d1d5db;">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from"
                   value="<?= esc($dateFrom); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid #374151; background:#020617; color:#e5e7eb; font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_to" style="margin-bottom:2px; color:#d1d5db;">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to"
                   value="<?= esc($dateTo); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid #374151; background:#020617; color:#e5e7eb; font-size:12px;">
        </div>

        <div style="display:flex; gap:6px;">
            <button type="submit"
                    style="margin-top:18px; padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:#2563eb; color:#e5e7eb; cursor:pointer;">
                Terapkan Filter
            </button>

            <a href="<?= site_url('reports/sales/menu'); ?>"
               style="margin-top:18px; padding:6px 10px; border-radius:999px; border:1px solid #4b5563; font-size:12px; background:#020617; color:#9ca3af; text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:#9ca3af; margin:8px 0 0;">
            Belum ada data penjualan untuk periode ini.
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Menu</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Qty</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Omzet</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">HPP</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Margin</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Margin %</th>
            </tr>
            </thead>
            <tbody>
            <?php
                $grandQty   = 0;
                $grandSales = 0;
                $grandCost  = 0;
            ?>
            <?php foreach ($rows as $r): ?>
                <?php
                    $qty    = (float) ($r['total_qty'] ?? 0);
                    $sales  = (float) ($r['total_sales'] ?? 0);
                    $cost   = (float) ($r['total_cost'] ?? 0);
                    $margin = $sales - $cost;
                    $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;

                    $grandQty   += $qty;
                    $grandSales += $sales;
                    $grandCost  += $cost;

                    $marginColor = $margin >= 0 ? '#6ee7b7' : '#fecaca';
                ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($r['menu_name'] ?? 'Menu'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        <?= number_format($qty, 2, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        Rp <?= number_format($sales, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        Rp <?= number_format($cost, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right; color:<?= $marginColor; ?>;">
                        Rp <?= number_format($margin, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right; color:<?= $marginColor; ?>;">
                        <?= number_format($marginPct, 1, ',', '.'); ?>%
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php
                $grandMargin = $grandSales - $grandCost;
                $grandMarginPct = $grandSales > 0 ? ($grandMargin / $grandSales * 100.0) : 0;
                $grandColor = $grandMargin >= 0 ? '#6ee7b7' : '#fecaca';
            ?>
            <tr>
                <td style="padding:6px 8px; border-top:1px solid #4b5563; font-weight:bold;">
                    TOTAL
                </td>
                <td style="padding:6px 8px; border-top:1px solid #4b5563; text-align:right; font-weight:bold;">
                    <?= number_format($grandQty, 2, ',', '.'); ?>
                </td>
                <td style="padding:6px 8px; border-top:1px solid #4b5563; text-align:right; font-weight:bold;">
                    Rp <?= number_format($grandSales, 0, ',', '.'); ?>
                </td>
                <td style="padding:6px 8px; border-top:1px solid #4b5563; text-align:right; font-weight:bold;">
                    Rp <?= number_format($grandCost, 0, ',', '.'); ?>
                </td>
                <td style="padding:6px 8px; border-top:1px solid #4b5563; text-align:right; font-weight:bold; color:<?= $grandColor; ?>;">
                    Rp <?= number_format($grandMargin, 0, ',', '.'); ?>
                </td>
                <td style="padding:6px 8px; border-top:1px solid #4b5563; text-align:right; font-weight:bold; color:<?= $grandColor; ?>;">
                    <?= number_format($grandMarginPct, 1, ',', '.'); ?>%
                </td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
