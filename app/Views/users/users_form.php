<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Users - Form
 */
$errors     = session('errors') ?? [];
$isEdit     = ! empty($user);
$role       = strtolower((string) (session('role') ?? session('role_name') ?? ''));
$canManage  = ($role === 'owner');
$roleLabel  = (string) ($roleLabel ?? '-');
?>

<div class="card">
    <h2 class="page-title"><?= esc($title ?? 'Form User'); ?></h2>
    <p class="page-subtitle"><?= esc($subtitle ?? ''); ?></p>

    <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="alert-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= esc($formAction ?? '#'); ?>" method="post" class="form">
        <?= csrf_field(); ?>

        <div class="form-grid">
            <div class="form-field">
                <label class="form-label">Username</label>
                <input
                    class="form-input"
                    type="text"
                    name="username"
                    value="<?= esc(old('username', $user['username'] ?? '')); ?>"
                    required>
            </div>

            <div class="form-field">
                <label class="form-label">Nama Lengkap</label>
                <input
                    class="form-input"
                    type="text"
                    name="full_name"
                    value="<?= esc(old('full_name', $user['full_name'] ?? '')); ?>"
                    required>
            </div>

            <div class="form-field">
                <label class="form-label">Email</label>
                <input
                    class="form-input"
                    type="email"
                    name="email"
                    value="<?= esc(old('email', $user['email'] ?? '')); ?>"
                    placeholder="nama@email.com"
                    required>
            </div>

            <?php if (! $isEdit): ?>
                <div class="form-field">
                    <label class="form-label">Role</label>
                    <select class="form-input" name="role_id" required>
                        <option value="">-- pilih role --</option>

                        <?php foreach (($roles ?? []) as $r): ?>
                            <?php
                            $rid   = (string) ($r['id'] ?? '');
                            $rname = (string) ($r['name'] ?? '');
                            $rdesc = (string) ($r['description'] ?? '');
                            $selected = ((string) old('role_id', '') === $rid);
                            $label = $rname !== '' ? ucfirst($rname) : '-';
                            if ($rdesc !== '') {
                                $label .= ' - ' . $rdesc;
                            }
                            ?>
                            <option value="<?= esc($rid); ?>" <?= $selected ? 'selected' : ''; ?>>
                                <?= esc($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <div class="form-field">
                    <label class="form-label">Role</label>
                    <input
                        class="form-input"
                        type="text"
                        value="<?= esc($roleLabel); ?>"
                        readonly
                        aria-readonly="true">
                </div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <?php if ($canManage): ?>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= site_url('users'); ?>" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <a href="<?= site_url('users'); ?>" class="btn btn-secondary">Kembali</a>
            <?php endif; ?>
        </div>

        <div class="form-note">
            Password dibuat melalui menu Lupa Password dengan akses ke email terdaftar.
        </div>
    </form>
</div>

<?= $this->endSection() ?>
