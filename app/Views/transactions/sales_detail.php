<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="tr-card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Detail Penjualan</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Rincian transaksi penjualan dan ringkasan HPP & margin.
            </p>
        </div>
        <div style="display:flex; gap:6px;">
            <a href="<?= site_url('transactions/sales/kitchen-ticket/' . $sale['id']); ?>" class="tr-btn tr-btn-primary tr-btn-sm">
                <span class="tr-btn-label">Ticket Dapur</span>
            </a>
            <a href="<?= site_url('transactions/sales'); ?>" class="tr-btn tr-btn-secondary tr-btn-sm">
                <span class="tr-btn-label">Kembali ke daftar</span>
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

    <?php
    $status = $sale['status'] ?? 'completed';
    $isVoid = $status === 'void';
    ?>

    <!-- HEADER -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:16px; font-size:12px; color:var(--tr-text);">
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Tanggal</div>
            <div style="font-weight:600;"><?= esc($sale['sale_date']) ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">No. Invoice</div>
            <div style="font-weight:600;"><?= esc($sale['invoice_no'] ?? '-') ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Customer</div>
            <div style="font-weight:600;"><?= esc(($sale['customer_name'] ?? '') !== '' ? $sale['customer_name'] : 'Tamu'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Status</div>
            <?php
            $statusStyle = $isVoid
                ? 'background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown);'
                : 'background:rgba(122,154,108,0.14); color:var(--tr-primary); border:1px solid var(--tr-primary);';
            ?>
            <div style="font-weight:600;">
                <span style="padding:2px 8px; border-radius:999px; font-size:11px; <?= $statusStyle; ?>">
                    <?= $isVoid ? 'Void' : 'Completed'; ?>
                </span>
            </div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Metode</div>
            <?php $method = strtolower((string) ($sale['payment_method'] ?? 'cash')); ?>
            <div style="font-weight:600;"><?= esc($method === 'qris' ? 'QRIS' : 'Cash'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Dibayar</div>
            <div style="font-weight:600;">Rp <?= number_format((float) ($sale['amount_paid'] ?? 0), 0, ',', '.'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
            <div style="color:var(--tr-muted-text); font-size:11px;">Kembalian</div>
            <div style="font-weight:600;">Rp <?= number_format((float) ($sale['change_amount'] ?? 0), 0, ',', '.'); ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:rgba(122,154,108,0.14); border:1px solid var(--tr-primary);">
            <div style="color:var(--tr-secondary-green); font-size:11px;">Total Penjualan</div>
            <div style="font-weight:700;">
                Rp <?= number_format($sale['total_amount'], 0, ',', '.'); ?>
            </div>
        </div>
    </div>

    <?php
    $totalRevenue = 0;
    $totalHpp     = 0;
    ?>

    <?php if (empty($items)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text);">Tidak ada item tercatat untuk penjualan ini.</p>
    <?php else: ?>

        <table class="tr-table tr-table-compact" style="margin-bottom:12px;">
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Kategori</th>
                    <th class="is-right">Qty</th>
                    <th class="is-right">Harga</th>
                    <th class="is-right">Subtotal</th>
                    <th class="is-right">HPP / Porsi</th>
                    <th class="is-right">Total HPP</th>
                    <th class="is-right">Margin</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $items = (isset($items) && is_iterable($items)) ? $items : [];
                ?>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="8" class="tr-table-empty">Tidak ada data</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $row): ?>
                        ...
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php foreach ($items as $row): ?>
                    <?php
                    $qty          = (float) ($row['qty'] ?? 0);
                    $subtotal     = (float) ($row['subtotal'] ?? 0);
                    $hppPerPortion = (float) ($row['hpp_snapshot'] ?? 0);
                    $totalHppItem = $hppPerPortion * $qty;
                    $marginItem   = $subtotal - $totalHppItem;

                    $totalRevenue += $subtotal;
                    $totalHpp     += $totalHppItem;
                    ?>
                    <tr>
                        <td>
                            <?= esc($row['menu_name'] ?? ''); ?>
                            <?php if (! empty($row['item_note'])): ?>
                                <div style="font-size:11px; color:var(--tr-muted-text); margin-top:4px;">
                                    Catatan: <?= esc($row['item_note']); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="is-muted">
                            <?= esc($row['category_name'] ?? '-'); ?>
                        </td>
                        <td class="is-right">
                            <?= number_format($qty, 2, ',', '.'); ?>
                        </td>
                        <td class="is-right">
                            Rp <?= number_format($row['price'], 0, ',', '.'); ?>
                        </td>
                        <td class="is-right">
                            Rp <?= number_format($subtotal, 0, ',', '.'); ?>
                        </td>
                        <td class="is-right">
                            <?php if ($hppPerPortion > 0): ?>
                                Rp <?= number_format($hppPerPortion, 0, ',', '.'); ?>
                            <?php else: ?>
                                <span class="is-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="is-right">
                            <?php if ($totalHppItem > 0): ?>
                                Rp <?= number_format($totalHppItem, 0, ',', '.'); ?>
                            <?php else: ?>
                                <span class="is-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="is-right">
                            <?php
                            $colorMargin = $marginItem >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                            ?>
                            <span style="color:<?= $colorMargin; ?>;">
                                Rp <?= number_format($marginItem, 0, ',', '.'); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        $grossMargin = $totalRevenue - $totalHpp;
        $marginPct   = $totalRevenue > 0 ? ($grossMargin / $totalRevenue * 100) : 0;
        ?>

        <div style="display:flex; justify-content:flex-end; margin-top:8px;">
            <div style="padding:10px 12px; border-radius:10px; background:var(--tr-bg); border:1px solid var(--tr-border); font-size:12px; min-width:260px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                    <span style="color:var(--tr-muted-text);">Total Revenue</span>
                    <span>Rp <?= number_format($totalRevenue, 0, ',', '.'); ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                    <span style="color:var(--tr-muted-text);">Total HPP</span>
                    <span>Rp <?= number_format($totalHpp, 0, ',', '.'); ?></span>
                </div>
                <div style="border-top:1px dashed var(--tr-border); margin:4px 0;"></div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--tr-primary-deep); font-weight:600;">Gross Margin</span>
                    <span style="font-weight:700;">
                        Rp <?= number_format($grossMargin, 0, ',', '.'); ?>
                        <span style="color:var(--tr-muted-text); font-size:11px;">
                            (<?= number_format($marginPct, 1, ',', '.'); ?>%)
                        </span>
                    </span>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <?php if (! empty($sale['notes'])): ?>
        <div style="margin-top:16px; font-size:12px; color:var(--tr-muted-text);">
            <div style="font-weight:600; margin-bottom:4px;">Catatan:</div>
            <div><?= nl2br(esc($sale['notes'])); ?></div>
        </div>
    <?php endif; ?>

    <?php if ($isVoid): ?>
        <div style="margin-top:16px; padding:10px 12px; border-radius:10px; background:var(--tr-secondary-beige); border:1px solid var(--tr-border); font-size:12px; color:var(--tr-text);">
            <div style="font-weight:700; margin-bottom:4px; color:var(--tr-accent-brown);">Transaksi sudah di-void.</div>
            <?php if (! empty($sale['void_reason'])): ?>
                <div style="color:var(--tr-text); margin-bottom:4px;">Alasan: <?= nl2br(esc($sale['void_reason'])); ?></div>
            <?php endif; ?>
            <div style="color:var(--tr-muted-text);">Tanggal void: <?= esc($sale['voided_at'] ?? '-'); ?></div>
        </div>
    <?php else: ?>
        <div style="margin-top:16px; padding:10px 12px; border-radius:10px; background:var(--tr-border); border:1px solid var(--tr-muted-text); font-size:12px; color:var(--tr-text);">
            <div style="font-weight:700; margin-bottom:6px; color:var(--tr-secondary-beige);">Void / Batalkan Transaksi</div>
            <form method="post" action="<?= site_url('transactions/sales/void/' . $sale['id']); ?>" onsubmit="return confirm('Yakin void transaksi ini? Stok akan dikembalikan.');">
                <?= csrf_field(); ?>
                <label style="display:block; margin-bottom:4px; color:var(--tr-muted-text);">Alasan (opsional)</label>
                <textarea name="void_reason" rows="2" class="tr-control" style="margin-bottom:8px;"></textarea>
                <button type="submit" class="tr-btn tr-btn-danger tr-btn-sm">
                    <span class="tr-btn-label">Void Transaksi</span>
                </button>
            </form>
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>