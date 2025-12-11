<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div></div>
        <a href="<?= site_url('master/products/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; text-decoration:none;">
            + Tambah Produk
        </a>
    </div>

    <h2 style="margin:0 0 8px; font-size:18px;">Master Produk</h2>
    <p style="margin:0 0 16px; font-size:13px; color:var(--tr-muted-text);">
        Daftar menu cafe yang saat ini terdaftar di sistem.
    </p>

    <?php if (empty($menus)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:0;">
            Belum ada data menu. Silakan tambahkan produk menggunakan tombol "Tambah Produk".
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:8px; border-bottom:1px solid var(--tr-border);">Kategori</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid var(--tr-border);">Nama Menu</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid var(--tr-border);">SKU</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid var(--tr-border);">Harga</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid var(--tr-border);">Status</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($menus as $menu): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($menu['category_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($menu['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($menu['sku'] ?? ''); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format((float) $menu['price'], 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <?php if (!empty($menu['is_active'])): ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:rgba(122,154,108,0.14); color:var(--tr-secondary-green); border:1px solid rgba(122,154,108,0.14);">
                                Aktif
                            </span>
                        <?php else: ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown);">
                                Nonaktif
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <a href="<?= site_url('master/products/edit/' . $menu['id']); ?>"
                           style="font-size:11px; margin-right:6px; color:#fff; text-decoration:none; background:var(--tr-primary); border:1px solid var(--tr-primary); padding:6px 10px; border-radius:999px;">
                            Edit
                        </a>

                        <form action="<?= site_url('master/products/delete/' . $menu['id']); ?>"
                              method="post"
                              style="display:inline;"
                              onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                            <?= csrf_field(); ?>
                            <button type="submit"
                                    style="font-size:11px; border:1px solid var(--tr-accent-brown); background:var(--tr-accent-brown); color:#fff; cursor:pointer; padding:6px 10px; border-radius:999px;">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="margin-top:12px; font-size:11px; color:var(--tr-muted-text);">
        Data ini berasal dari tabel <code>menus</code> dan <code>menu_categories</code>.
    </div>
</div>

<?= $this->endSection() ?>

