<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Biaya Overhead</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Catat dan pantau biaya operasional harian/bulanan.
            </p>
        </div>
        <a href="<?= site_url('overheads/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:var(--tr-primary); color:#fff; text-decoration:none;">
            + Tambah
        </a>
    </div>

    <!-- Filter -->
    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_from" style="margin-bottom:2px; color:var(--tr-muted-text);">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from"
                   value="<?= esc($dateFrom); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_to" style="margin-bottom:2px; color:var(--tr-muted-text);">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to"
                   value="<?= esc($dateTo); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="category_id" style="margin-bottom:2px; color:var(--tr-muted-text);">Kategori</label>
            <select name="category_id" id="category_id"
                    style="min-width:180px; padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="">-- semua kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <?php
                        $label = $cat['name'];
                        if ((int) ($cat['is_active'] ?? 0) !== 1) {
                            $label .= ' (Nonaktif)';
                        }
                    ?>
                    <option value="<?= $cat['id']; ?>" <?= (string)($filterCategory ?? '') === (string)$cat['id'] ? 'selected' : ''; ?>>
                        <?= esc($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; gap:6px;">
            <button type="submit"
                    style="margin-top:18px; padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:var(--tr-text); cursor:pointer;">
                Terapkan Filter
            </button>

            <a href="<?= site_url('overheads'); ?>"
               style="margin-top:18px; padding:6px 10px; border-radius:999px; border:1px solid var(--tr-muted-text); font-size:12px; background:var(--tr-bg); color:var(--tr-muted-text); text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:rgba(122,154,108,0.14); border:1px solid var(--tr-primary); color:var(--tr-secondary-green); font-size:12px;">
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">
            Belum ada data overhead.
        </p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter tanggal/kategori/deskripsi:</div>
            <input type="text" id="overheads-filter" placeholder="Cari overhead..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:220px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Tanggal</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Kategori</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid var(--tr-border);">Deskripsi</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Nominal</th>
            </tr>
            </thead>
            <tbody id="overheads-table-body">
            <?php foreach ($rows as $row): ?>
                <tr data-date="<?= esc(strtolower($row['trans_date'])); ?>" data-cat="<?= esc(strtolower($row['category_name'] ?? $row['category'] ?? '')); ?>" data-desc="<?= esc(strtolower($row['description'] ?? '')); ?>">
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['trans_date']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?php
                            $catLabel = $row['category_name'] ?? $row['category'] ?? '-';
                            $catActive = (int) ($row['category_active'] ?? 1) === 1;
                        ?>
                        <?= esc($catLabel); ?>
                        <?php if (! $catActive): ?>
                            <span style="font-size:10px; color:var(--tr-secondary-beige); margin-left:6px;">(Nonaktif)</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['description'] ?? ''); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; font-weight:600;">
                        Rp <?= number_format((float) $row['amount'], 0, ',', '.'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); font-weight:bold;">
                    TOTAL
                </td>
                <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">
                    Rp <?= number_format((float) $total, 0, ',', '.'); ?>
                </td>
            </tr>
            <tr id="overheads-noresult" style="display:none;">
                <td colspan="4" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    (function() {
        function init() {
            if (!window.App || !App.setupFilter) {
                return setTimeout(init, 50);
            }
            App.setupFilter({
                input: '#overheads-filter',
                rows: document.querySelectorAll('#overheads-table-body tr:not(#overheads-noresult)'),
                noResult: '#overheads-noresult',
                fields: ['date','cat','desc'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
