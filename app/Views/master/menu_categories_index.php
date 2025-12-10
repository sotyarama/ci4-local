<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Kategori Menu</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                Kelompokkan menu agar rapi di laporan dan POS.
            </p>
        </div>
        <a href="<?= site_url('master/categories/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:#22c55e; color:#022c22; text-decoration:none;">
            + Tambah
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:#022c22; border:1px solid #22c55e; color:#bbf7d0; font-size:12px;">
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:#3f1f1f; border:1px solid #b91c1c; color:#fecaca; font-size:12px;">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:#9ca3af; margin:8px 0 0;">
            Belum ada kategori menu.
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid #111827;">Nama</th>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid #111827;">Deskripsi</th>
                <th style="text-align:center; padding:6px 8px; border-bottom:1px solid #111827;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; color:#9ca3af;">
                        <?= esc($row['description'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:center;">
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <a href="<?= site_url('master/categories/edit/' . $row['id']); ?>"
                               style="font-size:11px; color:#93c5fd; text-decoration:none; border:1px solid #1f2937; padding:4px 8px; border-radius:8px; background:#0b1220;">
                                Edit
                            </a>
                            <form action="<?= site_url('master/categories/delete/' . $row['id']); ?>" method="post" onsubmit="return confirm('Hapus kategori ini?');">
                                <?= csrf_field(); ?>
                                <button type="submit" style="font-size:11px; color:#fca5a5; background:none; border:1px solid #1f2937; padding:4px 8px; border-radius:8px; cursor:pointer;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
