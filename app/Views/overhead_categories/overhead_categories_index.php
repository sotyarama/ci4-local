<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Kategori Overhead</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Kelola kategori biaya overhead (non gaji).
            </p>
        </div>
        <a href="<?= site_url('overhead-categories/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; text-decoration:none;">
            + Tambah
        </a>
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

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">
            Belum ada kategori overhead.
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Nama</th>
                <th style="text-align:center;padding:6px 8px; border-bottom:1px solid var(--tr-border);">Aktif</th>
                <th style="text-align:center;padding:6px 8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <?php if ((int) ($row['is_active'] ?? 0) === 1): ?>
                            <span style="padding:2px 8px; border-radius:999px; background:rgba(122,154,108,0.14); color:var(--tr-primary); border:1px solid var(--tr-primary);">Aktif</span>
                        <?php else: ?>
                            <span style="padding:2px 8px; border-radius:999px; background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown);">Nonaktif</span>
                        <?php endif; ?>
                </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <a href="<?= site_url('overhead-categories/edit/' . $row['id']); ?>"
                           style="display:inline-block; font-size:11px; padding:6px 12px; border-radius:999px; background:var(--tr-primary); color:#fff; text-decoration:none;">
                            Edit
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
