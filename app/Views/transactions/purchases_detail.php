<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Detail Pembelian</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Rincian pembelian bahan baku dan total biaya.
            </p>
        </div>
        <a href="<?= site_url('purchases'); ?>"
           style="font-size:11px; padding:6px 10px; border-radius:999px; background:var(--tr-border); color:var(--tr-text); text-decoration:none;">
            ← Kembali ke daftar
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:rgba(122,154,108,0.14); border:1px solid var(--tr-primary); color:var(--tr-secondary-green); font-size:12px;">
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); color:var(--tr-accent-brown); font-size:12px;">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px; margin-bottom:16px; font-size:12px; color:var(--tr-text);">
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Tanggal</div>
            <div style="font-weight:600;"><?= esc($header['purchase_date']); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Supplier</div>
            <div style="font-weight:600;"><?= esc($header['supplier_name'] ?? '-'); ?></div>
            <?php if (! empty($header['phone']) || ! empty($header['address'])): ?>
                <div style="color:var(--tr-muted-text); margin-top:4px; line-height:1.4;">
                    <?php if (! empty($header['phone'])): ?>
                        Telp: <?= esc($header['phone']); ?><br>
                    <?php endif; ?>
                    <?php if (! empty($header['address'])): ?>
                        <?= esc($header['address']); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">No. Invoice</div>
            <div style="font-weight:600;"><?= esc($header['invoice_no'] ?? '-'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:rgba(122,154,108,0.14); border:1px solid var(--tr-primary);">
            <div style="color:var(--tr-secondary-green); font-size:11px;">Total Pembelian</div>
            <div style="font-weight:700;">
                Rp <?= number_format((float) ($header['total_amount'] ?? 0), 0, ',', '.'); ?>
            </div>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text);">Tidak ada item untuk pembelian ini.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:12px;">
            <thead>
            <tr>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:left;">Bahan</th>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">Qty</th>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">Harga Satuan</th>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <?php $grandTotal = 0; ?>
            <?php foreach ($items as $row): ?>
                <?php
                    $qty        = (float) ($row['qty'] ?? 0);
                    $unitCost   = (float) ($row['unit_cost'] ?? 0);
                    $totalCost  = (float) ($row['total_cost'] ?? ($qty * $unitCost));
                    $grandTotal += $totalCost;
                ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['material_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format($qty, 2, ',', '.'); ?>
                        <?php if (! empty($row['unit_short'])): ?>
                            <span style="color:var(--tr-muted-text);"> <?= esc($row['unit_short']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($unitCost, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($totalCost, 0, ',', '.'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" style="padding:8px 8px; text-align:right; font-weight:700; border-top:1px solid var(--tr-border);">Total</td>
                <td style="padding:8px 8px; text-align:right; font-weight:700; border-top:1px solid var(--tr-border);">
                    Rp <?= number_format($grandTotal, 0, ',', '.'); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <?php if (! empty($header['notes'])): ?>
        <div style="margin-top:16px; font-size:12px; color:var(--tr-text);">
            <div style="font-weight:600; margin-bottom:4px;">Catatan</div>
            <div style="color:var(--tr-muted-text);"><?= nl2br(esc($header['notes'])); ?></div>
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>
