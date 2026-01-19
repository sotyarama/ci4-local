<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
/**
 * Logs Index View
 *
 * User-facing activity log viewer.
 * Uses tr-* design system per docs/ui-migration.md.
 * No JS hooks that conflict with docs/js-hooks.md.
 */

// Helper function for action badge color
$getActionColor = static function (string $action): string {
    return match ($action) {
        'create'                        => 'var(--tr-primary)',
        'update', 'bulk_save'           => 'var(--tr-accent-brown)',
        'delete', 'void'                => '#c0392b',
        'login_success', 'logout', 'reset_success' => '#27ae60',
        'login_fail', 'reset_fail', 'reset_invalid', 'forgot_unknown', 'forgot_email_fail' => '#e74c3c',
        'toggle', 'forgot_throttled', 'forgot_email_sent' => '#8e44ad',
        default                         => 'var(--tr-muted-text)',
    };
};

// Helper function for entity type icon/emoji
$getEntityIcon = static function (string $entityType): string {
    return match ($entityType) {
        'auth'              => '🔐',
        'sale'              => '💰',
        'purchase'          => '📦',
        'customer'          => '👤',
        'menu'              => '🍽️',
        'menu_options'      => '⚙️',
        'recipe'            => '📝',
        'overhead'          => '💸',
        'overhead_category' => '📂',
        'payroll'           => '💵',
        'user'              => '👥',
        default             => '📋',
    };
};
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
        <div>
            <h2 style="margin:0; font-size:18px;">Activity Logs</h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                Riwayat aktivitas sistem: transaksi, perubahan data, dan keamanan.
            </p>
        </div>
    </div>

    <!-- Filters -->
    <form method="get" action="<?= current_url(); ?>" id="logs-filter-form"
          style="margin-bottom:16px; display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; padding:12px; background:var(--tr-secondary-beige); border-radius:8px;">

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="logs-date-from" style="margin-bottom:3px; color:var(--tr-muted-text); font-weight:500;">Dari Tanggal</label>
            <input type="date" name="date_from" id="logs-date-from"
                   value="<?= esc($dateFrom ?? ''); ?>"
                   style="padding:6px 10px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="logs-date-to" style="margin-bottom:3px; color:var(--tr-muted-text); font-weight:500;">Sampai Tanggal</label>
            <input type="date" name="date_to" id="logs-date-to"
                   value="<?= esc($dateTo ?? ''); ?>"
                   style="padding:6px 10px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="logs-entity-type" style="margin-bottom:3px; color:var(--tr-muted-text); font-weight:500;">Tipe</label>
            <select name="entity_type" id="logs-entity-type"
                    style="min-width:140px; padding:6px 10px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="">Semua Tipe</option>
                <?php foreach ($entityTypeList ?? [] as $et): ?>
                    <?php $label = $entityTypeLabels[$et] ?? ucfirst(str_replace('_', ' ', $et)); ?>
                    <option value="<?= esc($et); ?>" <?= ($entityType ?? '') === $et ? 'selected' : ''; ?>><?= esc($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="logs-action" style="margin-bottom:3px; color:var(--tr-muted-text); font-weight:500;">Action</label>
            <select name="action" id="logs-action"
                    style="min-width:130px; padding:6px 10px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                <option value="">Semua Action</option>
                <?php foreach ($actionList ?? [] as $act): ?>
                    <?php $label = $actionLabels[$act] ?? ucfirst(str_replace('_', ' ', $act)); ?>
                    <option value="<?= esc($act); ?>" <?= ($action ?? '') === $act ? 'selected' : ''; ?>><?= esc($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; font-size:12px;">
            <label for="logs-username" style="margin-bottom:3px; color:var(--tr-muted-text); font-weight:500;">Username</label>
            <input type="text" name="username" id="logs-username"
                   value="<?= esc($username ?? ''); ?>"
                   placeholder="Cari user..."
                   style="min-width:120px; padding:6px 10px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
        </div>

        <div style="display:flex; gap:6px; align-items:flex-end;">
            <button type="submit"
                    style="padding:7px 14px; border-radius:6px; border:none; font-size:12px; font-weight:500; background:var(--tr-primary); color:var(--tr-text); cursor:pointer;">
                Filter
            </button>
            <a href="<?= site_url('logs'); ?>"
               style="padding:7px 14px; border-radius:6px; border:1px solid var(--tr-border); font-size:12px; background:var(--tr-bg); color:var(--tr-muted-text); text-decoration:none;">
                Reset
            </a>
        </div>
    </form>

    <!-- Results -->
    <?php if (empty($logs)): ?>
        <div style="padding:24px; text-align:center; color:var(--tr-muted-text);">
            <p style="margin:0; font-size:14px;">Tidak ada aktivitas ditemukan.</p>
            <p style="margin:6px 0 0; font-size:12px;">Coba ubah filter atau periksa apakah sudah ada data di sistem.</p>
        </div>
    <?php else: ?>
        <div style="margin-bottom:8px; font-size:12px; color:var(--tr-muted-text);">
            Menampilkan <?= count($logs); ?> aktivitas terbaru
        </div>

        <div class="table-scroll-wrap" style="max-height:65vh; overflow-y:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead style="position:sticky; top:0; background:var(--tr-bg); z-index:1;">
                    <tr>
                        <th style="text-align:left; padding:8px 10px; border-bottom:2px solid var(--tr-border); font-weight:600; color:var(--tr-text);">Waktu</th>
                        <th style="text-align:left; padding:8px 10px; border-bottom:2px solid var(--tr-border); font-weight:600; color:var(--tr-text);">User</th>
                        <th style="text-align:left; padding:8px 10px; border-bottom:2px solid var(--tr-border); font-weight:600; color:var(--tr-text);">Action</th>
                        <th style="text-align:left; padding:8px 10px; border-bottom:2px solid var(--tr-border); font-weight:600; color:var(--tr-text);">Tipe</th>
                        <th style="text-align:left; padding:8px 10px; border-bottom:2px solid var(--tr-border); font-weight:600; color:var(--tr-text);">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <?php
                            $act = $log['action'] ?? '';
                            $entityType = $log['entity_type'] ?? '';
                            $actionColor = $getActionColor($act);
                            $entityIcon = $getEntityIcon($entityType);
                            $userName = $log['user_name'] ?? null;
                            $userId = $log['user_id'] ?? null;
                            $userDisplay = $userName ?: ($userId ? 'User #' . $userId : 'System');
                            $entityLabel = $entityTypeLabels[$entityType] ?? ucfirst(str_replace('_', ' ', $entityType));
                            $actionLabel = $actionLabels[$act] ?? ucfirst(str_replace('_', ' ', $act));
                            $entityId = $log['entity_id'] ?? null;
                        ?>
                        <tr style="border-bottom:1px solid var(--tr-border);">
                            <td style="padding:10px; white-space:nowrap; color:var(--tr-muted-text);">
                                <?php
                                    $dt = $log['created_at'] ?? '';
                                    if ($dt) {
                                        $ts = strtotime($dt);
                                        $isToday = date('Y-m-d', $ts) === date('Y-m-d');
                                        echo $isToday
                                            ? '<span style="color:var(--tr-primary); font-weight:500;">Hari ini</span> ' . date('H:i', $ts)
                                            : date('d M Y H:i', $ts);
                                    }
                                ?>
                            </td>
                            <td style="padding:10px;">
                                <span style="font-weight:500; color:var(--tr-text);"><?= esc($userDisplay); ?></span>
                            </td>
                            <td style="padding:10px;">
                                <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:500; background:color-mix(in srgb, <?= $actionColor; ?> 15%, transparent); color:<?= $actionColor; ?>; border:1px solid color-mix(in srgb, <?= $actionColor; ?> 30%, transparent);">
                                    <?= esc($actionLabel); ?>
                                </span>
                            </td>
                            <td style="padding:10px;">
                                <span style="display:inline-flex; align-items:center; gap:4px;">
                                    <span style="font-size:14px;"><?= $entityIcon; ?></span>
                                    <span style="color:var(--tr-text);"><?= esc($entityLabel); ?></span>
                                    <?php if ($entityId): ?>
                                        <span style="color:var(--tr-muted-text); font-size:11px;">#<?= esc($entityId); ?></span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td style="padding:10px; color:var(--tr-text); max-width:300px;">
                                <?= esc($log['description'] ?? '-'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div style="margin-top:12px; padding-top:10px; border-top:1px solid var(--tr-border); font-size:11px; color:var(--tr-muted-text);">
        <strong>Catatan:</strong> Halaman ini menampilkan ringkasan aktivitas. Untuk detail teknis (payload JSON), gunakan menu <em>Audit Log</em> di bagian Master.
    </div>
</div>

<?= $this->endSection() ?>
