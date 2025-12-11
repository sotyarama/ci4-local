<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;"><?= esc($title ?? 'Kategori Menu'); ?></h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Kelola kategori untuk mengelompokkan menu/produk.
            </p>
        </div>
        <a href="<?= site_url('master/categories'); ?>"
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

    <form action="<?= ($mode ?? 'create') === 'edit'
            ? site_url('master/categories/update/' . ($category['id'] ?? 0))
            : site_url('master/categories/store'); ?>"
          method="post">
        <?= csrf_field() ?>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px; margin-bottom:12px;">
            <div>
                <label style="font-size:11px; color:var(--tr-muted-text); display:block; margin-bottom:4px;">Nama Kategori</label>
                <input type="text"
                       name="name"
                       required
                       value="<?= old('name', $category['name'] ?? ''); ?>"
                       placeholder="mis: Makanan, Minuman"
                       style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);">
            </div>
        </div>

        <div style="margin-bottom:12px;">
            <label style="font-size:11px; color:var(--tr-muted-text); display:block; margin-bottom:4px;">Deskripsi (opsional)</label>
            <textarea name="description"
                      rows="2"
                      style="width:100%; padding:6px 8px; font-size:12px; background:var(--tr-bg); border:1px solid var(--tr-border); border-radius:6px; color:var(--tr-text);"><?= old('description', $category['description'] ?? ''); ?></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px;">
            <button type="submit"
                    style="font-size:13px; padding:8px 16px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; cursor:pointer;">
                <?= ($mode ?? 'create') === 'edit' ? 'Simpan Perubahan' : 'Simpan'; ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>


