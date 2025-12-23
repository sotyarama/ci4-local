<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Products - Index
 * - Fokus: readability + no inline style
 * - DataTables untuk pencarian/sort/pagination
 */
$fmtMoney = static fn($v): string => number_format((float) ($v ?? 0), 0, ',', '.');
?>

<div class="card">
    <!-- Header actions -->
    <div class="page-head">
        <div class="page-head__left"></div>

        <div class="page-head__right">
            <a class="btn btn-primary btn-sm" href="<?= site_url('master/products/create'); ?>">
                + Tambah Produk
            </a>
        </div>
    </div>

    <h2 class="page-title">Master Produk</h2>
    <p class="page-subtitle">
        Daftar menu cafe yang saat ini terdaftar di sistem.
    </p>

    <?php if (empty($menus)): ?>
        <p class="empty-state">
            Belum ada data menu. Silakan tambahkan produk menggunakan tombol "Tambah Produk".
        </p>
    <?php else: ?>
        <div class="table-tools">
            <div class="table-tools__hint">Filter nama/kategori/status:</div>
            <input
                type="text"
                id="products-filter"
                class="table-tools__search"
                placeholder="Cari produk...">
        </div>

        <table class="table" id="menuTable">
            <thead>
                <tr>
                    <th class="table__th">Kategori</th>
                    <th class="table__th">Nama Menu</th>
                    <th class="table__th">SKU</th>
                    <th class="table__th table__th--right">Harga</th>
                    <th class="table__th table__th--center">Status</th>
                    <th class="table__th table__th--center">Aksi</th>
                </tr>
            </thead>

            <tbody id="products-table-body">
                <?php foreach ($menus as $menu): ?>
                    <?php
                    $isActive    = ! empty($menu['is_active']);
                    $statusLabel = $isActive ? 'aktif' : 'nonaktif';
                    $id          = (int) ($menu['id'] ?? 0);
                    ?>
                    <tr
                        data-name="<?= esc(strtolower((string) ($menu['name'] ?? ''))); ?>"
                        data-cat="<?= esc(strtolower((string) ($menu['category_name'] ?? ''))); ?>"
                        data-status="<?= esc($statusLabel); ?>">

                        <td class="table__td"><?= esc($menu['category_name'] ?? '-'); ?></td>
                        <td class="table__td"><?= esc($menu['name'] ?? '-'); ?></td>
                        <td class="table__td"><?= esc($menu['sku'] ?? ''); ?></td>
                        <td class="table__td table__td--right">Rp <?= $fmtMoney($menu['price'] ?? 0); ?></td>

                        <td class="table__td table__td--center">
                            <?php if ($isActive): ?>
                                <span class="badge badge--active">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge--inactive">Nonaktif</span>
                            <?php endif; ?>
                        </td>

                        <td class="table__td table__td--center">
                            <div class="row-actions">
                                <a class="btn btn-primary btn-sm" href="<?= site_url('master/products/edit/' . $id); ?>">
                                    Edit
                                </a>

                                <form
                                    action="<?= site_url('master/products/delete/' . $id); ?>"
                                    method="post"
                                    class="inline"
                                    onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footnote">
        Data ini berasal dari tabel <code>menus</code> dan <code>menu_categories</code>.
    </div>
</div>

<script src="/assets/js/datatables/menu.js" defer></script>

<?= $this->endSection() ?>
