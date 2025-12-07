<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Master Resep Menu</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                Mapping menu ke bahan baku (BOM) sebagai dasar perhitungan HPP.
            </p>
        </div>
        <a href="<?= site_url('master/recipes/create'); ?>"
           style="font-size:12px; padding:6px 12px; border-radius:999px; border:none; background:#22c55e; color:#022c22; text-decoration:none;">
            + Tambah Resep
        </a>
    </div>

    <table style="width:100%; border-collapse:collapse; font-size:12px;">
        <thead>
            <tr>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #111827;">Kategori</th>
                <th style="text-align:left; padding:8px; border-bottom:1px solid #111827;">Nama Menu</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid #111827;">Harga Jual</th>
                <th style="text-align:right; padding:8px; border-bottom:1px solid #111827;">HPP / Porsi</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid #111827;">Status Resep</th>
                <th style="text-align:center; padding:8px; border-bottom:1px solid #111827;">Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php if (empty($menus)): ?>
            <tr>
                <td colspan="5" style="padding:8px; text-align:center; color:#9ca3af;">
                    Belum ada data menu.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($menus as $menu): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827;">
                        <?= esc($menu['category_name'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827;">
                        <?= esc($menu['name']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">
                        Rp <?= number_format((float)($menu['price'] ?? 0), 0, ',', '.'); ?>
                    </td>

                    <!-- 🔹 Kolom HPP / Porsi -->
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:right;">
                        <?php if (!empty($menu['recipe_id']) && $menu['hpp_per_yield'] !== null): ?>
                            Rp <?= number_format((float)$menu['hpp_per_yield'], 0, ',', '.'); ?>
                            <span style="font-size:10px; color:#9ca3af;">
                                / <?= esc($menu['yield_unit'] ?? 'porsi'); ?>
                            </span>
                        <?php else: ?>
                            <span style="font-size:11px; color:#9ca3af;">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- Status Resep -->
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:center;">
                        <?php if (!empty($menu['recipe_id'])): ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:#022c22; color:#6ee7b7; border:1px solid #064e3b;">
                                Sudah ada resep
                            </span>
                        <?php else: ?>
                            <span style="font-size:11px; padding:2px 8px; border-radius:999px; background:#3f1f1f; color:#fecaca; border:1px solid #991b1b;">
                                Belum ada resep
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- Aksi -->
                    <td style="padding:6px 8px; border-bottom:1px solid #111827; text-align:center;">
                        <?php if (!empty($menu['recipe_id'])): ?>
                            <a href="<?= site_url('master/recipes/edit/' . $menu['recipe_id']); ?>"
                            style="font-size:11px; margin-right:6px; color:#60a5fa; text-decoration:none;">
                                Edit Resep
                            </a>
                        <?php else: ?>
                            <span style="font-size:11px; color:#9ca3af;">
                                Buat dari tombol "+ Tambah Resep"
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:12px; font-size:11px; color:#6b7280;">
        Ke depan, halaman ini bisa diperluas untuk menampilkan HPP dan food cost per menu.
    </div>
</div>

<?= $this->endSection() ?>
