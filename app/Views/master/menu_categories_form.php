<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Categories - Form (Create/Edit)
 * - Minim inline style, konsisten dengan theme-temurasa.css
 * - Mode: create|edit ditentukan dari $mode
 */

$mode   = $mode ?? 'create';
$errors = session('errors') ?? [];
$isEdit = ($mode === 'edit');

$action = $isEdit
    ? site_url('master/categories/update/' . ($category['id'] ?? 0))
    : site_url('master/categories/store');
?>

<div class="tr-card">
    <div class="page-head">
        <div>
            <h2 class="page-title"><?= esc($title ?? 'Kategori Menu'); ?></h2>
            <p class="page-subtitle">Kelola kategori untuk mengelompokkan menu/produk.</p>
        </div>

        <a href="<?= site_url('master/categories'); ?>" class="tr-btn tr-btn-secondary tr-btn-sm">
            Kembali
        </a>
    </div>

    <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan input:</strong>
            <ul class="alert-list">
                <?php foreach ($errors as $err): ?>
                    <li><?= esc($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= esc($action); ?>" method="post" class="form">
        <?= csrf_field(); ?>

        <div class="form-grid">
            <div class="form-field">
                <label class="form-label">Nama Kategori</label>
                <input
                    class="form-input tr-control"
                    type="text"
                    name="name"
                    required
                    value="<?= esc(old('name', $category['name'] ?? '')); ?>"
                    placeholder="mis: Makanan, Minuman">
            </div>
        </div>

        <div class="form-field">
            <label class="form-label">Deskripsi (opsional)</label>
            <textarea
                class="form-input tr-control"
                name="description"
                rows="2"><?= esc(old('description', $category['description'] ?? '')); ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="tr-btn tr-btn-primary">
                <?= $isEdit ? 'Simpan Perubahan' : 'Simpan'; ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>