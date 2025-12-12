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

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
        <div style="font-size:12px; color:var(--tr-muted-text);">Filter nama/kategori/status:</div>
        <input type="text" id="recipe-filter" placeholder="Cari menu..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:200px;">
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

        <tbody id="recipe-table-body">
        <?php if (empty($menus)): ?>
            <tr>
                <td colspan="6" style="padding:8px; text-align:center; color:var(--tr-muted-text);">
                    Belum ada data menu.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($menus as $menu): ?>
                <tr data-name="<?= esc(strtolower($menu['name'])); ?>" data-cat="<?= esc(strtolower($menu['category_name'] ?? '')); ?>" data-status="<?= !empty($menu['recipe_id']) ? 'sudah' : 'belum'; ?>">
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
                        <?php if (!empty($menu['recipe_id'])): ?>
                            <a href="<?= site_url('master/recipes/edit/' . $menu['recipe_id']); ?>"
                               style="display:inline-block; font-size:11px; padding:6px 12px; border-radius:999px; background:var(--tr-primary); color:#fff; text-decoration:none;">
                                Edit
                            </a>
                        <?php else: ?>
                            <span style="font-size:11px; color:var(--tr-muted-text);">
                                Buat dari tombol "+ Tambah Resep"
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr id="recipe-noresult" style="display:none;">
                <td colspan="6" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:12px; font-size:11px; color:var(--tr-muted-text);">
        Ke depan, halaman ini bisa diperluas untuk menampilkan HPP dan food cost per menu.
    </div>
</div>

<script>
    (function() {
        const input = document.getElementById('recipe-filter');
        const tbody = document.getElementById('recipe-table-body');
        const nores = document.getElementById('recipe-noresult');
        if (!input || !tbody) return;
        let timer;
        input.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const q = (input.value || '').toLowerCase().trim();
                let shown = 0;
                tbody.querySelectorAll('tr').forEach(function(tr) {
                    if (tr.id === 'recipe-noresult') return;
                    const name = tr.dataset.name || '';
                    const cat = tr.dataset.cat || '';
                    const status = tr.dataset.status || '';
                    const match = !q || name.includes(q) || cat.includes(q) || status.includes(q);
                    tr.style.display = match ? '' : 'none';
                    if (match) shown++;
                });
                if (nores) nores.style.display = shown === 0 ? '' : 'none';
            }, 200);
        });
    })();
</script>

<?= $this->endSection() ?>
