<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card" style="padding:14px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <h2 style="margin:0; font-size:18px;"><?= esc($title); ?></h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                <?= esc($subtitle); ?>
            </p>
        </div>
        <a href="<?= site_url('transactions/sales/detail/' . $sale['id']); ?>"
           style="font-size:11px; padding:6px 10px; border-radius:999px; background:var(--tr-border); color:var(--tr-text); text-decoration:none;">
            Kembali
        </a>
    </div>

    <?php if (empty($items)): ?>
        <div style="font-size:12px; color:var(--tr-muted-text);">Tidak ada item untuk tiket dapur.</div>
    <?php else: ?>
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
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
