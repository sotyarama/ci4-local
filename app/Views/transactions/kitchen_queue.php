<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="tr-card tr-card-outlined">
    <div class="tr-card-header">
        <div>
            <h2 class="tr-card-title" style="font-size:18px;"><?= esc($title ?? 'Kitchen Queue'); ?></h2>
            <p class="tr-card-subtitle"><?= esc($subtitle ?? 'Antrian pesanan untuk dapur'); ?></p>
        </div>
        <div class="tr-card-actions">
            <a href="<?= site_url('transactions/sales'); ?>" class="tr-btn tr-btn-secondary tr-btn-sm">
                <span class="tr-btn-label">Riwayat Penjualan</span>
            </a>
        </div>
    </div>

    <div class="tr-card-body">

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

        <form method="get" action="<?= current_url(); ?>" style="margin-bottom:10px; display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
            <div style="display:flex; flex-direction:column; font-size:12px;">
                <label for="kitchen-status" style="margin-bottom:2px; color:var(--tr-muted-text);">Status Dapur</label>
                <select id="kitchen-status" name="status" class="tr-control" style="min-width:160px;">
                    <option value="open" <?= ($filter ?? 'open') === 'open' ? 'selected' : ''; ?>>Open</option>
                    <option value="done" <?= ($filter ?? '') === 'done' ? 'selected' : ''; ?>>Done</option>
                    <option value="all" <?= ($filter ?? '') === 'all' ? 'selected' : ''; ?>>Semua</option>
                </select>
            </div>
            <button type="submit" class="tr-btn tr-btn-primary tr-btn-sm" style="margin-top:18px;">
                <span class="tr-btn-label">Terapkan</span>
            </button>
            <div style="margin-left:auto;">
                <input type="text" id="kitchen-filter" placeholder="Cari invoice/customer..." class="tr-control" style="min-width:220px;">
            </div>
        </form>

        <?php if (empty($sales)): ?>
            <p class="tr-help" style="margin:0;">Belum ada pesanan di kitchen queue.</p>
        <?php else: ?>
            <div class="tr-table-wrap">
                <table class="tr-table tr-table-compact">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th class="is-right">Total</th>
                            <th class="is-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kitchen-table-body">
                        <?php foreach ($sales as $row): ?>
                            <?php
                            $customerLabel = ($row['customer_name'] ?? '') !== '' ? $row['customer_name'] : 'Tamu';
                            $kStatus = $row['kitchen_status'] ?? 'open';
                            $isDone = $kStatus === 'done';
                            $statusStyle = $isDone
                                ? 'background:rgba(122,154,108,0.14); color:var(--tr-primary); border:1px solid var(--tr-primary);'
                                : 'background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown);';
                            $statusLabel = $isDone ? 'Done' : 'Open';
                            ?>
                            <tr data-invoice="<?= esc(strtolower($row['invoice_no'] ?? '')); ?>"
                                data-customer="<?= esc(strtolower($customerLabel)); ?>">
                                <td><?= esc($row['created_at'] ?? $row['sale_date']); ?></td>
                                <td><?= esc($row['invoice_no'] ?? '-'); ?></td>
                                <td><?= esc($customerLabel); ?></td>
                                <td>
                                    <span style="padding:2px 8px; border-radius:999px; font-size:11px; <?= $statusStyle; ?>">
                                        <?= $statusLabel; ?>
                                    </span>
                                </td>
                                <td class="is-right">
                                    Rp <?= number_format((float) ($row['total_amount'] ?? 0), 0, ',', '.'); ?>
                                </td>
                                <td class="is-center">
                                    <div style="display:flex; gap:6px; justify-content:center;">
                                        <a href="<?= site_url('transactions/sales/kitchen-ticket/' . $row['id']); ?>" class="tr-btn tr-btn-primary tr-btn-sm">
                                            <span class="tr-btn-label">Ticket</span>
                                        </a>
                                        <?php if (! $isDone): ?>
                                            <form method="post" action="<?= site_url('transactions/kitchen/done/' . $row['id']); ?>" onsubmit="return confirm('Tandai pesanan ini selesai?');">
                                                <?= csrf_field(); ?>
                                                <button type="submit" class="tr-btn tr-btn-primary tr-btn-sm">
                                                    <span class="tr-btn-label">Done</span>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="is-muted" style="font-size:11px;">Selesai</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr id="kitchen-noresult" style="display:none;">
                            <td colspan="6" class="tr-table-empty">Tidak ada hasil.</td>
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
                input: '#kitchen-filter',
                rows: document.querySelectorAll('#kitchen-table-body tr:not(#kitchen-noresult)'),
                noResult: '#kitchen-noresult',
                fields: ['invoice', 'customer'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>