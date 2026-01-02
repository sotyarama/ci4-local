<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;"><?= esc($title); ?></h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                <?= esc($subtitle); ?>
            </p>
        </div>
        <?php
        $exportBase = [
            'start'      => $startDate,
            'end'        => $endDate,
            'allday'     => $allDay ? 1 : 0,
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'group'      => $group,
        ];
        $csvParams = array_merge($exportBase, ['export' => 'csv']);
        $csvQuery = http_build_query(array_filter($csvParams, function ($v) {
            return $v !== null && $v !== '';
        }));
        $csvUrl = current_url() . ($csvQuery ? '?' . $csvQuery : '');
        $pdfParams = array_merge($exportBase, ['export' => 'pdf']);
        $pdfQuery = http_build_query(array_filter($pdfParams, function ($v) {
            return $v !== null && $v !== '';
        }));
        $pdfUrl = current_url() . ($pdfQuery ? '?' . $pdfQuery : '');
        ?>
        <div style="display:flex; gap:6px;">
            <a href="<?= $csvUrl; ?>"
                style="font-size:11px; padding:6px 10px; border-radius:999px; border:1px solid var(--tr-muted-text); background:var(--tr-border); color:var(--tr-text); text-decoration:none;">
                Export CSV
            </a>
            <a href="<?= $pdfUrl; ?>"
                style="font-size:11px; padding:6px 10px; border-radius:999px; border:1px solid var(--tr-muted-text); background:var(--tr-secondary-beige); color:var(--tr-text); text-decoration:none;">
                Export PDF
            </a>
        </div>
    </div>

    <?= $this->include('partials/date_range_picker', ['mode' => 'datetime']) ?>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text);">Tidak ada data untuk filter ini.</p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter periode (mis. 2025-12 atau 2025-W50 atau tanggal):</div>
            <input type="text" id="time-filter" placeholder="Cari periode..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:200px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Periode</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Total Penjualan</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Total HPP</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin %</th>
                </tr>
            </thead>
            <tbody id="time-table-body">
                <?php foreach ($rows as $r): ?>
                    <?php
                    $sales = (float) ($r['total_sales'] ?? 0);
                    $cost  = (float) ($r['total_cost'] ?? 0);
                    $margin = $sales - $cost;
                    $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;
                    $marginColor = $margin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                    ?>
                    <tr data-period="<?= esc(strtolower($r['period'] ?? '')); ?>">
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?= esc($r['period'] ?? ''); ?>
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
                $grandSales = (float) $totalSalesAll;
                $grandCost  = (float) $totalCostAll;
                $grandMargin = $grandSales - $grandCost;
                $grandMarginPct = $grandSales > 0 ? ($grandMargin / $grandSales * 100.0) : 0;
                $grandColor = $grandMargin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                ?>
                <tr>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); font-weight:bold;">
                        TOTAL (filter)
                    </td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">
                        Rp <?= number_format($grandSales, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">
                        Rp <?= number_format($grandCost, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold; color:<?= $grandColor; ?>">
                        Rp <?= number_format($grandMargin, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold; color:<?= $grandColor; ?>">
                        <?= number_format($grandMarginPct, 1, ',', '.'); ?>%
                    </td>
                </tr>
                <tr id="time-noresult" style="display:none;">
                    <td colspan="5" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
                </tr>
            </tbody>
        </table>

        <?php
        $queryBase = [
            'start'     => $startDate,
            'end'       => $endDate,
            'allday'    => $allDay ? 1 : 0,
            'start_time' => $startTime,
            'end_time'  => $endTime,
            'per_page'  => $perPage,
            'group'     => $group,
        ];
        $buildUrl = static function (int $targetPage) use ($queryBase): string {
            $params = array_merge($queryBase, ['page' => $targetPage]);
            $params = array_filter($params, static function ($v) {
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
                    ? "Menampilkan {$startRow}-{$endRow} dari {$totalRows} periode"
                    : "Tidak ada data untuk filter ini"; ?>
            </div>
            <div style="display:flex; gap:6px;">
                <a href="<?= $buildUrl(max(1, $page - 1)); ?>"
                    style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:<?= $page > 1 ? 'var(--tr-border)' : 'var(--tr-secondary-beige)'; ?>; color:<?= $page > 1 ? 'var(--tr-text)' : 'var(--tr-muted-text)'; ?>; text-decoration:none; pointer-events:<?= $page > 1 ? 'auto' : 'none'; ?>;">
                    ƒ?û Prev
                </a>
                <span style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); color:var(--tr-text);">
                    Halaman <?= $page; ?> / <?= max(1, $totalPages); ?>
                </span>
                <a href="<?= $buildUrl(min($totalPages, $page + 1)); ?>"
                    style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:<?= $page < $totalPages ? 'var(--tr-border)' : 'var(--tr-secondary-beige)'; ?>; color:<?= $page < $totalPages ? 'var(--tr-text)' : 'var(--tr-muted-text)'; ?>; text-decoration:none; pointer-events:<?= $page < $totalPages ? 'auto' : 'none'; ?>;">
                    Next ƒ?§
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterInput = document.getElementById('time-filter');
        const tableRows = Array.from(document.querySelectorAll('#time-table-body tr[data-period]'));
        const noResultRow = document.getElementById('time-noresult');

        if (filterInput) {
            filterInput.addEventListener('input', function() {
                const q = filterInput.value.trim().toLowerCase();
                let shown = 0;
                tableRows.forEach(function(tr) {
                    const period = (tr.getAttribute('data-period') || '').toLowerCase();
                    const match = period.indexOf(q) !== -1;
                    tr.style.display = match ? '' : 'none';
                    if (match) shown++;
                });
                if (noResultRow) {
                    noResultRow.style.display = shown === 0 ? '' : 'none';
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>