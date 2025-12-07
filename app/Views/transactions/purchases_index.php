<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h2 style="margin:0; font-size:18px;">Pembelian Bahan Baku</h2>
        <a href="<?= site_url('purchases/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:#22c55e; color:#022c22; text-decoration:none;">
            + Tambah Pembelian
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="background:#022c22; border-radius:8px; padding:8px 10px; border:1px solid #16a34a; font-size:12px; color:#bbf7d0; margin-bottom:12px;">
            <?= esc(session()->getFlashdata('message')); ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:#7f1d1d; border-radius:8px; padding:8px 10px; border:1px solid #b91c1c; font-size:12px; color:#fee2e2; margin-bottom:12px;">
            <?= esc(session()->getFlashdata('error')); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($purchases)): ?>
        <p style="font-size:12px; color:#9ca3af; margin:0;">
            Belum ada data pembelian.
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #111827;">Tanggal</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #111827;">Supplier</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #111827;">No. Invoice</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid #111827;">Total</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid #111827;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($purchases as $p): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827;">
                        <?= esc($p['purchase_date']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827;">
                        <?= esc($p['supplier_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827;">
                        <?= esc($p['invoice_no'] ?? ''); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">
                        Rp <?= number_format((float) $p['total_amount'], 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:center;">
                        <a href="<?= site_url('purchases/detail/' . $p['id']); ?>"
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
