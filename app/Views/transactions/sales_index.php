<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h2 style="margin:0; font-size:18px;">Riwayat Penjualan</h2>
        <a href="<?= site_url('transactions/sales/create'); ?>"
           style="font-size:12px; padding:6px 12px; border-radius:999px; background:#22c55e; color:#022c22; text-decoration:none;">
            + Input Penjualan
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="padding:8px; margin-bottom:12px; background:#064e3b; color:#6ee7b7; border-radius:6px; font-size:12px;">
            <?= session('message') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($sales)): ?>
        <p style="font-size:12px; color:#9ca3af;">Belum ada transaksi penjualan.</p>
    <?php else: ?>

        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="padding:8px; border-bottom:1px solid #111827; text-align:left;">Tanggal</th>
                <th style="padding:8px; border-bottom:1px solid #111827; text-align:left;">Invoice</th>
                <th style="padding:8px; border-bottom:1px solid #111827; text-align:right;">Total</th>
                <th style="padding:8px; border-bottom:1px solid #111827; text-align:center;">Aksi</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($sales as $row): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['sale_date']) ?>
                    </td>

                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['invoice_no'] ?? '-') ?>
                    </td>

                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        Rp <?= number_format($row['total_amount'], 0, ',', '.') ?>
                    </td>

                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:center;">
                        <a href="<?= site_url('transactions/sales/detail/' . $row['id']); ?>"
                           style="color:#60a5fa; font-size:11px; text-decoration:none;">
                            Lihat
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>
