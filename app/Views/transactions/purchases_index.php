<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="tr-card tr-card-outlined">
    <div class="tr-card-header">
        <div>
            <h2 class="tr-card-title" style="font-size:18px;">Pembelian Bahan Baku</h2>
            <p class="tr-card-subtitle">Daftar transaksi pembelian bahan baku dari supplier.</p>
        </div>
        <div class="tr-card-actions">
            <a href="<?= site_url('purchases/create'); ?>" class="tr-btn tr-btn-primary tr-btn-sm">
                <span class="tr-btn-label">+ Tambah Pembelian</span>
            </a>
        </div>
    </div>

    <div class="tr-card-body">

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
            <p class="tr-help" style="margin:0;">
                Belum ada data pembelian.
            </p>
        <?php else: ?>
            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:10px;">
                <div class="tr-help" style="margin:0;">Filter supplier/invoice/tanggal:</div>
                <input type="text" id="purchases-filter" placeholder="Cari pembelian..." class="tr-control" style="max-width:320px; height:36px;">
            </div>

            <div class="tr-table-wrap">
                <table class="tr-table tr-table-compact">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>No. Invoice</th>
                            <th class="is-right">Total</th>
                            <th class="is-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="purchases-table-body">
                        <?php foreach ($purchases as $p): ?>
                            <tr data-date="<?= esc(strtolower($p['purchase_date'])); ?>" data-supp="<?= esc(strtolower($p['supplier_name'] ?? '-')); ?>" data-invoice="<?= esc(strtolower($p['invoice_no'] ?? '')); ?>">
                                <td><?= esc($p['purchase_date']); ?></td>
                                <td><?= esc($p['supplier_name'] ?? '-'); ?></td>
                                <td><?= esc($p['invoice_no'] ?? ''); ?></td>
                                <td class="is-right">
                                    Rp <?= number_format((float) $p['total_amount'], 0, ',', '.'); ?>
                                </td>
                                <td class="is-center">
                                    <a href="<?= site_url('purchases/detail/' . $p['id']); ?>" class="tr-btn tr-btn-secondary tr-btn-sm">
                                        <span class="tr-btn-label">Detail</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr id="purchases-noresult" style="display:none;">
                            <td colspan="5" class="tr-table-empty">Tidak ada hasil.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
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
                fields: ['date', 'supp', 'invoice'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>