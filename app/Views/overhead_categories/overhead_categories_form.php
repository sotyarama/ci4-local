<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Tambah Kategori Overhead</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                Masukkan nama kategori (non gaji), mis: sewa, listrik, internet.
            </p>
        </div>
        <a href="<?= site_url('overhead-categories'); ?>"
           style="font-size:11px; padding:6px 10px; border-radius:999px; background:#111827; color:#e5e7eb; text-decoration:none;">
            Kembali
        </a>
    </div>

    <?php $errors = session('errors') ?? []; ?>

    <?php if (! empty($errors)): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:#3f1f1f; border:1px solid #b91c1c; color:#fecaca; font-size:12px;">
            <div style="font-weight:600; margin-bottom:4px;">Terjadi kesalahan input:</div>
            <ul style="margin:0 0 0 18px; padding:0;">
                <?php foreach ($errors as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('overhead-categories/store'); ?>" method="post">
        <?= csrf_field() ?>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:12px;">
            <div>
                <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:4px;">Nama Kategori</label>
                <input type="text"
                       name="name"
                       required
                       value="<?= old('name'); ?>"
                       placeholder="mis: Sewa"
                       style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
            </div>
            <div>
                <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:4px;">Aktif</label>
                <label style="font-size:12px; color:#e5e7eb;">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           <?= old('is_active', '1') ? 'checked' : ''; ?>
                           style="margin-right:6px;">
                    Tandai aktif
                </label>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px;">
            <button type="submit"
                    style="font-size:13px; padding:8px 16px; border-radius:999px; border:none; background:#22c55e; color:#022c22; cursor:pointer;">
                Simpan
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
