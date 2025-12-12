<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h2 style="margin:0; font-size:18px;">Pembelian Bahan Baku</h2>
        <a href="<?= site_url('purchases/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; text-decoration:none;">
            + Tambah Pembelian
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="background:rgba(122,154,108,0.14); border-radius:8px; padding:8px 10px; border:1px solid var(--tr-primary); font-size:12px; color:var(--tr-secondary-green); margin-bottom:12px;">
            <?= esc(session()->getFlashdata('message')); ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:var(--tr-accent-brown); border-radius:8px; padding:8px 10px; border:1px solid var(--tr-accent-brown); font-size:12px; color:var(--tr-secondary-beige); margin-bottom:12px;">
            <?= esc(session()->getFlashdata('error')); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($purchases)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:0;">
            Belum ada data pembelian.
        </p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter supplier/invoice/tanggal:</div>
            <input type="text" id="purchases-filter" placeholder="Cari pembelian..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:220px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Tanggal</th>
                <th style="text-align:left; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Supplier</th>
                <th style="text-align:left; padding:10px 8px; border-bottom:1px solid var(--tr-border);">No. Invoice</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Total</th>
                <th style="text-align:center; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
            </thead>
            <tbody id="purchases-table-body">
            <?php foreach ($purchases as $p): ?>
                <tr data-date="<?= esc(strtolower($p['purchase_date'])); ?>" data-supp="<?= esc(strtolower($p['supplier_name'] ?? '-')); ?>" data-invoice="<?= esc(strtolower($p['invoice_no'] ?? '')); ?>">
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($p['purchase_date']); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($p['supplier_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($p['invoice_no'] ?? ''); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format((float) $p['total_amount'], 0, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <a href="<?= site_url('purchases/detail/' . $p['id']); ?>"
                           style="font-size:11px; color:#fff; text-decoration:none; background:var(--tr-primary); border:1px solid var(--tr-primary); padding:5px 12px; border-radius:14px; display:inline-flex; align-items:center; justify-content:center; min-width:64px;">
                            Detail
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr id="purchases-noresult" style="display:none;">
                <td colspan="5" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    (function() {
        function init() {
            if (!window.App || !App.setupFilter) {
                return setTimeout(init, 50);
            }
            App.setupFilter({
                input: '#purchases-filter',
                rows: document.querySelectorAll('#purchases-table-body tr:not(#purchases-noresult)'),
                noResult: '#purchases-noresult',
                fields: ['date','supp','invoice'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
