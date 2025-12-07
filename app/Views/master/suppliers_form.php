<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php $errors = session('errors') ?? []; ?>

<div class="card">
    <h2 style="margin:0 0 8px; font-size:18px;">
        <?= esc($title ?? 'Form Supplier'); ?>
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

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:12px;">
            <div>
                <label style="font-size:12px; display:block; margin-bottom:4px;">Nama Supplier</label>
                <input
                    type="text"
                    name="name"
                    value="<?= esc(old('name', $supplier['name'] ?? '')); ?>"
                    style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;"
                    required
                >
            </div>

            <div>
                <label style="font-size:12px; display:block; margin-bottom:4px;">Telepon (opsional)</label>
                <input
                    type="text"
                    name="phone"
                    value="<?= esc(old('phone', $supplier['phone'] ?? '')); ?>"
                    style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;"
                >
            </div>
        </div>

        <div style="margin-top:12px;">
            <label style="font-size:12px; display:block; margin-bottom:4px;">Alamat (opsional)</label>
            <textarea
                name="address"
                rows="3"
                style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #1f2937; background:#020617; color:#e5e7eb; font-size:13px;"
            ><?= esc(old('address', $supplier['address'] ?? '')); ?></textarea>
        </div>

        <div style="margin-top:12px;">
            <label style="font-size:12px;">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    <?= (old('is_active', $supplier['is_active'] ?? 1) ? 'checked' : ''); ?>
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
            <a href="<?= site_url('master/suppliers'); ?>"
               style="padding:8px 14px; border-radius:999px; border:1px solid #4b5563; font-size:13px; color:#e5e7eb; text-decoration:none;">
                Batal
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
