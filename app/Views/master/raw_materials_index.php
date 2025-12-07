<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h2 style="margin:0; font-size:18px;">Master Bahan Baku</h2>
        <a href="<?= site_url('master/raw-materials/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:#22c55e; color:#022c22; text-decoration:none;">
            + Tambah Bahan
        </a>
    </div>

    <p style="margin:0 0 16px; font-size:13px; color:#9ca3af;">
        Daftar bahan baku untuk resep dan pengelolaan stok.
    </p>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="background:#022c22; border-radius:8px; padding:8px 10px; border:1px solid #16a34a; font-size:12px; color:#bbf7d0; margin-bottom:12px;">
            <?= esc(session()->getFlashdata('message')); ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:#7f1d1d; border-radius:8px; padding:8px 10px; border:1px solid #b91c1c; font-size:12px; color:#fee2e2; margin-bottom:12px;">
            <?= esc(session()->getFlashdata('error')); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($materials)): ?>
        <p style="font-size:12px; color:#9ca3af; margin:0;">
            Belum ada data bahan baku. Silakan tambahkan data baru.
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #111827;">Nama Bahan</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #111827;">Satuan</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid #111827;">Stok Saat Ini</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid #111827;">Min Stok</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid #111827;">Last Cost</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid #111827;">Avg Cost</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid #111827;">Status</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid #111827;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($materials as $m): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827;">
                        <?= esc($m['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827;">
                        <?= esc($m['unit_short'] ?? $m['unit_name'] ?? ''); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">
                        <?= number_format((float) $m['current_stock'], 3, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">
                        <?= number_format((float) $m['min_stock'], 3, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">
                        Rp <?= number_format((float) $m['cost_last'], 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">
                        Rp <?= number_format((float) $m['cost_avg'], 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:center;">
                        <?php if (!empty($m['is_active'])): ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:#022c22; color:#6ee7b7; border:1px solid #064e3b;">
                                Aktif
                            </span>
                        <?php else: ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:#3f1f1f; color:#fecaca; border:1px solid #991b1b;">
                                Nonaktif
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:center;">
                        <a href="<?= site_url('master/raw-materials/edit/' . $m['id']); ?>"
                           style="font-size:11px; margin-right:6px; color:#60a5fa; text-decoration:none;">
                            Edit
                        </a>
                        <form action="<?= site_url('master/raw-materials/delete/' . $m['id']); ?>"
                              method="post"
                              style="display:inline;"
                              onsubmit="return confirm('Yakin ingin menghapus bahan ini?');">
                            <?= csrf_field(); ?>
                            <button type="submit"
                                    style="font-size:11px; border:none; background:none; color:#fca5a5; cursor:pointer;">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="margin-top:12px; font-size:11px; color:#6b7280;">
        Data ini akan digunakan pada modul Resep, Pembelian, dan Stock Movement.
    </div>
</div>

<?= $this->endSection() ?>
