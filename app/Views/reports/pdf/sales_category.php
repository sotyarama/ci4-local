<?= $this->extend('reports/pdf/layout') ?>

<?= $this->section('content') ?>
<?php
    $grandQty = (float) ($totalQtyAll ?? 0);
    $grandSales = (float) ($totalSalesAll ?? 0);
    $grandCost = (float) ($totalCostAll ?? 0);
    $grandMargin = $grandSales - $grandCost;
    $grandMarginPct = $grandSales > 0 ? ($grandMargin / $grandSales * 100.0) : 0;
?>

<?php if (empty($rows)): ?>
    <div class="note">Tidak ada data untuk periode ini.</div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="num">Qty</th>
                <th class="num">Omzet</th>
                <th class="num">HPP</th>
                <th class="num">Margin</th>
                <th class="num">Margin %</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <?php
                    $qty = (float) ($row['total_qty'] ?? 0);
                    $sales = (float) ($row['total_sales'] ?? 0);
                    $cost = (float) ($row['total_cost'] ?? 0);
                    $margin = $sales - $cost;
                    $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;
                ?>
                <tr>
                    <td><?= esc($row['category_name'] ?? 'Kategori'); ?></td>
                    <td class="num"><?= number_format($qty, 0, ',', '.'); ?></td>
                    <td class="num">Rp <?= number_format($sales, 0, ',', '.'); ?></td>
                    <td class="num">Rp <?= number_format($cost, 0, ',', '.'); ?></td>
                    <td class="num">Rp <?= number_format($margin, 0, ',', '.'); ?></td>
                    <td class="num"><?= number_format($marginPct, 1, ',', '.'); ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL</td>
                <td class="num"><?= number_format($grandQty, 0, ',', '.'); ?></td>
                <td class="num">Rp <?= number_format($grandSales, 0, ',', '.'); ?></td>
                <td class="num">Rp <?= number_format($grandCost, 0, ',', '.'); ?></td>
                <td class="num">Rp <?= number_format($grandMargin, 0, ',', '.'); ?></td>
                <td class="num"><?= number_format($grandMarginPct, 1, ',', '.'); ?>%</td>
            </tr>
        </tfoot>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>
