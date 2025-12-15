<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;"><?= esc($title); ?></h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                <?= esc($subtitle); ?>
            </p>
        </div>
    </div>

    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_from" style="margin-bottom:2px; color:var(--tr-muted-text);">Dari Tanggal</label>
            <input type="date" name="date_from" id="date_from"
                   value="<?= esc($dateFrom); ?>"
                   style="padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:12px; min-width:150px;">
        </div>
        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="date_to" style="margin-bottom:2px; color:var(--tr-muted-text);">Sampai Tanggal</label>
            <input type="date" name="date_to" id="date_to"
                   value="<?= esc($dateTo); ?>"
                   style="padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:12px; min-width:150px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="material_id" style="margin-bottom:2px; color:var(--tr-muted-text);">Bahan</label>
            <select name="material_id" id="material_id"
                    style="min-width:180px; padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:12px;">
                <option value="0">Semua bahan</option>
                <?php foreach ($materials as $mat): ?>
                    <option value="<?= $mat['id']; ?>" <?= ((int)$materialId === (int)$mat['id']) ? 'selected' : ''; ?>>
                        <?= esc($mat['name']); ?> (<?= esc($mat['unit_short'] ?? ''); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="q" style="margin-bottom:2px; color:var(--tr-muted-text);">Cari nama bahan</label>
            <input type="text" name="q" id="q" placeholder="ketik nama..."
                   value="<?= esc($search); ?>"
                   style="padding:6px 8px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color:var(--tr-text); font-size:12px; min-width:180px;">
        </div>

        <div style="display:flex; gap:6px; align-items:flex-end;">
            <button type="submit"
                    style="padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:#fff; cursor:pointer;">
                Terapkan Filter
            </button>
            <a href="<?= current_url(); ?>" style="font-size:12px; color:var(--tr-muted-text); text-decoration:none;">Reset</a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text);">Tidak ada data untuk filter ini.</p>
    <?php else: ?>
        <div class="table-scroll-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="min-width:180px;">Bahan</th>
                        <th style="width:90px;">Opening</th>
                        <th style="width:90px;">IN</th>
                        <th style="width:90px;">OUT</th>
                        <th style="width:90px;">Saldo Periode</th>
                        <th style="width:90px;">Stok Sistem</th>
                        <th style="width:90px;">Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <?php
                            $var = (float) $row['variance'];
                            $varColor = $var === 0.0 ? 'var(--tr-muted-text)' : ($var > 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)');
                        ?>
                        <tr>
                            <td><?= esc($row['name']); ?> <span class="muted">(<?= esc($row['unit']); ?>)</span></td>
                            <td style="text-align:right;"><?= number_format($row['opening'], 3, ',', '.'); ?></td>
                            <td style="text-align:right;"><?= number_format($row['in'], 3, ',', '.'); ?></td>
                            <td style="text-align:right;"><?= number_format($row['out'], 3, ',', '.'); ?></td>
                            <td style="text-align:right;"><?= number_format($row['closing'], 3, ',', '.'); ?></td>
                            <td style="text-align:right;"><?= number_format($row['current'], 3, ',', '.'); ?></td>
                            <td style="text-align:right; color:<?= $varColor; ?>; font-weight:600;">
                                <?= number_format($var, 3, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p style="margin-top:10px; font-size:12px; color:var(--tr-muted-text);">
            Selisih = stok sistem (sekarang) - saldo akhir periode. Jika periode berakhir hari ini, selisih ≠ 0 menandakan drift yang perlu dicek.
        </p>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
