<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Detail Penjualan</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Rincian transaksi penjualan dan ringkasan HPP & margin.
            </p>
        </div>
                <div style="display:flex; gap:6px;">
            <a href="<?= site_url('transactions/sales/kitchen-ticket/' . $sale['id']); ?>"
               style="font-size:11px; padding:6px 10px; border-radius:999px; background:rgba(122,154,108,0.14); color:var(--tr-primary); text-decoration:none; border:1px solid var(--tr-primary);">
                Ticket Dapur
            </a>
            <a href="<?= site_url('transactions/sales'); ?>"
               style="font-size:11px; padding:6px 10px; border-radius:999px; background:var(--tr-border); color:var(--tr-text); text-decoration:none;">
                Kembali ke daftar
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

        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:12px;">
            <thead>
            <tr>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:left;">Menu</th>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:left;">Kategori</th>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">Qty</th>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">Harga</th>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">Subtotal</th>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">HPP / Porsi</th>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">Total HPP</th>
                <th style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">Margin</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $row): ?>
                <?php
                    $qty          = (float) ($row['qty'] ?? 0);
                    $subtotal     = (float) ($row['subtotal'] ?? 0);
                    $hppPerPortion= (float) ($row['hpp_snapshot'] ?? 0);
                    $totalHppItem = $hppPerPortion * $qty;
                    $marginItem   = $subtotal - $totalHppItem;

                    $totalRevenue += $subtotal;
                    $totalHpp     += $totalHppItem;
                ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['menu_name'] ?? ''); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); color:var(--tr-muted-text);">
                        <?= esc($row['category_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format($qty, 2, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($row['price'], 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format($subtotal, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?php if ($hppPerPortion > 0): ?>
                            Rp <?= number_format($hppPerPortion, 0, ',', '.'); ?>
                        <?php else: ?>
                            <span style="color:var(--tr-muted-text);">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?php if ($totalHppItem > 0): ?>
                            Rp <?= number_format($totalHppItem, 0, ',', '.'); ?>
                        <?php else: ?>
                            <span style="color:var(--tr-muted-text);">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
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
                <textarea name="void_reason" rows="2" style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text); margin-bottom:8px;"></textarea>
                <button type="submit" style="padding:8px 12px; border-radius:8px; border:1px solid var(--tr-accent-brown); background:var(--tr-secondary-beige); color:var(--tr-accent-brown); cursor:pointer; font-size:12px;">
                    Void Transaksi
                </button>
            </form>
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>
