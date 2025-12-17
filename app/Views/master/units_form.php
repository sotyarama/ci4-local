<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Units - Form
 */
$errors = session('errors') ?? [];
?>

<div class="card">
    <h2 class="page-title"><?= esc($title ?? 'Form Satuan'); ?></h2>
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
                <label class="form-label">Nama Satuan</label>
                <input
                    class="form-input"
                    type="text"
                    name="name"
                    value="<?= esc(old('name', $unit['name'] ?? '')); ?>"
                    placeholder="mis: Gram, Mililiter, Pcs"
                    required>
            </div>

            <div class="form-field">
                <label class="form-label">Singkatan</label>
                <input
                    class="form-input"
                    type="text"
                    name="short_name"
                    value="<?= esc(old('short_name', $unit['short_name'] ?? '')); ?>"
                    placeholder="mis: g, ml, pcs"
                    required>
            </div>
        </div>

        <div class="form-check">
            <label class="form-check__label">
                <input
                    class="form-check__input"
                    type="checkbox"
                    name="is_active"
                    value="1"
                    <?= (old('is_active', $unit['is_active'] ?? 1) ? 'checked' : ''); ?>>
                Aktif
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('master/units'); ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>