<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Products - Form
 * - Fokus: minim inline style, pakai class theme-temurasa.css
 */
$errors = session('errors') ?? [];
$titleText = $title ?? 'Form Produk';
$subtitleText = $subtitle ?? '';
?>

<div class="tr-card">
    <div class="page-head">
        <div>
            <h2 class="page-title"><?= esc($titleText); ?></h2>
            <?php if ($subtitleText !== ''): ?>
                <p class="page-subtitle"><?= esc($subtitleText); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <div class="alert-heading">Terjadi kesalahan:</div>
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
                <label class="form-label" for="product-name">Nama Menu</label>
                <input
                    id="product-name"
                    class="form-input tr-control"
                    type="text"
                    name="name"
                    value="<?= esc(old('name', $menu['name'] ?? '')); ?>"
                    required>
            </div>

            <div class="form-field">
                <label class="form-label" for="product-category">Kategori</label>
                <select
                    id="product-category"
                    class="form-input tr-control"
                    name="menu_category_id"
                    required>
                    <option value="">-- pilih kategori --</option>
                    <?php foreach (($categories ?? []) as $cat): ?>
                        <?php
                        $catId = (string) ($cat['id'] ?? '');
                        $selected = ((string) old('menu_category_id', $menu['menu_category_id'] ?? '') === $catId);
                        ?>
                        <option value="<?= esc($catId); ?>" <?= $selected ? 'selected' : ''; ?>>
                            <?= esc($cat['name'] ?? '-'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-field">
                <label class="form-label" for="product-sku">SKU (opsional)</label>
                <input
                    id="product-sku"
                    class="form-input tr-control"
                    type="text"
                    name="sku"
                    value="<?= esc(old('sku', $menu['sku'] ?? '')); ?>"
                    placeholder="">
            </div>

            <div class="form-field">
                <label class="form-label" for="product-price">Harga Jual</label>
                <input
                    id="product-price"
                    class="form-input tr-control"
                    type="number"
                    name="price"
                    step="100"
                    min="0"
                    inputmode="numeric"
                    value="<?= esc(old('price', $menu['price'] ?? '0')); ?>"
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
                    <?= (old('is_active', $menu['is_active'] ?? 1) ? 'checked' : ''); ?>>
                Aktif
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="tr-btn tr-btn-primary">Simpan</button>
            <a class="tr-btn tr-btn-secondary" href="<?= site_url('master/products'); ?>">Batal</a>
        </div>

    <div class="form-note">
        Perubahan di sini hanya mengatur menu &amp; harga jual. HPP dan resep akan diatur di modul Resep.
    </div>
</form>
</div>

<?= $this->endSection() ?>
