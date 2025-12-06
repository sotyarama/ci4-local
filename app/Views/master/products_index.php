<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div></div>
        <a href="<?= site_url('master/products/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:#22c55e; color:#022c22; text-decoration:none;">
            + Tambah Produk
        </a>
    </div>

    <h2 style="margin:0 0 8px; font-size:18px;">Master Produk</h2>
    <p style="margin:0 0 16px; font-size:13px; color:#9ca3af;">
        Daftar menu cafe yang saat ini terdaftar di sistem.
    </p>

    <?php if (empty($menus)): ?>
        <p style="font-size:12px; color:#9ca3af; margin:0;">
            Belum ada data menu. Silakan tambahkan produk menggunakan tombol "Tambah Produk".
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #111827;">Kategori</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #111827;">Nama Menu</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #111827;">SKU</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid #111827;">Harga</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid #111827;">Status</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid #111827;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($menus as $menu): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827;">
                        <?= esc($menu['category_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827;">
                        <?= esc($menu['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827;">
                        <?= esc($menu['sku'] ?? ''); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">
                        Rp <?= number_format((float) $menu['price'], 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:center;">
                        <?php if (!empty($menu['is_active'])): ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:#022c22; color:#6ee7b7; border:1px solid #064e3b;">
                                Aktif
                            </span>
                        <?php else: ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:#3f1f1f; color:#fecaca; border:1px solid #991b1b;">
                                Nonaktif
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:center;">
                        <a href="<?= site_url('master/products/edit/' . $menu['id']); ?>"
                           style="font-size:11px; margin-right:6px; color:#60a5fa; text-decoration:none;">
                            Edit
                        </a>

                        <form action="<?= site_url('master/products/delete/' . $menu['id']); ?>"
                              method="post"
                              style="display:inline;"
                              onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                            <?= csrf_field(); ?>
                            <button type="submit"
                                    style="font-size:11px; border:none; background:none; color:#fca5a5; cursor:pointer;">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="margin-top:12px; font-size:11px; color:#6b7280;">
        Data ini berasal dari tabel <code>menus</code> dan <code>menu_categories</code>.
    </div>
</div>

<?= $this->endSection() ?>
