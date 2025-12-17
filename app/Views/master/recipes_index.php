<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Master Recipes - Index (Refactored)
 * - Behavior SAMA dengan versi lama
 * - Styling konsisten dengan modul lain
 * - Filter pakai App.setupFilter
 */
$fmtMoney = static fn($v) => number_format((float) ($v ?? 0), 0, ',', '.');
$canCreate = $canCreateRecipe ?? true;
?>

<div class="card">

    <!-- Header -->
    <div class="page-head">
        <div>
            <h2 class="page-title" style="margin:0 0 4px;">Master Resep Menu</h2>
            <p class="page-subtitle" style="margin:0;">
                Mapping menu ke bahan baku (BOM) sebagai dasar perhitungan HPP.
            </p>
        </div>

        <?php if ($canCreate): ?>
            <a href="<?= site_url('master/recipes/create'); ?>" class="btn btn-primary btn-sm">
                + Tambah Resep
            </a>
        <?php else: ?>
            <button class="btn btn-secondary btn-sm" disabled
                title="Semua menu sudah memiliki resep">
                + Tambah Resep
            </button>
        <?php endif; ?>
    </div>

    <!-- Filter -->
    <div class="table-tools">
        <div class="table-tools__hint">Filter nama/kategori/status:</div>
        <input
            type="text"
            id="recipe-filter"
            class="table-tools__search"
            placeholder="Cari menu...">
    </div>

    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th class="table__th">Kategori</th>
                <th class="table__th">Nama Menu</th>
                <th class="table__th table__th--right">Harga Jual</th>
                <th class="table__th table__th--right">HPP / Porsi</th>
                <th class="table__th table__th--center">Status Resep</th>
                <th class="table__th table__th--center">Aksi</th>
            </tr>
        </thead>

        <tbody id="recipe-table-body">
            <?php if (empty($menus)): ?>
                <tr>
                    <td colspan="6" class="table__td table__td--center muted">
                        Belum ada data menu.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($menus as $menu): ?>
                    <?php
                    $menuName = (string) ($menu['name'] ?? '');
                    $catName  = (string) ($menu['category_name'] ?? '-');
                    $hasRecipe = ! empty($menu['recipe_id']);
                    $status = $hasRecipe ? 'sudah' : 'belum';
                    ?>
                    <tr
                        data-name="<?= esc(strtolower($menuName)); ?>"
                        data-category="<?= esc(strtolower($catName)); ?>"
                        data-status="<?= esc($status); ?>">

                        <td class="table__td"><?= esc($catName); ?></td>
                        <td class="table__td"><?= esc($menuName); ?></td>

                        <td class="table__td table__td--right">
                            Rp <?= $fmtMoney($menu['price'] ?? 0); ?>
                        </td>

                        <td class="table__td table__td--right">
                            <?php if ($hasRecipe && $menu['hpp_per_yield'] !== null): ?>
                                Rp <?= $fmtMoney($menu['hpp_per_yield']); ?>
                                <span class="muted" style="font-size:10px;">
                                    / <?= esc($menu['yield_unit'] ?? 'porsi'); ?>
                                </span>
                            <?php else: ?>
                                <span class="muted">-</span>
                            <?php endif; ?>
                        </td>

                        <td class="table__td table__td--center">
                            <?php if ($hasRecipe): ?>
                                <span class="badge badge--active">Sudah ada</span>
                            <?php else: ?>
                                <span class="badge badge--inactive">Belum</span>
                            <?php endif; ?>
                        </td>

                        <td class="table__td table__td--center">
                            <?php if ($hasRecipe): ?>
                                <a href="<?= site_url('master/recipes/edit/' . $menu['recipe_id']); ?>"
                                    class="btn btn-primary btn-sm">
                                    Edit
                                </a>
                            <?php else: ?>
                                <span class="muted" style="font-size:11px;">
                                    Buat dari tombol “Tambah Resep”
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr id="recipe-noresult" style="display:none;">
                    <td colspan="6" class="table__td table__td--center muted">
                        Tidak ada hasil.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footnote">
        Ke depan, halaman ini bisa diperluas untuk menampilkan food cost & margin per menu.
    </div>
</div>

<script>
    (function() {
        function initFilter() {
            if (!window.App || !App.setupFilter) {
                return setTimeout(initFilter, 50);
            }

            App.setupFilter({
                input: '#recipe-filter',
                rows: document.querySelectorAll('#recipe-table-body tr:not(#recipe-noresult)'),
                noResult: '#recipe-noresult',
                fields: ['name', 'category', 'status'],
                debounce: 200
            });
        }

        document.addEventListener('DOMContentLoaded', initFilter);
    })();
</script>

<?= $this->endSection() ?>