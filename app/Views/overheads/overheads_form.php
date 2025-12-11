<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Tambah Biaya Overhead</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Catat biaya operasional harian/bulanan.
            </p>
        </div>
        <a href="<?= site_url('overheads'); ?>"
           style="font-size:11px; padding:6px 10px; border-radius:999px; background:var(--tr-border); color:var(--tr-text); text-decoration:none;">
            Kembali
        </a>
    </div>

    <?php $errors = session('errors') ?? []; ?>

    <?php if (! empty($errors)): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); color:var(--tr-accent-brown); font-size:12px;">
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
                <label style="font-size:11px; color:var(--tr-muted-text); display:block; margin-bottom:4px;">Tanggal</label>
                <input type="date"
                       name="trans_date"
                       required
                       value="<?= old('trans_date', date('Y-m-d')); ?>"
                       style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
            </div>
            <div>
                <label style="font-size:11px; color:var(--tr-muted-text); display:block; margin-bottom:4px;">Kategori</label>
                <select name="category_id"
                        style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
                    <option value="">-- pilih kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id']; ?>" <?= old('category_id') == $cat['id'] ? 'selected' : ''; ?>>
                            <?= esc($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div style="font-size:11px; color:var(--tr-muted-text); margin-top:2px;">Gaji tidak dicatat di sini.</div>
                <div style="font-size:11px; color:var(--tr-muted-text); margin-top:4px;">
                    Perlu kategori baru? Tambah di <a href="<?= site_url('overhead-categories'); ?>" style="color:var(--tr-secondary-green);">Kategori Overhead</a>.
                </div>
            </div>
            <div>
                <label style="font-size:11px; color:var(--tr-muted-text); display:block; margin-bottom:4px;">Nominal</label>
                <input type="number"
                       step="0.01"
                       min="0"
                       name="amount"
                       required
                       value="<?= old('amount', '0'); ?>"
                       style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="font-size:11px; color:var(--tr-muted-text); display:block; margin-bottom:4px;">Deskripsi (opsional)</label>
            <textarea name="description"
                      rows="3"
                      style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text); resize:vertical;"><?= old('description'); ?></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px;">
            <button type="submit"
                    style="font-size:13px; padding:8px 16px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; cursor:pointer;">
                Simpan
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>


