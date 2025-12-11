<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Audit Log</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Jejak perubahan menu & resep (create/update).
            </p>
        </div>
    </div>

    <form method="get" action="<?= current_url(); ?>"
          style="margin-bottom:12px; display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;">

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="entity_type" style="margin-bottom:2px; color:var(--tr-muted-text);">Entity</label>
            <select name="entity_type" id="entity_type"
                    style="min-width:160px; padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="">-- semua --</option>
                <option value="menu" <?= $entityType === 'menu' ? 'selected' : ''; ?>>Menu</option>
                <option value="recipe" <?= $entityType === 'recipe' ? 'selected' : ''; ?>>Recipe</option>
            </select>
        </div>

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

        <div style="display:flex; gap:6px;">
            <button type="submit"
                    style="margin-top:18px; padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:var(--tr-text); cursor:pointer;">
                Terapkan Filter
            </button>

            <a href="<?= site_url('audit-logs'); ?>"
               style="margin-top:18px; padding:6px 10px; border-radius:999px; border:1px solid var(--tr-muted-text); font-size:12px; background:var(--tr-bg); color:var(--tr-muted-text); text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <?php if (empty($logs)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text);">Belum ada log.</p>
    <?php else: ?>
        <div class="table-scroll-wrap" style="max-height:70vh;">
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                <tr>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Waktu</th>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Entity</th>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Action</th>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Deskripsi</th>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">User</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <?php
                        $actionColor = $log['action'] === 'update' ? 'var(--tr-accent-brown)' : 'var(--tr-primary)';
                    ?>
                    <tr>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?= esc($log['created_at']); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?= esc($log['entity_type']); ?> #<?= esc($log['entity_id'] ?? '-'); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <span style="padding:2px 8px; border-radius:999px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); color:<?= $actionColor; ?>;">
                                <?= esc($log['action']); ?>
                            </span>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); white-space:pre-wrap;">
                            <?= esc($log['description'] ?? ''); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?= esc($log['user_id'] ?? '-'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div style="margin-top:10px; font-size:11px; color:var(--tr-muted-text);">
        Log memuat payload perubahan (JSON) tersimpan di DB; viewer ini hanya menampilkan meta singkat. Untuk detail, cek tabel <code>audit_logs.payload</code>.
    </div>
</div>

<?= $this->endSection() ?>

