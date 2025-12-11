<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Laporan Pembelian per Supplier</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Ringkasan jumlah dan total pembelian per pemasok pada periode tertentu.
            </p>
        </div>
    </div>

    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">
        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_from" style="margin-bottom:2px; color:var(--tr-muted-text);">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from"
                   value="<?= esc($dateFrom); ?>"
                   style="padding:6px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>
        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_to" style="margin-bottom:2px; color:var(--tr-muted-text);">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to"
                   value="<?= esc($dateTo); ?>"
                   style="padding:6px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>
        <div style="display:flex; flex-direction:column; font-size:12px; min-width:220px;">
            <label for="supplier_id" style="margin-bottom:2px; color:var(--tr-muted-text);">Supplier (opsional)</label>
            <select name="supplier_id" id="supplier_id"
                    style="padding:6px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="">-- Semua Supplier --</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['id']; ?>" <?= (string)$supplierId === (string)$s['id'] ? 'selected' : ''; ?>>
                        <?= esc($s['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display:flex; gap:6px; align-items:flex-end;">
            <button type="submit"
                    style="padding:7px 12px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:#fff; cursor:pointer;">
                Terapkan Filter
            </button>
            <a href="<?= site_url('reports/purchases/supplier'); ?>"
               style="padding:7px 12px; border-radius:999px; border:1px solid var(--tr-muted-text); font-size:12px; background:var(--tr-bg); color:var(--tr-muted-text); text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text); margin:8px 0 0;">
            Belum ada data pembelian pada periode/filter ini.
        </p>
    <?php else: ?>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
            <tr>
                <th style="text-align:left; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Supplier</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Jumlah PO</th>
                <th style="text-align:right; padding:10px 8px; border-bottom:1px solid var(--tr-border);">Total Pembelian</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border);">
                        <?= esc($row['supplier_name']); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                        <?= number_format((float) ($row['purchase_count'] ?? 0), 0, ',', '.'); ?>
                    </td>
                    <td style="padding:10px 8px; border-bottom:1px solid var(--tr-border); text-align:right; font-weight:600;">
                        Rp <?= number_format((float) ($row['total_amount'] ?? 0), 0, ',', '.'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" style="padding:10px 8px; border-top:1px solid var(--tr-border); font-weight:700; text-align:right;">
                    GRAND TOTAL
                </td>
                <td style="padding:10px 8px; border-top:1px solid var(--tr-border); font-weight:700; text-align:right;">
                    Rp <?= number_format($grandTotal, 0, ',', '.'); ?>
                </td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
