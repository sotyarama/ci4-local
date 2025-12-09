<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Biaya Overhead</h2>
            <p style="margin:2px 0 0; font-size:12px; color:#9ca3af;">
                Catat dan pantau biaya operasional harian/bulanan.
            </p>
        </div>
        <a href="<?= site_url('overheads/create'); ?>"
           style="font-size:12px; padding:6px 10px; border-radius:999px; border:none; background:#22c55e; color:#022c22; text-decoration:none;">
            + Tambah
        </a>
    </div>

    <!-- Filter -->
    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_from" style="margin-bottom:2px; color:#d1d5db;">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from"
                   value="<?= esc($dateFrom); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid #374151; background:#020617; color:#e5e7eb; font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_to" style="margin-bottom:2px; color:#d1d5db;">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to"
                   value="<?= esc($dateTo); ?>"
                   style="padding:5px 8px; border-radius:6px; border:1px solid #374151; background:#020617; color:#e5e7eb; font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="category_id" style="margin-bottom:2px; color:#d1d5db;">Kategori</label>
            <select name="category_id" id="category_id"
                    style="min-width:180px; padding:5px 8px; border-radius:6px; border:1px solid #374151; background:#020617; color:#e5e7eb; font-size:12px;">
                <option value="">-- semua kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id']; ?>" <?= (string)($filterCategory ?? '') === (string)$cat['id'] ? 'selected' : ''; ?>>
                        <?= esc($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; gap:6px;">
            <button type="submit"
                    style="margin-top:18px; padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:#2563eb; color:#e5e7eb; cursor:pointer;">
                Terapkan Filter
            </button>

            <a href="<?= site_url('overheads'); ?>"
               style="margin-top:18px; padding:6px 10px; border-radius:999px; border:1px solid #4b5563; font-size:12px; background:#020617; color:#9ca3af; text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="padding:8px 10px; margin-bottom:12px; border-radius:6px; background:#022c22; border:1px solid #22c55e; color:#bbf7d0; font-size:12px;">
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:#9ca3af; margin:8px 0 0;">
            Belum ada data overhead.
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Tanggal</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Kategori</th>
                <th style="text-align:left;  padding:6px 8px; border-bottom:1px solid #111827;">Deskripsi</th>
                <th style="text-align:right; padding:6px 8px; border-bottom:1px solid #111827;">Nominal</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['trans_date']); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['category_name'] ?? $row['category'] ?? '-'); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937;">
                        <?= esc($row['description'] ?? ''); ?>
                    </td>
                    <td style="padding:6px 8px; border-bottom:1px solid #1f2937; text-align:right; font-weight:600;">
                        Rp <?= number_format((float) $row['amount'], 0, ',', '.'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="padding:6px 8px; border-top:1px solid #4b5563; font-weight:bold;">
                    TOTAL
                </td>
                <td style="padding:6px 8px; border-top:1px solid #4b5563; text-align:right; font-weight:bold;">
                    Rp <?= number_format((float) $total, 0, ',', '.'); ?>
                </td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
