<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Master Resep Menu</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Mapping menu ke bahan baku (BOM) sebagai dasar perhitungan HPP.
            </p>
        </div>
        <a href="<?= site_url('master/recipes/create'); ?>"
           style="font-size:12px; padding:6px 12px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; text-decoration:none;">
            + Tambah Resep
        </a>
    </div>

    <table style="width:100%; border-collapse:collapse; font-size:12px;">
        <thead>
            <tr>
                <th style="text-align:left; padding:8px; border-bottom:1px solid var(--tr-border);">Kategori</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid var(--tr-border);">Nama Menu</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid var(--tr-border);">Harga Jual</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid var(--tr-border);">HPP / Porsi</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid var(--tr-border);">Status Resep</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid var(--tr-border);">Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php if (empty($menus)): ?>
            <tr>
                <td colspan="5" style="padding:8px; text-align:center; color:var(--tr-muted-text);">
                    Belum ada data menu.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($menus as $menu): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($menu['category_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($menu['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        Rp <?= number_format((float)($menu['price'] ?? 0), 0, ',', '.'); ?>
                    </td>

                    <!-- 🔹 Kolom HPP / Porsi -->
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?php if (!empty($menu['recipe_id']) && $menu['hpp_per_yield'] !== null): ?>
                            Rp <?= number_format((float)$menu['hpp_per_yield'], 0, ',', '.'); ?>
                            <span style="font-size:10px; color:var(--tr-muted-text);">
                                / <?= esc($menu['yield_unit'] ?? 'porsi'); ?>
                            </span>
                        <?php else: ?>
                            <span style="font-size:11px; color:var(--tr-muted-text);">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- Status Resep -->
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <?php if (!empty($menu['recipe_id'])): ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:rgba(122,154,108,0.14); color:var(--tr-secondary-green); border:1px solid rgba(122,154,108,0.14);">
                                Sudah ada resep
                            </span>
                        <?php else: ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:var(--tr-secondary-beige); color:var(--tr-accent-brown); border:1px solid var(--tr-accent-brown);">
                                Belum ada resep
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- Aksi -->
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">
                        <div style="display:flex; justify-content:center; gap:8px;">
                            <?php if (!empty($menu['recipe_id'])): ?>
                                <a href="<?= site_url('master/recipes/edit/' . $menu['recipe_id']); ?>"
                                   style="display:inline-flex; align-items:center; gap:6px; font-size:11px; color:#fff; text-decoration:none; background:var(--tr-primary); border:1px solid var(--tr-primary); padding:6px 12px; border-radius:10px; min-width:110px; justify-content:center;">
                                    <span style="font-size:12px;">✏</span>
                                    <span>Edit Resep</span>
                                </a>
                            <?php else: ?>
                                <span style="font-size:11px; color:var(--tr-muted-text);">
                                    Buat dari tombol "+ Tambah Resep"
                                </span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:12px; font-size:11px; color:var(--tr-muted-text);">
        Ke depan, halaman ini bisa diperluas untuk menampilkan HPP dan food cost per menu.
    </div>
</div>

<?= $this->endSection() ?>
