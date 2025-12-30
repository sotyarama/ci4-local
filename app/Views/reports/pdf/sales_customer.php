<?= $this->extend('reports/pdf/layout') ?>

<?= $this->section('content') ?>
<?php
    $showFull = ($mode ?? 'full') === 'full';
    $grandOrders = (int) ($totalOrdersAll ?? 0);
    $grandItems = (float) ($totalItemsAll ?? 0);
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
                <th>Customer</th>
                <?php if ($showFull): ?>
                    <th>Telepon</th>
                    <th>Email</th>
                <?php endif; ?>
                <th class="num">Order</th>
                <th class="num">Item</th>
                <th class="num">Omzet</th>
                <th class="num">HPP</th>
                <th class="num">Margin</th>
                <th class="num">Margin %</th>
                <?php if ($showFull): ?>
                    <th class="num">Hari Aktif</th>
                    <th class="num">Avg Order/Hari</th>
                    <th>Order Terakhir</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <?php
                    $orders = (int) ($row['total_orders'] ?? 0);
                    $items = (float) ($row['total_items'] ?? 0);
                    $sales = (float) ($row['total_sales'] ?? 0);
                    $cost = (float) ($row['total_cost'] ?? 0);
                    $margin = $sales - $cost;
                    $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;
                    $activeDays = (int) ($row['active_days'] ?? 0);
                    $avgOrders = $activeDays > 0 ? ($orders / $activeDays) : 0;
                ?>
                <tr>
                    <td><?= esc($row['customer_name'] ?? 'Tamu'); ?></td>
                    <?php if ($showFull): ?>
                        <td><?= esc($row['customer_phone'] ?? '-'); ?></td>
                        <td><?= esc($row['customer_email'] ?? '-'); ?></td>
                    <?php endif; ?>
                    <td class="num"><?= number_format($orders, 0, ',', '.'); ?></td>
                    <td class="num"><?= number_format($items, 2, ',', '.'); ?></td>
                    <td class="num">Rp <?= number_format($sales, 0, ',', '.'); ?></td>
                    <td class="num">Rp <?= number_format($cost, 0, ',', '.'); ?></td>
                    <td class="num">Rp <?= number_format($margin, 0, ',', '.'); ?></td>
                    <td class="num"><?= number_format($marginPct, 1, ',', '.'); ?>%</td>
                    <?php if ($showFull): ?>
                        <td class="num"><?= number_format($activeDays, 0, ',', '.'); ?></td>
                        <td class="num"><?= number_format($avgOrders, 2, ',', '.'); ?></td>
                        <td><?= esc($row['last_order_date'] ?? '-'); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL</td>
                <?php if ($showFull): ?>
                    <td>-</td>
                    <td>-</td>
                <?php endif; ?>
                <td class="num"><?= number_format($grandOrders, 0, ',', '.'); ?></td>
                <td class="num"><?= number_format($grandItems, 2, ',', '.'); ?></td>
                <td class="num">Rp <?= number_format($grandSales, 0, ',', '.'); ?></td>
                <td class="num">Rp <?= number_format($grandCost, 0, ',', '.'); ?></td>
                <td class="num">Rp <?= number_format($grandMargin, 0, ',', '.'); ?></td>
                <td class="num"><?= number_format($grandMarginPct, 1, ',', '.'); ?>%</td>
                <?php if ($showFull): ?>
                    <td class="num">-</td>
                    <td class="num">-</td>
                    <td>-</td>
                <?php endif; ?>
            </tr>
        </tfoot>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>
