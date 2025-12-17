<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Units - Index
 * - Konsisten dengan theme-temurasa.css
 * - Tetap pakai App.setupFilter (app.js)
 */
?>

<div class="card">

    <div class="page-head">
        <div>
            <h2 class="page-title" style="margin:0 0 4px;">Master Satuan</h2>
            <p class="page-subtitle" style="margin:0;">Daftar satuan untuk bahan baku & resep.</p>
        </div>

        <a href="<?= site_url('master/units/create'); ?>" class="btn btn-primary btn-sm">
            + Tambah Satuan
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

    <?php if (empty($units)): ?>
        <p class="empty-state">Belum ada data satuan. Silakan tambahkan data baru.</p>
    <?php else: ?>

        <div class="table-tools">
            <div class="table-tools__hint">Filter nama/singkatan/status:</div>
            <input
                type="text"
                id="units-filter"
                class="table-tools__search"
                placeholder="Cari satuan...">
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th class="table__th">Nama</th>
                    <th class="table__th">Singkatan</th>
                    <th class="table__th table__th--center">Status</th>
                    <th class="table__th table__th--center">Aksi</th>
                </tr>
            </thead>

            <tbody id="units-table-body">
                <?php foreach ($units as $u): ?>
                    <?php
                    $id       = (int) ($u['id'] ?? 0);
                    $name     = (string) ($u['name'] ?? '');
                    $short    = (string) ($u['short_name'] ?? '');
                    $isActive = ! empty($u['is_active']);
                    $status   = $isActive ? 'aktif' : 'nonaktif';
                    ?>
                    <tr
                        data-name="<?= esc(strtolower($name)); ?>"
                        data-short="<?= esc(strtolower($short)); ?>"
                        data-status="<?= esc($status); ?>">

                        <td class="table__td"><?= esc($name !== '' ? $name : '-'); ?></td>
                        <td class="table__td"><?= esc($short); ?></td>

                        <td class="table__td table__td--center">
                            <?php if ($isActive): ?>
                                <span class="badge badge--active">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge--inactive">Nonaktif</span>
                            <?php endif; ?>
                        </td>

                        <td class="table__td table__td--center">
                            <div class="row-actions">
                                <a href="<?= site_url('master/units/edit/' . $id); ?>" class="btn btn-primary btn-sm">
                                    Edit
                                </a>

                                <form
                                    action="<?= site_url('master/units/delete/' . $id); ?>"
                                    method="post"
                                    class="inline"
                                    onsubmit="return confirm('Yakin ingin menghapus satuan ini?');">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr id="units-noresult" style="display:none;">
                    <td colspan="4" class="table__td table__td--center muted">Tidak ada hasil.</td>
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
                input: '#units-filter',
                rows: document.querySelectorAll('#units-table-body tr:not(#units-noresult)'),
                noResult: '#units-noresult',
                fields: ['name', 'short', 'status'],
                debounce: 200
            });
        }

        document.addEventListener('DOMContentLoaded', initFilter);
    })();
</script>

<?= $this->endSection() ?>