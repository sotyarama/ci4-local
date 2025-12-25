<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Users - Index
 */
$role          = strtolower((string) (session('role') ?? session('role_name') ?? ''));
$canManage     = ($role === 'owner');
$currentUserId = (int) (session('user_id') ?? 0);
?>

<div class="card">

    <div class="page-head">
        <div>
            <h2 class="page-title" style="margin:0 0 4px;">User Management</h2>
            <p class="page-subtitle" style="margin:0;">Kelola akun pengguna & role akses.</p>
        </div>

        <?php if ($canManage): ?>
            <a href="<?= site_url('users/create'); ?>" class="btn btn-primary btn-sm">
                + Tambah User
            </a>
        <?php endif; ?>
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

    <?php if (empty($users)): ?>
        <p class="empty-state">Belum ada data user. Silakan tambahkan data baru.</p>
    <?php else: ?>

        <div class="table-tools">
            <div class="table-tools__hint">Filter nama/username/email/role/status:</div>
            <input
                type="text"
                id="users-filter"
                class="table-tools__search"
                placeholder="Cari user...">
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th class="table__th">Username</th>
                    <th class="table__th">Nama Lengkap</th>
                    <th class="table__th">Email</th>
                    <th class="table__th">Role</th>
                    <th class="table__th table__th--center">Status</th>
                    <th class="table__th table__th--center">Aksi</th>
                </tr>
            </thead>

            <tbody id="users-table-body">
                <?php foreach ($users as $u): ?>
                    <?php
                    $id        = (int) ($u['id'] ?? 0);
                    $username  = (string) ($u['username'] ?? '');
                    $fullName  = (string) ($u['full_name'] ?? '');
                    $email     = (string) ($u['email'] ?? '');
                    $roleName  = (string) ($u['role_name'] ?? '');
                    $isActive  = ! empty($u['active']);
                    $status    = $isActive ? 'aktif' : 'nonaktif';
                    $roleLabel = $roleName !== '' ? ucfirst($roleName) : '-';
                    ?>
                    <tr
                        data-username="<?= esc(strtolower($username)); ?>"
                        data-name="<?= esc(strtolower($fullName)); ?>"
                        data-email="<?= esc(strtolower($email)); ?>"
                        data-role="<?= esc(strtolower($roleLabel)); ?>"
                        data-status="<?= esc($status); ?>">

                        <td class="table__td"><?= esc($username !== '' ? $username : '-'); ?></td>
                        <td class="table__td"><?= esc($fullName !== '' ? $fullName : '-'); ?></td>
                        <td class="table__td"><?= esc($email !== '' ? $email : '-'); ?></td>
                        <td class="table__td"><?= esc($roleLabel); ?></td>

                        <td class="table__td table__td--center">
                            <?php if ($isActive): ?>
                                <span class="badge badge--active">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge--inactive">Nonaktif</span>
                            <?php endif; ?>
                        </td>

                        <td class="table__td table__td--center">
                            <?php if ($canManage): ?>
                                <div class="row-actions">
                                    <a href="<?= site_url('users/edit/' . $id); ?>" class="btn btn-primary btn-sm">
                                        Edit
                                    </a>

                                    <?php if ($currentUserId !== $id): ?>
                                        <form
                                            action="<?= site_url('users/delete/' . $id); ?>"
                                            method="post"
                                            class="inline"
                                            onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="muted" style="font-size:12px;">Akun aktif</span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="muted">Read-only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr id="users-noresult" style="display:none;">
                    <td colspan="6" class="table__td table__td--center muted">Tidak ada hasil.</td>
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
                input: '#users-filter',
                rows: document.querySelectorAll('#users-table-body tr:not(#users-noresult)'),
                noResult: '#users-noresult',
                fields: ['username', 'name', 'email', 'role', 'status'],
                debounce: 200
            });
        }

        document.addEventListener('DOMContentLoaded', initFilter);
    })();
</script>

<?= $this->endSection() ?>
