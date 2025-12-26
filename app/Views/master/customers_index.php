<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Customers - Index
 * - Konsisten dengan theme-temurasa.css
 * - Filter sederhana via App.setupFilter
 */
?>

<div class="card">

    <div class="page-head">
        <div>
            <h2 class="page-title" style="margin:0 0 4px;">Master Customer</h2>
            <p class="page-subtitle" style="margin:0;">Daftar customer untuk transaksi penjualan.</p>
        </div>

        <a href="<?= site_url('master/customers/create'); ?>" class="btn btn-primary btn-sm">
            + Tambah Customer
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

    <?php if (empty($customers)): ?>
        <p class="empty-state">Belum ada data customer. Silakan tambahkan data baru.</p>
    <?php else: ?>

        <div class="table-tools">
            <div class="table-tools__hint">Filter nama/telepon/email/status:</div>
            <input
                type="text"
                id="customers-filter"
                class="table-tools__search"
                placeholder="Cari customer...">
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th class="table__th">Nama</th>
                    <th class="table__th">Email</th>
                    <th class="table__th">No. Telepon</th>
                    <th class="table__th table__th--center">Status</th>
                    <th class="table__th table__th--center">Aksi</th>
                </tr>
            </thead>
            <tbody id="customers-table-body">
                <?php foreach ($customers as $c): ?>
                    <?php
                    $id       = (int) ($c['id'] ?? 0);
                    $name     = (string) ($c['name'] ?? '');
                    $phone    = (string) ($c['phone'] ?? '');
                    $email    = (string) ($c['email'] ?? '');
                    $isActive = ! empty($c['is_active']);
                    $status   = $isActive ? 'aktif' : 'nonaktif';
                    ?>
                    <tr
                        data-name="<?= esc(strtolower($name)); ?>"
                        data-phone="<?= esc(strtolower($phone)); ?>"
                        data-email="<?= esc(strtolower($email)); ?>"
                        data-status="<?= esc($status); ?>">

                        <td class="table__td"><?= esc($name !== '' ? $name : '-'); ?></td>
                        <td class="table__td"><?= esc($email !== '' ? $email : '-'); ?></td>
                        <td class="table__td"><?= esc($phone !== '' ? $phone : '-'); ?></td>
                        <td class="table__td table__td--center">
                            <?php if ($isActive): ?>
                                <span class="badge badge--active">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge--inactive">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="table__td table__td--center">
                            <div class="row-actions">
                                <a href="<?= site_url('master/customers/edit/' . $id); ?>" class="btn btn-primary btn-sm">
                                    Edit
                                </a>
                                <form
                                    action="<?= site_url('master/customers/delete/' . $id); ?>"
                                    method="post"
                                    class="inline"
                                    onsubmit="return confirm('Yakin ingin menghapus customer ini?');">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr id="customers-noresult" style="display:none;">
                    <td colspan="5" class="table__td table__td--center muted">Tidak ada hasil.</td>
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
                input: '#customers-filter',
                rows: document.querySelectorAll('#customers-table-body tr:not(#customers-noresult)'),
                noResult: '#customers-noresult',
                fields: ['name', 'phone', 'email', 'status'],
                debounce: 200
            });
        }

        document.addEventListener('DOMContentLoaded', initFilter);
    })();
</script>

<?= $this->endSection() ?>
