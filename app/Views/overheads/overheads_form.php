<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Tambah Biaya Overhead</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                Catat biaya operasional harian/bulanan.
            </p>
        </div>
        <a href="<?= site_url('overheads'); ?>"
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

    <form action="<?= site_url('overheads/store'); ?>" method="post">
        <?= csrf_field() ?>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:12px;">
            <div>
                <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:4px;">Tanggal</label>
                <input type="date"
                       name="trans_date"
                       required
                       value="<?= old('trans_date', date('Y-m-d')); ?>"
                       style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
            </div>
            <div>
                <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:4px;">Kategori</label>
                <select name="category_id"
                        style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
                    <option value="">-- pilih kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id']; ?>" <?= old('category_id') == $cat['id'] ? 'selected' : ''; ?>>
                            <?= esc($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div style="font-size:11px; color:#6b7280; margin-top:2px;">Gaji tidak dicatat di sini.</div>
                <div style="font-size:11px; color:#9ca3af; margin-top:4px;">
                    Perlu kategori baru? Tambah di <a href="<?= site_url('overhead-categories'); ?>" style="color:#93c5fd;">Kategori Overhead</a>.
                </div>
            </div>
            <div>
                <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:4px;">Nominal</label>
                <input type="number"
                       step="0.01"
                       min="0"
                       name="amount"
                       required
                       value="<?= old('amount', '0'); ?>"
                       style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb;">
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="font-size:11px; color:#9ca3af; display:block; margin-bottom:4px;">Deskripsi (opsional)</label>
            <textarea name="description"
                      rows="3"
                      style="width:100%; padding:6px 8px; font-size:12px; background:#020617; border:1px solid #374151; border-radius:6px; color:#e5e7eb; resize:vertical;"><?= old('description'); ?></textarea>
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
