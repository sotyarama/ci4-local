<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="tr-card" style="padding:14px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <h2 style="margin:0; font-size:18px;"><?= esc($title); ?></h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                <?= esc($subtitle); ?>
            </p>
        </div>
        <div style="display:flex; gap:6px;">
            <?php if (($sale['kitchen_status'] ?? 'open') !== 'done'): ?>
                <form method="post" action="<?= site_url('transactions/kitchen/done/' . $sale['id']); ?>" onsubmit="return confirm('Tandai pesanan ini selesai?');">
                    <?= csrf_field(); ?>
                    <button type="submit" class="tr-btn tr-btn-primary tr-btn-sm">
                        <span class="tr-btn-label">Done</span>
                    </button>
                </form>
            <?php endif; ?>
            <a href="<?= site_url('transactions/kitchen'); ?>" class="tr-btn tr-btn-secondary tr-btn-sm">
                <span class="tr-btn-label">Kitchen Queue</span>
            </a>
            <a href="<?= site_url('transactions/sales/detail/' . $sale['id']); ?>" class="tr-btn tr-btn-secondary tr-btn-sm">
                <span class="tr-btn-label">Detail</span>
            </a>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <p class="tr-help" style="margin:0;">Tidak ada item untuk tiket dapur.</p>
    <?php else: ?>
        <?php
        $method = strtolower((string) ($sale['payment_method'] ?? 'cash'));
        $methodLabel = $method === 'qris' ? 'QRIS' : 'Cash';
        $paid = (float) ($sale['amount_paid'] ?? 0);
        $change = (float) ($sale['change_amount'] ?? 0);
        ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:8px; margin-bottom:12px; font-size:12px;">
            <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
                <div style="color:var(--tr-muted-text); font-size:11px;">Customer</div>
                <div style="font-weight:600;"><?= esc(($sale['customer_name'] ?? '') !== '' ? $sale['customer_name'] : 'Tamu'); ?></div>
            </div>
            <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
                <div style="color:var(--tr-muted-text); font-size:11px;">Metode</div>
                <div style="font-weight:600;"><?= esc($methodLabel); ?></div>
            </div>
            <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
                <div style="color:var(--tr-muted-text); font-size:11px;">Dibayar</div>
                <div style="font-weight:600;">Rp <?= number_format($paid, 0, ',', '.'); ?></div>
            </div>
            <div style="padding:8px 10px; border-radius:8px; background:var(--tr-bg); border:1px solid var(--tr-border);">
                <div style="color:var(--tr-muted-text); font-size:11px;">Kembalian</div>
                <div style="font-weight:600;">Rp <?= number_format($change, 0, ',', '.'); ?></div>
            </div>
        </div>
        <div style="display:flex; flex-direction:column; gap:12px;">
            <?php foreach ($items as $item): ?>
                <?php
                $itemId = (int) ($item['id'] ?? 0);
                $qty = (float) ($item['qty'] ?? 0);
                $groups = $optionsByItem[$itemId] ?? [];
                ?>
                <div style="border:1px solid var(--tr-border); border-radius:10px; padding:10px; background:#fff;">
                    <div style="font-weight:700; font-size:14px; color:var(--tr-text);">
                        <?= esc($item['menu_name'] ?? ''); ?> x<?= number_format($qty, 2, ',', '.'); ?>
                    </div>
                    <?php if (! empty($groups)): ?>
                        <div style="margin-top:6px; font-size:12px; color:var(--tr-muted-text); display:flex; flex-direction:column; gap:4px;">
                            <?php foreach ($groups as $groupName => $opts): ?>
                                <div>
                                    <span style="font-weight:600;"><?= esc(strtoupper($groupName)); ?>:</span>
                                    <?= esc(implode(', ', $opts)); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($item['item_note'])): ?>
                        <div style="margin-top:6px; font-size:12px; color:var(--tr-muted-text);">
                            Catatan: <?= esc($item['item_note']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>