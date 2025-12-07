<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php $errors = session('errors') ?? []; ?>

<div class="card">
    <h2 style="margin:0 0 8px; font-size:18px;">
        <?= esc($title ?? 'Form Bahan Baku'); ?>
    </h2>
    <p style="margin:0 0 16px; font-size:13px; color:#9ca3af;">
        <?= esc($subtitle ?? ''); ?>
    </p>

    <?php if (!empty($errors)): ?>
        <div style="background:#7f1d1d; border-radius:8px; padding:8px 10px; border:1px solid #b91c1c; font-size:12px; color:#fee2e2; margin-bottom:12px;">
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin:4px 0 0 16px; padding:0;">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= esc($formAction ?? '#'); ?>" method="post">
        <?= csrf_field(); ?>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
            <div>
                <label style="font-size:12px; display:block; margin-bottom:4px;">Nama Bahan</label>
                <input
                    type="text"
                    name="name"
                    value="<?= esc(old('name', $material['name'] ?? '')); ?>"
                    style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;"
                    required
                >
            </div>

            <div>
                <label style="font-size:12px; display:block; margin-bottom:4px;">Satuan</label>
                <select
                    name="unit_id"
                    style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;"
                    required
                >
                    <option value="">-- pilih satuan --</option>
                    <?php foreach ($units as $u): ?>
                        <option
                            value="<?= $u['id']; ?>"
                            <?= (string) old('unit_id', $material['unit_id'] ?? '') === (string) $u['id'] ? 'selected' : ''; ?>
                        >
                            <?= esc($u['name']); ?> (<?= esc($u['short_name']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label style="font-size:12px; display:block; margin-bottom:4px;">Min. Stok</label>
                <input
                    type="number"
                    step="0.001"
                    name="min_stock"
                    value="<?= esc(old('min_stock', $material['min_stock'] ?? '0')); ?>"
                    style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;"
                >
            </div>

            <?php if (empty($material)): ?>
                <div>
                    <label style="font-size:12px; display:block; margin-bottom:4px;">Stok Awal (opsional)</label>
                    <input
                        type="number"
                        step="0.001"
                        name="initial_stock"
                        value="<?= esc(old('initial_stock', '0')); ?>"
                        style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;"
                    >
                </div>

                <div>
                    <label style="font-size:12px; display:block; margin-bottom:4px;">Harga per Satuan (opsional)</label>
                    <input
                        type="number"
                        step="1"
                        name="initial_cost"
                        value="<?= esc(old('initial_cost', '0')); ?>"
                        style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;"
                    >
                </div>
            <?php else: ?>
                <div>
                    <label style="font-size:12px; display:block; margin-bottom:4px;">Stok Saat Ini</label>
                    <input
                        type="number"
                        step="0.001"
                        name="current_stock"
                        value="<?= esc(old('current_stock', $material['current_stock'] ?? '0')); ?>"
                        style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;"
                    >
                </div>

                <div>
                    <label style="font-size:12px; display:block; margin-bottom:4px;">Last Cost (readonly untuk info)</label>
                    <input
                        type="number"
                        step="100"
                        value="<?= esc($material['cost_last'] ?? '0'); ?>"
                        style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #1f2937; background:#020617; color:#9ca3af; font-size:13px;"
                        disabled
                    >
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top:12px;">
            <label style="font-size:12px;">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    <?= (old('is_active', $material['is_active'] ?? 1) ? 'checked' : ''); ?>
                    style="margin-right:6px;"
                >
                Aktif
            </label>
        </div>

        <div style="margin-top:16px; display:flex; gap:8px;">
            <button type="submit"
                    style="padding:8px 14px; border-radius:999px; border:none; background:#3b82f6; color:#fff; font-size:13px; cursor:pointer;">
                Simpan
            </button>
            <a href="<?= site_url('master/raw-materials'); ?>"
               style="padding:8px 14px; border-radius:999px; border:1px solid #4b5563; font-size:13px; color:#e5e7eb; text-decoration:none;">
                Batal
            </a>
        </div>

        <div style="margin-top:10px; font-size:11px; color:#6b7280;">
            Stok & harga akan diperbarui otomatis dari modul Pembelian dan Penyesuaian Stok.
        </div>
    </form>
</div>

<?= $this->endSection() ?>
