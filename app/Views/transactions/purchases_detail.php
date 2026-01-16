<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="tr-card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Detail Pembelian</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Rincian pembelian bahan baku dan total biaya.
            </p>
        </div>
        <a href="<?= site_url('purchases'); ?>" class="tr-btn tr-btn-secondary tr-btn-sm">
            <span class="tr-btn-label">← Kembali ke daftar</span>
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
        <p class="tr-help" style="margin:0;">Tidak ada item untuk pembelian ini.</p>
    <?php else: ?>
        <table class="tr-table tr-table-compact" style="margin-bottom:12px;">
            <thead>
                <tr>
                    <th>Bahan</th>
                    <th class="is-right">Qty</th>
                    <th class="is-right">Harga Satuan</th>
                    <th class="is-right">Subtotal</th>
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
                        <td>
                            <?= esc($row['material_name'] ?? '-'); ?>
                            <?php if (! empty($row['brand_name'])): ?>
                                <div style="font-size:11px; color:var(--tr-muted-text);">
                                    Brand: <?= esc($row['brand_name']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (! empty($row['variant_name'])): ?>
                                <div style="font-size:11px; color:var(--tr-muted-text);">
                                    Varian: <?= esc($row['variant_name']); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="is-right">
                            <?= number_format($qty, 2, ',', '.'); ?>
                            <?php if (! empty($row['unit_short'])): ?>
                                <span class="is-muted"> <?= esc($row['unit_short']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="is-right">
                            Rp <?= number_format($unitCost, 0, ',', '.'); ?>
                        </td>
                        <td class="is-right">
                            Rp <?= number_format($totalCost, 0, ',', '.'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="is-right" style="font-weight:700;">Total</td>
                    <td class="is-right" style="font-weight:700;">
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