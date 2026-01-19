<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Audit Log</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Jejak perubahan data dan aktivitas sistem.
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
                <?php foreach ($entityTypeList ?? [] as $et): ?>
                    <option value="<?= esc($et); ?>" <?= $entityType === $et ? 'selected' : ''; ?>><?= esc(ucfirst(str_replace('_', ' ', $et))); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="action" style="margin-bottom:2px; color:var(--tr-muted-text);">Action</label>
            <select name="action" id="action"
                    style="min-width:120px; padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="">-- semua --</option>
                <?php foreach ($actionList ?? [] as $act): ?>
                    <option value="<?= esc($act); ?>" <?= ($action ?? '') === $act ? 'selected' : ''; ?>><?= esc($act); ?></option>
                <?php endforeach; ?>
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
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter entity/action/desc/user:</div>
            <input type="text" id="audit-filter" placeholder="Cari log..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:220px;">
        </div>
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
                <tbody id="audit-table-body">
                <?php foreach ($logs as $log): ?>
                    <?php
                        $act = $log['action'] ?? '';
                        $actionColor = match($act) {
                            'create' => 'var(--tr-primary)',
                            'update' => 'var(--tr-accent-brown)',
                            'delete', 'void' => '#c0392b',
                            'login_success', 'logout', 'reset_success' => '#27ae60',
                            'login_fail', 'reset_fail', 'reset_invalid', 'forgot_unknown' => '#e74c3c',
                            default => 'var(--tr-muted-text)',
                        };
                        $userName = $log['user_name'] ?? null;
                        $userId = $log['user_id'] ?? null;
                        $userDisplay = $userName ?: ($userId ? 'ID:' . $userId : '-');
                    ?>
                    <tr data-entity="<?= esc(strtolower($log['entity_type'] ?? '')); ?>" data-action="<?= esc(strtolower($act)); ?>" data-desc="<?= esc(strtolower($log['description'] ?? '')); ?>" data-user="<?= esc(strtolower($userDisplay)); ?>">
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?= esc($log['created_at']); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?= esc($log['entity_type']); ?><?php if (isset($log['entity_id']) && $log['entity_id']): ?> #<?= esc($log['entity_id']); ?><?php endif; ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <span style="padding:2px 8px; border-radius:999px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); color:<?= $actionColor; ?>;">
                                <?= esc($act); ?>
                            </span>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); white-space:pre-wrap;">
                            <?= esc($log['description'] ?? ''); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?= esc($userDisplay); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr id="audit-noresult" style="display:none;">
                    <td colspan="5" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
                </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div style="margin-top:10px; font-size:11px; color:var(--tr-muted-text);">
        Log memuat payload perubahan (JSON) tersimpan di DB; viewer ini hanya menampilkan meta singkat. Untuk detail, cek tabel <code>audit_logs.payload</code>.
    </div>
</div>

<script>
    (function() {
        function init() {
            if (!window.App || !App.setupFilter) {
                return setTimeout(init, 50);
            }
            App.setupFilter({
                input: '#audit-filter',
                rows: document.querySelectorAll('#audit-table-body tr:not(#audit-noresult)'),
                noResult: '#audit-noresult',
                fields: ['entity','action','desc','user'],
                debounce: 200
            });
        }
        document.addEventListener('DOMContentLoaded', init);
    })();
</script>

<?= $this->endSection() ?>
