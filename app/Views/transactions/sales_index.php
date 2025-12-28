<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
// Guard for Intelephense + runtime safety
$sales = (isset($sales) && is_iterable($sales)) ? $sales : [];
?>

<div class="tr-card tr-card--outlined">
    <div class="tr-card__header">
        <div>
            <h2 class="tr-card__title" style="font-size:18px;">Riwayat Penjualan</h2>
            <p class="tr-card__subtitle">Daftar transaksi penjualan beserta ringkasan margin.</p>
        </div>

        <div class="tr-card__actions">
            <a href="<?= site_url('transactions/sales/create'); ?>" class="tr-btn tr-btn--primary tr-btn--sm">
                <span class="tr-btn__label">+ Transaksi Baru</span>
            </a>
        </div>
    </div>

    <div class="tr-card__body">

        <?php if (isset($todaySales)): ?>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px; margin-bottom:12px;">
                <div class="tr-card tr-card--flat" style="padding:10px; border:1px solid var(--tr-border); border-radius:12px; background:var(--tr-secondary-beige); box-shadow:none;">
                    <div class="tr-help" style="margin:0;">Tanggal</div>
                    <div style="font-size:14px; color:var(--tr-text); font-weight:700; margin-top:2px;">
                        <?= esc($todayDate ?? date('Y-m-d')); ?>
                    </div>
                </div>

                <div class="tr-card tr-card--flat" style="padding:10px; border:1px solid var(--tr-border); border-radius:12px; background:var(--tr-secondary-beige); box-shadow:none;">
                    <div class="tr-help" style="margin:0;">Total Penjualan (hari ini)</div>
                    <div style="font-size:16px; color:var(--tr-text); font-weight:800; margin-top:2px;">
                        Rp <?= number_format((float) ($todaySales ?? 0), 0, ',', '.'); ?>
                    </div>
                </div>

                <div class="tr-card tr-card--flat" style="padding:10px; border:1px solid var(--tr-border); border-radius:12px; background:var(--tr-secondary-beige); box-shadow:none;">
                    <div class="tr-help" style="margin:0;">Margin (hari ini)</div>
                    <?php
                    $marginToday     = (float) ($todayMargin ?? 0);
                    $marginPctToday  = (float) ($todayMarginPct ?? 0);
                    $marginColorCard = $marginToday >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                    ?>
                    <div style="font-size:16px; font-weight:800; margin-top:2px; color:<?= $marginColorCard; ?>;">
                        Rp <?= number_format($marginToday, 0, ',', '.'); ?>
                        <span style="font-size:11px; color:var(--tr-muted-text); margin-left:4px;">
                            (<?= number_format($marginPctToday, 1, ',', '.'); ?>%)
                        </span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('message')): ?>
            <div style="padding:10px 12px; margin-bottom:12px; border-radius:12px; background:rgba(122,154,108,0.14); border:1px solid var(--tr-primary); color:var(--tr-secondary-green); font-size:12px;">
                <?= session()->getFlashdata('message'); ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div style="padding:10px 12px; margin-bottom:12px; border-radius:12px; background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); color:var(--tr-accent-brown); font-size:12px;">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($sales)): ?>
            <p class="tr-help" style="margin:0;">Belum ada data penjualan.</p>
        <?php else: ?>

            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:10px;">
                <div class="tr-help" style="margin:0;">Filter invoice/tanggal/status/customer:</div>
                <input type="text"
                    id="sales-filter"
                    placeholder="Cari penjualan..."
                    class="tr-control"
                    style="max-width:320px; height:36px;">
            </div>

            <div class="tr-table-wrap">
                <table class="tr-table tr-table--compact">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th class="is-right">Total</th>
                            <th class="is-right">HPP</th>
                            <th class="is-right">Margin</th>
                            <th class="is-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="sales-table-body">
                        <?php foreach ($sales as $row): ?>
                            <?php
                            $total      = (float) ($row['total_amount'] ?? 0);
                            $cost       = (float) ($row['total_cost'] ?? 0);
                            $margin     = $total - $cost;
                            $marginPct  = $total > 0 ? ($margin / $total * 100) : 0;

                            $marginColor = $margin >= 0 ? 'var(--tr-secondary-green)' : 'var(--tr-accent-brown)';

                            $status      = $row['status'] ?? 'completed';
                            $isVoid      = ($status === 'void');
                            $statusLabel = $isVoid ? 'Void' : 'Completed';

                            // temporary badge style (nanti kalau B2.5 badges sudah ada, ganti ke class)
                            $statusStyle = $isVoid
                                ? 'background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown);'
                                : 'background:rgba(122,154,108,0.14); color:var(--tr-primary); border:1px solid var(--tr-primary);';

                            $customerLabel = (($row['customer_name'] ?? '') !== '') ? $row['customer_name'] : 'Tamu';
                            $saleId = (int) ($row['id'] ?? 0);
                            ?>

                            <tr data-date="<?= esc(strtolower($row['sale_date'] ?? '')); ?>"
                                data-invoice="<?= esc(strtolower($row['invoice_no'] ?? '')); ?>"
                                data-customer="<?= esc(strtolower($customerLabel)); ?>"
                                data-status="<?= esc(strtolower($status)); ?>">

                                <td><?= esc($row['sale_date'] ?? '-'); ?></td>
                                <td><?= esc($row['invoice_no'] ?? '-'); ?></td>
                                <td><?= esc($customerLabel); ?></td>

                                <td>
                                    <span style="display:inline-flex; align-items:center; padding:2px 10px; border-radius:999px; font-size:11px; <?= $statusStyle; ?>">
                                        <?= esc($statusLabel); ?>
                                    </span>
                                </td>

                                <td class="is-right">Rp <?= number_format($total, 0, ',', '.'); ?></td>

                                <td class="is-right">
                                    <?php if ($cost > 0): ?>
                                        Rp <?= number_format($cost, 0, ',', '.'); ?>
                                    <?php else: ?>
                                        <span class="is-muted">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="is-right">
                                    <span style="color:<?= $marginColor; ?>; font-weight:700;">
                                        Rp <?= number_format($margin, 0, ',', '.'); ?>
                                    </span>
                                    <?php if ($total > 0): ?>
                                        <span class="is-muted" style="font-size:11px;">
                                            (<?= number_format($marginPct, 1, ',', '.'); ?>%)
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="is-center">
                                    <div style="display:inline-flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                                        <a href="<?= site_url('transactions/sales/detail/' . $saleId); ?>"
                                            class="tr-btn tr-btn--secondary tr-btn--sm">
                                            <span class="tr-btn__label">Detail</span>
                                        </a>

                                        <?php if (! $isVoid): ?>
                                            <button type="button"
                                                class="tr-btn tr-btn--danger tr-btn--sm btn-void"
                                                data-url="<?= site_url('transactions/sales/void/' . $saleId); ?>">
                                                <span class="tr-btn__label">Void</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr id="sales-noresult" style="display:none;">
                            <td colspan="8" class="tr-table-empty">Tidak ada hasil.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </div>
</div>

<!-- Modal Void (sementara masih inline; nanti B2 modal kita rapikan) -->
<div id="void-modal"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:9998; align-items:center; justify-content:center; padding:16px;">
    <div class="tr-card" style="max-width:420px; width:100%;">
        <div class="tr-card__header">
            <div>
                <div class="tr-card__title" style="font-size:16px;">Void Transaksi</div>
                <div class="tr-card__subtitle">Isi alasan (opsional) lalu konfirmasi.</div>
            </div>
            <button type="button" id="void-close" class="tr-btn tr-btn--ghost tr-btn--sm" style="height:32px; padding:0 10px;">×</button>
        </div>

        <div class="tr-card__body">
            <form id="void-form" method="post" action="" style="display:flex; flex-direction:column; gap:10px;">
                <?= csrf_field(); ?>

                <textarea name="void_reason" id="void-reason" rows="3" placeholder="Alasan void (opsional)" class="tr-control"></textarea>

                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:2px;">
                    <button type="button" id="void-cancel" class="tr-btn tr-btn--secondary">
                        <span class="tr-btn__label">Batal</span>
                    </button>
                    <button type="submit" class="tr-btn tr-btn--danger">
                        <span class="tr-btn__label">Void &amp; Kembalikan Stok</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        function init() {
            if (!window.App || !App.setupFilter) return setTimeout(init, 50);

            App.setupFilter({
                input: '#sales-filter',
                rows: document.querySelectorAll('#sales-table-body tr:not(#sales-noresult)'),
                noResult: '#sales-noresult',
                fields: ['date', 'invoice', 'customer', 'status'],
                debounce: 200
            });

            var modal = document.getElementById('void-modal');
            var form = document.getElementById('void-form');
            var reason = document.getElementById('void-reason');
            var closeBtn = document.getElementById('void-close');
            var cancelBtn = document.getElementById('void-cancel');

            function openModal(url) {
                form.action = url;
                reason.value = '';
                modal.style.display = 'flex';
                reason.focus();
            }

            function closeModal() {
                modal.style.display = 'none';
                form.action = '';
            }

            document.querySelectorAll('.btn-void').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal(btn.getAttribute('data-url'));
                });
            });

            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeModal();
            });

            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });
        }

        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>