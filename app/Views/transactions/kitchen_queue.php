<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <h2 style="margin:0; font-size:18px;"><?= esc($title ?? 'Kitchen Queue'); ?></h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                <?= esc($subtitle ?? ''); ?>
            </p>
        </div>
        <div style="display:flex; gap:6px;">
            <a href="<?= site_url('transactions/sales'); ?>"
               style="font-size:11px; padding:6px 10px; border-radius:999px; background:var(--tr-border); color:var(--tr-text); text-decoration:none;">
                Riwayat Penjualan
            </a>
        </div>
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

    <form method="get" action="<?= current_url(); ?>" style="margin-bottom:10px; display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="kitchen-status" style="margin-bottom:2px; color:var(--tr-muted-text);">Status Dapur</label>
            <select id="kitchen-status" name="status"
                    style="min-width:160px; padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="open" <?= ($filter ?? 'open') === 'open' ? 'selected' : ''; ?>>Open</option>
                <option value="done" <?= ($filter ?? '') === 'done' ? 'selected' : ''; ?>>Done</option>
                <option value="all" <?= ($filter ?? '') === 'all' ? 'selected' : ''; ?>>Semua</option>
            </select>
        </div>
        <button type="submit"
                style="margin-top:18px; padding:6px 12px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:#fff; cursor:pointer;">
            Terapkan
        </button>
        <div style="margin-left:auto;">
            <input type="text" id="kitchen-filter" placeholder="Cari invoice/customer..."
                   style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:220px;">
        </div>
    </form>

    <?php if (empty($sales)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text);">Belum ada pesanan di kitchen queue.</p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Waktu</th>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Invoice</th>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Customer</th>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Status</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Total</th>
                <th style="text-align:center; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
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
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['created_at'] ?? $row['sale_date']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['invoice_no'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($customerLabel); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <span style="padding:2px 8px; border-radius:999px; font-size:11px; <?= $statusStyle; ?>">
                            <?= $statusLabel; ?>
                        </span>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format((float) ($row['total_amount'] ?? 0), 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="<?= site_url('transactions/sales/kitchen-ticket/' . $row['id']); ?>"
                               style="font-size:11px; padding:6px 10px; border-radius:999px; background:var(--tr-primary); color:#fff; text-decoration:none;">
                                Ticket
                            </a>
                            <?php if (! $isDone): ?>
                                <form method="post" action="<?= site_url('transactions/kitchen/done/' . $row['id']); ?>" onsubmit="return confirm('Tandai pesanan ini selesai?');">
                                    <?= csrf_field(); ?>
                                    <button type="submit"
                                            style="font-size:11px; padding:6px 10px; border-radius:999px; border:1px solid var(--tr-primary); background:rgba(122,154,108,0.14); color:var(--tr-primary); cursor:pointer;">
                                        Done
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="font-size:11px; color:var(--tr-muted-text);">Selesai</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr id="kitchen-noresult" style="display:none;">
                <td colspan="6" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
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
