<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Riwayat Penjualan</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Daftar transaksi penjualan beserta ringkasan margin.
            </p>
        </div>
        <a href="<?= site_url('transactions/sales/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; text-decoration:none;">
            + Transaksi Baru
        </a>
    </div>

    <?php if (isset($todaySales)): ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px; margin-bottom:12px;">
            <div style="padding:10px; border:1px solid var(--tr-border); border-radius:10px; background:var(--tr-secondary-beige);">
                <div style="font-size:11px; color:var(--tr-muted-text);">Tanggal</div>
                <div style="font-size:14px; color:var(--tr-text); font-weight:600; margin-top:2px;"><?= esc($todayDate ?? date('Y-m-d')); ?></div>
            </div>
            <div style="padding:10px; border:1px solid var(--tr-border); border-radius:10px; background:var(--tr-secondary-beige);">
                <div style="font-size:11px; color:var(--tr-muted-text);">Total Penjualan (hari ini)</div>
                <div style="font-size:16px; color:var(--tr-text); font-weight:700; margin-top:2px;">
                    Rp <?= number_format((float) ($todaySales ?? 0), 0, ',', '.'); ?>
                </div>
            </div>
            <div style="padding:10px; border:1px solid var(--tr-border); border-radius:10px; background:var(--tr-secondary-beige);">
                <div style="font-size:11px; color:var(--tr-muted-text);">Margin (hari ini)</div>
                <?php
                    $marginToday     = (float) ($todayMargin ?? 0);
                    $marginPctToday  = (float) ($todayMarginPct ?? 0);
                    $marginColorCard = $marginToday >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                ?>
                <div style="font-size:16px; font-weight:700; margin-top:2px; color:<?= $marginColorCard; ?>;">
                    Rp <?= number_format($marginToday, 0, ',', '.'); ?>
                    <span style="font-size:11px; color:var(--tr-muted-text); margin-left:4px;">
                        (<?= number_format($marginPctToday, 1, ',', '.'); ?>%)
                    </span>
                </div>
            </div>
        </div>
    <?php endif; ?>

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

    <?php if (empty($sales)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text);">Belum ada data penjualan.</p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter invoice/tanggal/status/customer:</div>
            <input type="text" id="sales-filter" placeholder="Cari penjualan..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:220px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Tanggal</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Invoice</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Customer</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Status</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Total</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">HPP</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin</th>
                <th style="text-align:center; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
            </thead>
            <tbody id="sales-table-body">
            <?php foreach ($sales as $row): ?>
                <?php
                    $total   = (float) ($row['total_amount'] ?? 0);
                    $cost    = (float) ($row['total_cost'] ?? 0);
                    $margin  = $total - $cost;
                    $marginPct = $total > 0 ? ($margin / $total * 100) : 0;
                    $marginColor = $margin >= 0 ? 'var(--tr-secondary-green)' : 'var(--tr-accent-brown)';
                    $status = $row['status'] ?? 'completed';
                    $isVoid = $status === 'void';
                    $statusLabel = $isVoid ? 'Void' : 'Completed';
                    $statusStyle = $isVoid
                        ? 'background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown);'
                        : 'background:rgba(122,154,108,0.14); color:var(--tr-primary); border:1px solid var(--tr-primary);';
                ?>
                <tr data-date="<?= esc(strtolower($row['sale_date'])); ?>" data-invoice="<?= esc(strtolower($row['invoice_no'] ?? '')); ?>" data-customer="<?= esc(strtolower($row['customer_name'] ?? '')); ?>" data-status="<?= strtolower($status); ?>">
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['sale_date']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['invoice_no'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['customer_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <span style="padding:2px 8px; border-radius:999px; font-size:11px; <?= $statusStyle; ?>">
                            <?= $statusLabel; ?>
                        </span>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($total, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?php if ($cost > 0): ?>
                            Rp <?= number_format($cost, 0, ',', '.'); ?>
                        <?php else: ?>
                            <span style="color:var(--tr-muted-text);">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <span style="color:<?= $marginColor; ?>;">
                            Rp <?= number_format($margin, 0, ',', '.'); ?>
                            <?php if ($total > 0): ?>
                                <span style="color:var(--tr-muted-text); font-size:11px;">
                                    (<?= number_format($marginPct, 1, ',', '.'); ?>%)
                                </span>
                            <?php endif; ?>
                        </span>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <div style="display:flex; justify-content:center; gap:8px;">
                            <a href="<?= site_url('transactions/sales/detail/' . $row['id']); ?>"
                               style="font-size:11px; color:#fff; text-decoration:none; background:var(--tr-primary); border:1px solid var(--tr-primary); padding:6px 10px; border-radius:999px;">
                                Detail
                            </a>
                            <?php if (! $isVoid): ?>
                                <form method="post" action="<?= site_url('transactions/sales/void/' . $row['id']); ?>" onsubmit="return confirm('Void transaksi ini? Stok akan dikembalikan.');" style="display:inline;">
                                    <?= csrf_field(); ?>
                                    <button type="submit" style="font-size:11px; color:#fff; background:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown); cursor:pointer; padding:6px 10px; border-radius:999px;">
                                        Void
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr id="sales-noresult" style="display:none;">
                <td colspan="8" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
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
                input: '#sales-filter',
                rows: document.querySelectorAll('#sales-table-body tr:not(#sales-noresult)'),
                noResult: '#sales-noresult',
                fields: ['date','invoice','customer','status'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
