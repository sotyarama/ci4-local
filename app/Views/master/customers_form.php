<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Customers - Form
 */
$errors = session('errors') ?? [];
?>

<div class="card">
    <h2 class="page-title"><?= esc($title ?? 'Form Customer'); ?></h2>
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
                <label class="form-label">Nama Customer</label>
                <input
                    class="form-input"
                    type="text"
                    name="name"
                    value="<?= esc(old('name', $customer['name'] ?? '')); ?>"
                    placeholder="mis: Budi, Ibu Rina"
                    required>
            </div>

            <div class="form-field">
                <label class="form-label">No. Telepon (opsional)</label>
                <input
                    class="form-input"
                    type="text"
                    name="phone"
                    value="<?= esc(old('phone', $customer['phone'] ?? '')); ?>"
                    placeholder="mis: 08xxx">
            </div>

            <div class="form-field">
                <label class="form-label">Email (opsional)</label>
                <input
                    class="form-input"
                    type="email"
                    name="email"
                    value="<?= esc(old('email', $customer['email'] ?? '')); ?>"
                    placeholder="mis: nama@email.com">
            </div>
        </div>

        <div class="form-check">
            <label class="form-check__label">
                <input
                    class="form-check__input"
                    type="checkbox"
                    name="is_active"
                    value="1"
                    <?= (old('is_active', $customer['is_active'] ?? 1) ? 'checked' : ''); ?>>
                Aktif
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('master/customers'); ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
