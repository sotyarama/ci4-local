<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Riwayat Penjualan</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                Daftar transaksi penjualan beserta ringkasan margin.
            </p>
        </div>
        <a href="<?= site_url('transactions/sales/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:#22c55e; color:#022c22; text-decoration:none;">
            + Transaksi Baru
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:#022c22; border:1px solid #16a34a; color:#bbf7d0; font-size:12px;">
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($sales)): ?>
        <p style="font-size:12px; color:#9ca3af;">Belum ada data penjualan.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Tanggal</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Invoice</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Customer</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Total</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">HPP</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Margin</th>
                <th style="text-align:center; padding:6px 8px; border-bottom:1px solid #111827;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($sales as $row): ?>
                <?php
                    $total   = (float) ($row['total_amount'] ?? 0);
                    $cost    = (float) ($row['total_cost'] ?? 0);
                    $margin  = $total - $cost;
                    $marginPct = $total > 0 ? ($margin / $total * 100) : 0;
                    $marginColor = $margin >= 0 ? '#bbf7d0' : '#fecaca';
                ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['sale_date']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['invoice_no'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['customer_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        Rp <?= number_format($total, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        <?php if ($cost > 0): ?>
                            Rp <?= number_format($cost, 0, ',', '.'); ?>
                        <?php else: ?>
                            <span style="color:#9ca3af;">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        <span style="color:<?= $marginColor; ?>;">
                            Rp <?= number_format($margin, 0, ',', '.'); ?>
                            <?php if ($total > 0): ?>
                                <span style="color:#9ca3af; font-size:11px;">
                                    (<?= number_format($marginPct, 1, ',', '.'); ?>%)
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:center;">
                        <a href="<?= site_url('transactions/sales/detail/' . $row['id']); ?>"
                           style="font-size:11px; color:#60a5fa; text-decoration:none;">
                            Detail
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
