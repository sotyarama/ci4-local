<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h2 style="margin:0; font-size:18px;">Master Bahan Baku</h2>
        <a href="<?= site_url('master/raw-materials/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; text-decoration:none;">
            + Tambah Bahan
        </a>
    </div>

    <p style="margin:0 0 16px; font-size:13px; color:var(--tr-muted-text);">
        Daftar bahan baku untuk resep dan pengelolaan stok.
    </p>

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

    <?php if (empty($materials)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:0;">
            Belum ada data bahan baku. Silakan tambahkan data baru.
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:8px; border-bottom:1px solid var(--tr-border);">Nama Bahan</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid var(--tr-border);">Satuan</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid var(--tr-border);">Stok Saat Ini</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid var(--tr-border);">Min Stok</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid var(--tr-border);">Last Cost</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid var(--tr-border);">Avg Cost</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid var(--tr-border);">Status</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($materials as $m): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($m['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($m['unit_short'] ?? $m['unit_name'] ?? ''); ?>
                    </td>
                    <?php
                        $currentStock = (float) ($m['current_stock'] ?? 0);
                        $minStock     = (float) ($m['min_stock'] ?? 0);
                        $isLow        = $minStock > 0 && $currentStock < $minStock;
                    ?>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format($currentStock, 3, ',', '.'); ?>
                        <?php if ($isLow): ?>
                            <span style="margin-left:6px; padding:2px 8px; border-radius:999px; background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown); font-size:10px; font-weight:700;">
                                Low
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format($minStock, 3, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format((float) $m['cost_last'], 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format((float) $m['cost_avg'], 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <?php if (!empty($m['is_active'])): ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:rgba(122,154,108,0.14); color:var(--tr-secondary-green); border:1px solid rgba(122,154,108,0.14);">
                                Aktif
                            </span>
                        <?php else: ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown);">
                                Nonaktif
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <a href="<?= site_url('master/raw-materials/edit/' . $m['id']); ?>"
                           style="font-size:11px; margin-right:6px; color:#fff; text-decoration:none; background:var(--tr-primary); border:1px solid var(--tr-primary); padding:6px 10px; border-radius:999px;">
                            Edit
                        </a>
                        <form action="<?= site_url('master/raw-materials/delete/' . $m['id']); ?>"
                              method="post"
                              style="display:inline;"
                              onsubmit="return confirm('Yakin ingin menghapus bahan ini?');">
                            <?= csrf_field(); ?>
                            <button type="submit"
                                    style="font-size:11px; border:1px solid var(--tr-accent-brown); background:var(--tr-accent-brown); color:#fff; cursor:pointer; padding:6px 10px; border-radius:999px;">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="margin-top:12px; font-size:11px; color:var(--tr-muted-text);">
        Data ini akan digunakan pada modul Resep, Pembelian, dan Stock Movement.
    </div>
</div>

<?= $this->endSection() ?>
