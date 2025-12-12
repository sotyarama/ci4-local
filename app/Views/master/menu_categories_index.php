<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Kategori Menu</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Kelompokkan menu agar rapi di laporan dan POS.
            </p>
        </div>
        <a href="<?= site_url('master/categories/create'); ?>"
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
            Belum ada kategori menu.
        </p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter nama/desk:</div>
            <input type="text" id="cat-filter" placeholder="Cari kategori..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:200px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Nama</th>
                <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Deskripsi</th>
                <th style="text-align:center; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
            </thead>
            <tbody id="cat-table-body">
            <?php foreach ($rows as $row): ?>
                <tr data-name="<?= esc(strtolower($row['name'])); ?>" data-desc="<?= esc(strtolower($row['description'] ?? '')); ?>">
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); color:var(--tr-muted-text);">
                        <?= esc($row['description'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <a href="<?= site_url('master/categories/edit/' . $row['id']); ?>"
                               style="font-size:11px; color:#fff; text-decoration:none; border:1px solid var(--tr-primary); padding:6px 10px; border-radius:999px; background:var(--tr-primary);">
                                Edit
                            </a>
                            <form action="<?= site_url('master/categories/delete/' . $row['id']); ?>" method="post" onsubmit="return confirm('Hapus kategori ini?');">
                                <?= csrf_field(); ?>
                                <button type="submit" style="font-size:11px; color:#fff; background:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown); padding:6px 10px; border-radius:999px; cursor:pointer;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr id="cat-noresult" style="display:none;">
                <td colspan="3" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    (function() {
        if (!window.App || !App.setupFilter) return;
        App.setupFilter({
            input: '#cat-filter',
            rows: document.querySelectorAll('#cat-table-body tr:not(#cat-noresult)'),
            noResult: '#cat-noresult',
            fields: ['name','desc'],
            debounce: 200
        });
    })();
</script>

<?= $this->endSection() ?>
