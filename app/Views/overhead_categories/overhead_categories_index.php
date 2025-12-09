<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Kategori Overhead</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                Kelola kategori biaya overhead (non gaji).
            </p>
        </div>
        <a href="<?= site_url('overhead-categories/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:#22c55e; color:#022c22; text-decoration:none;">
            + Tambah
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:#022c22; border:1px solid #22c55e; color:#bbf7d0; font-size:12px;">
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:#9ca3af; margin:8px 0 0;">
            Belum ada kategori overhead.
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Nama</th>
                <th style="text-align:center;padding:6px 8px; border-bottom:1px solid #111827;">Aktif</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:center;">
                        <?php if ((int) ($row['is_active'] ?? 0) === 1): ?>
                            <span style="padding:2px 8px; border-radius:999px; background:#022c22; color:#22c55e; border:1px solid #22c55e;">Aktif</span>
                        <?php else: ?>
                            <span style="padding:2px 8px; border-radius:999px; background:#3f1f1f; color:#fecaca; border:1px solid #b91c1c;">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
