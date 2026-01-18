<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Suppliers - Form
 * - Fokus: minim inline style, pakai class dari theme-temurasa.css
 * - Tetap kompatibel dengan controller (create/edit) lewat $supplier & $formAction
 */
$errors = session('errors') ?? [];
?>

<div class="tr-card">
    <h2 class="page-title"><?= esc($title ?? 'Form Supplier'); ?></h2>
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
                <label class="form-label">Nama Supplier</label>
                <input
                    class="form-input tr-control"
                    type="text"
                    name="name"
                    value="<?= esc(old('name', $supplier['name'] ?? '')); ?>"
                    required>
            </div>

            <div class="form-field">
                <label class="form-label">Telepon (opsional)</label>
                <input
                    class="form-input tr-control"
                    type="text"
                    name="phone"
                    value="<?= esc(old('phone', $supplier['phone'] ?? '')); ?>">
            </div>
        </div>

        <div class="form-field form-field--stack">
            <label class="form-label">Alamat (opsional)</label>
            <textarea
                class="form-input tr-control"
                name="address"
                rows="3"><?= esc(old('address', $supplier['address'] ?? '')); ?></textarea>
        </div>

        <div class="form-check">
            <label class="form-check__label">
                <input
                    class="form-check__input"
                    type="checkbox"
                    name="is_active"
                    value="1"
                    <?= (old('is_active', $supplier['is_active'] ?? 1) ? 'checked' : ''); ?>>
                Aktif
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="tr-btn tr-btn-primary">Simpan</button>
            <a href="<?= site_url('master/suppliers'); ?>" class="tr-btn tr-btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>