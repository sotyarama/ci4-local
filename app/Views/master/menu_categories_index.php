<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Categories - Index
 * - Minim inline style, konsisten dengan theme-temurasa.css
 * - Tetap pakai App.setupFilter (app.js)
 */
?>

<div class="card">

    <div class="page-head">
        <div>
            <h2 class="page-title">Kategori Menu</h2>
            <p class="page-subtitle">Kelompokkan menu agar rapi di laporan dan POS.</p>
        </div>

        <a href="<?= site_url('master/categories/create'); ?>" class="btn btn-primary btn-sm">
            + Tambah
        </a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('message')); ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <p class="empty-state">Belum ada kategori menu.</p>
    <?php else: ?>

        <div class="table-tools">
            <div class="table-tools__hint">Filter nama/desk:</div>
            <input
                type="text"
                id="cat-filter"
                class="table-tools__search"
                placeholder="Cari kategori...">
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th class="table__th">Nama</th>
                    <th class="table__th">Deskripsi</th>
                    <th class="table__th table__th--center">Aksi</th>
                </tr>
            </thead>

            <tbody id="cat-table-body">
                <?php foreach ($rows as $row): ?>
                    <?php
                    $name = (string) ($row['name'] ?? '');
                    $desc = (string) ($row['description'] ?? '');
                    $id   = (int) ($row['id'] ?? 0);
                    ?>
                    <tr
                        data-name="<?= esc(strtolower($name)); ?>"
                        data-desc="<?= esc(strtolower($desc)); ?>">

                        <td class="table__td"><?= esc($name); ?></td>
                        <td class="table__td muted"><?= esc($desc !== '' ? $desc : '-'); ?></td>

                        <td class="table__td table__td--center">
                            <div class="row-actions">
                                <a
                                    href="<?= site_url('master/categories/edit/' . $id); ?>"
                                    class="btn btn-primary btn-xs">
                                    Edit
                                </a>

                                <form
                                    action="<?= site_url('master/categories/delete/' . $id); ?>"
                                    method="post"
                                    class="inline"
                                    onsubmit="return confirm('Hapus kategori ini?');">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr id="cat-noresult" style="display:none;">
                    <td colspan="3" class="table__td table__td--center muted">Tidak ada hasil.</td>
                </tr>
            </tbody>
        </table>

    <?php endif; ?>

</div>

<script>
    (function() {
        function initFilter() {
            if (!window.App || !App.setupFilter) return setTimeout(initFilter, 50);

            App.setupFilter({
                input: '#cat-filter',
                rows: document.querySelectorAll('#cat-table-body tr:not(#cat-noresult)'),
                noResult: '#cat-noresult',
                fields: ['name', 'desc'],
                debounce: 200
            });
        }

        document.addEventListener('DOMContentLoaded', initFilter);
    })();
</script>

<?= $this->endSection() ?>