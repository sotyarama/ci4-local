<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Raw Materials - Form
 * - Fokus: minim inline style, pakai class dari theme-temurasa.css
 * - Create vs Edit:
 *   - Create: initial_stock & initial_cost
 *   - Edit:   current_stock + last cost (readonly info)
 */
$errors = session('errors') ?? [];
$isEdit = ! empty($material);
?>

<div class="card">
    <h2 class="page-title"><?= esc($title ?? 'Form Bahan Baku'); ?></h2>
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
                <label class="form-label">Nama Bahan</label>
                <input
                    class="form-input"
                    type="text"
                    name="name"
                    value="<?= esc(old('name', $material['name'] ?? '')); ?>"
                    required>
            </div>

            <div class="form-field">
                <label class="form-label">Satuan</label>
                <select class="form-input" name="unit_id" required>
                    <option value="">-- pilih satuan --</option>

                    <?php foreach (($units ?? []) as $u): ?>
                        <?php
                        $uid   = (string) ($u['id'] ?? '');
                        $uname = (string) ($u['name'] ?? '');
                        $short = (string) ($u['short_name'] ?? '');
                        $selected = ((string) old('unit_id', $material['unit_id'] ?? '') === $uid);
                        ?>
                        <option value="<?= esc($uid); ?>" <?= $selected ? 'selected' : ''; ?>>
                            <?= esc($uname); ?><?= $short !== '' ? ' (' . esc($short) . ')' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-field">
                <label class="form-label">Min. Stok</label>
                <input
                    class="form-input"
                    type="number"
                    step="0.001"
                    name="min_stock"
                    value="<?= esc(old('min_stock', $material['min_stock'] ?? '0')); ?>">
            </div>

            <?php if (! $isEdit): ?>
                <div class="form-field">
                    <label class="form-label">Stok Awal (opsional)</label>
                    <input
                        class="form-input"
                        type="number"
                        step="0.001"
                        name="initial_stock"
                        value="<?= esc(old('initial_stock', '0')); ?>">
                </div>

                <div class="form-field">
                    <label class="form-label">Harga per Satuan (opsional)</label>
                    <input
                        class="form-input"
                        type="number"
                        step="1"
                        name="initial_cost"
                        value="<?= esc(old('initial_cost', '0')); ?>">
                </div>
            <?php else: ?>
                <div class="form-field">
                    <label class="form-label">Stok Saat Ini</label>
                    <input
                        class="form-input"
                        type="number"
                        step="0.001"
                        name="current_stock"
                        value="<?= esc(old('current_stock', $material['current_stock'] ?? '0')); ?>">
                </div>

                <div class="form-field">
                    <label class="form-label">Last Cost (readonly untuk info)</label>
                    <input
                        class="form-input"
                        type="number"
                        step="1"
                        value="<?= esc($material['cost_last'] ?? '0'); ?>"
                        readonly
                        aria-readonly="true">
                    <div class="form-note">
                        Last Cost & Avg Cost akan berubah otomatis dari modul Pembelian / Stock Movement.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-check">
            <label class="form-check__label">
                <input
                    class="form-check__input"
                    type="checkbox"
                    name="is_active"
                    value="1"
                    <?= (old('is_active', $material['is_active'] ?? 1) ? 'checked' : ''); ?>>
                Aktif
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('master/raw-materials'); ?>" class="btn btn-secondary">Batal</a>
        </div>

        <?php if (! $isEdit): ?>
            <div class="form-note">
                Stok & harga akan diperbarui otomatis dari modul Pembelian dan Penyesuaian Stok.
            </div>
        <?php endif; ?>
    </form>
</div>

<?= $this->endSection() ?>