<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Detail Penjualan</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                Rincian transaksi penjualan dan ringkasan HPP & margin.
            </p>
        </div>
        <a href="<?= site_url('transactions/sales'); ?>"
           style="font-size:11px; padding:6px 10px; border-radius:999px; background:#111827; color:#e5e7eb; text-decoration:none;">
            ← Kembali ke daftar
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:#022c22; border:1px solid #16a34a; color:#bbf7d0; font-size:12px;">
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:#3f1f1f; border:1px solid #b91c1c; color:#fecaca; font-size:12px;">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <?php
        $status = $sale['status'] ?? 'completed';
        $isVoid = $status === 'void';
    ?>

    <!-- HEADER -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:16px; font-size:12px; color:#e5e7eb;">
        <div style="padding:8px 10px; border-radius:8px; background:#020617; border:1px solid #1f2937;">
            <div style="color:#9ca3af; font-size:11px;">Tanggal</div>
            <div style="font-weight:600;"><?= esc($sale['sale_date']) ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:#020617; border:1px solid #1f2937;">
            <div style="color:#9ca3af; font-size:11px;">No. Invoice</div>
            <div style="font-weight:600;"><?= esc($sale['invoice_no'] ?? '-') ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:#020617; border:1px solid #1f2937;">
            <div style="color:#9ca3af; font-size:11px;">Customer</div>
            <div style="font-weight:600;"><?= esc($sale['customer_name'] ?? '-') ?></div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:#020617; border:1px solid #1f2937;">
            <div style="color:#9ca3af; font-size:11px;">Status</div>
            <?php
                $statusStyle = $isVoid
                    ? 'background:#3f1f1f; color:#fecaca; border:1px solid #b91c1c;'
                    : 'background:#022c22; color:#22c55e; border:1px solid #16a34a;';
            ?>
            <div style="font-weight:600;">
                <span style="padding:2px 8px; border-radius:999px; font-size:11px; <?= $statusStyle; ?>">
                    <?= $isVoid ? 'Void' : 'Completed'; ?>
                </span>
            </div>
        </div>
        <div style="padding:8px 10px; border-radius:8px; background:#022c22; border:1px solid #047857;">
            <div style="color:#a7f3d0; font-size:11px;">Total Penjualan</div>
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
        <p style="font-size:12px; color:#9ca3af;">Tidak ada item tercatat untuk penjualan ini.</p>
    <?php else: ?>

        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:12px;">
            <thead>
            <tr>
                <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:left;">Menu</th>
                <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:left;">Kategori</th>
                <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">Qty</th>
                <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">Harga</th>
                <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">Subtotal</th>
                <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">HPP / Porsi</th>
                <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">Total HPP</th>
                <th style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">Margin</th>
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
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['menu_name'] ?? ''); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; color:#9ca3af;">
                        <?= esc($row['category_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        <?= number_format($qty, 2, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        Rp <?= number_format($row['price'], 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        Rp <?= number_format($subtotal, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        <?php if ($hppPerPortion > 0): ?>
                            Rp <?= number_format($hppPerPortion, 0, ',', '.'); ?>
                        <?php else: ?>
                            <span style="color:#9ca3af;">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        <?php if ($totalHppItem > 0): ?>
                            Rp <?= number_format($totalHppItem, 0, ',', '.'); ?>
                        <?php else: ?>
                            <span style="color:#9ca3af;">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right;">
                        <?php
                            $colorMargin = $marginItem >= 0 ? '#bbf7d0' : '#fecaca';
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
            <div style="padding:10px 12px; border-radius:10px; background:#020617; border:1px solid #1f2937; font-size:12px; min-width:260px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                    <span style="color:#9ca3af;">Total Revenue</span>
                    <span>Rp <?= number_format($totalRevenue, 0, ',', '.'); ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                    <span style="color:#9ca3af;">Total HPP</span>
                    <span>Rp <?= number_format($totalHpp, 0, ',', '.'); ?></span>
                </div>
                <div style="border-top:1px dashed #374151; margin:4px 0;"></div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:#a5b4fc; font-weight:600;">Gross Margin</span>
                    <span style="font-weight:700;">
                        Rp <?= number_format($grossMargin, 0, ',', '.'); ?>
                        <span style="color:#9ca3af; font-size:11px;">
                            (<?= number_format($marginPct, 1, ',', '.'); ?>%)
                        </span>
                    </span>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <?php if (! empty($sale['notes'])): ?>
        <div style="margin-top:16px; font-size:12px; color:#9ca3af;">
            <div style="font-weight:600; margin-bottom:4px;">Catatan:</div>
            <div><?= nl2br(esc($sale['notes'])); ?></div>
        </div>
    <?php endif; ?>

    <?php if ($isVoid): ?>
        <div style="margin-top:16px; padding:10px 12px; border-radius:10px; background:#0b1220; border:1px solid #1f2937; font-size:12px; color:#fca5a5;">
            <div style="font-weight:700; margin-bottom:4px;">Transaksi sudah di-void.</div>
            <?php if (! empty($sale['void_reason'])): ?>
                <div style="color:#e5e7eb; margin-bottom:4px;">Alasan: <?= nl2br(esc($sale['void_reason'])); ?></div>
            <?php endif; ?>
            <div style="color:#9ca3af;">Tanggal void: <?= esc($sale['voided_at'] ?? '-'); ?></div>
        </div>
    <?php else: ?>
        <div style="margin-top:16px; padding:10px 12px; border-radius:10px; background:#1f2937; border:1px solid #4b5563; font-size:12px; color:#e5e7eb;">
            <div style="font-weight:700; margin-bottom:6px; color:#fca5a5;">Void / Batalkan Transaksi</div>
            <form method="post" action="<?= site_url('transactions/sales/void/' . $sale['id']); ?>" onsubmit="return confirm('Yakin void transaksi ini? Stok akan dikembalikan.');">
                <?= csrf_field(); ?>
                <label style="display:block; margin-bottom:4px; color:#9ca3af;">Alasan (opsional)</label>
                <textarea name="void_reason" rows="2" style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb; margin-bottom:8px;"></textarea>
                <button type="submit" style="padding:8px 12px; border-radius:8px; border:1px solid #b91c1c; background:#3f1f1f; color:#fecaca; cursor:pointer; font-size:12px;">
                    Void Transaksi
                </button>
            </form>
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>
