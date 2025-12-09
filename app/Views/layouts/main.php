<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Cafe POS'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #020617;
            color: #e5e7eb;
        }
        .layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 220px;
            background: #020617;
            border-right: 1px solid #111827;
            padding: 16px 14px;
            overflow-y: auto;
        }
        .sidebar-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .sidebar-sub {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        .nav-section-title {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: .08em;
            margin: 16px 0 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }
        .nav-link {
            display: block;
            padding: 7px 8px;
            border-radius: 8px;
            font-size: 13px;
            color: #e5e7eb;
            text-decoration: none;
            margin-bottom: 3px;
            border: 1px solid transparent;
            transition: background 0.15s ease, border-color 0.15s ease;
        }
        .nav-link:hover {
            background: #0f172a;
        }
        .nav-link.small {
            font-size: 12px;
            color: #9ca3af;
        }
        .nav-link.active {
            background: #f9fafb;
            border: 1px solid #3b82f6;
            color: #2563eb;
            font-weight: 600;
            box-shadow: 0 6px 18px rgba(59,130,246,0.25);
        }
        .nav-group {
            margin-bottom: 4px;
        }
        .collapse-arrow {
            font-size: 12px;
            transition: transform 0.2s ease;
            color: #6b7280;
        }
        .collapsed .collapse-arrow {
            transform: rotate(-90deg);
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            padding: 10px 18px;
            border-bottom: 1px solid #111827;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #020617;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .topbar-title {
            font-size: 16px;
            font-weight: 500;
        }
        .topbar-subtitle {
            font-size: 12px;
            color: #9ca3af;
        }
        .topbar-right {
            font-size: 12px;
            color: #9ca3af;
        }
        .topbar-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid #1f2937;
        }
        .dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #22c55e;
            margin-right: 6px;
        }

        .content {
            padding: 18px 20px 32px;
        }

        /* Card default agar view lain tinggal pakai */
        .card {
            background: #020617;
            border-radius: 16px;
            padding: 18px 20px;
            border: 1px solid #111827;
            box-shadow: 0 20px 40px rgba(0,0,0,0.45);
        }
        .disabled-link {
            opacity: 0.35;
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<?php
    $role = strtolower((string) (session('role') ?? session('role_name') ?? ''));
    $uri = service('uri');
    $currentPath = strtolower(trim($uri->getPath(), '/'));
    // fallback jika basepath/public mengganggu
    $reqUri = strtolower(trim(parse_url(current_url(false), PHP_URL_PATH) ?? '', '/'));
    if ($reqUri !== '') {
        $currentPath = $reqUri;
    }
    $flashError = session()->getFlashdata('error') ?? null;
    $roleAllow = [
        'owner'   => ['dashboard','master','transactions','inventory','reports','overhead'],
        'staff'   => ['dashboard','master','transactions','inventory','reports','overhead'],
        'auditor' => ['dashboard','master','transactions','inventory','reports','overhead'], // auditor boleh lihat semua tapi tetap read-only via filter
    ];
    $menuAllowed = static function(string $key) use ($role, $roleAllow): bool {
        if ($role === '') {
            return false;
        }
        return in_array($key, $roleAllow[$role] ?? [], true);
    };
    $isActive = static function(array $paths) use ($currentPath): bool {
        foreach ($paths as $p) {
            $p = strtolower(trim($p, '/'));
            if ($p === '' && $currentPath === '') {
                return true;
            }
            if ($p !== '' && (strpos($currentPath, $p) === 0)) {
                return true;
            }
        }
        return false;
    };
?>
<?php if (! empty($flashError)): ?>
    <div id="flash-error" style="position:fixed; top:18px; left:50%; transform:translateX(-50%); z-index:9999; background:#3f1f1f; border:1px solid #b91c1c; color:#fecaca; padding:14px 18px; border-radius:12px; box-shadow:0 16px 40px rgba(0,0,0,0.55); font-size:13px; max-width:520px; width:calc(100% - 32px); text-align:center;">
        <?= esc($flashError); ?>
    </div>
    <script>
        setTimeout(function() {
            var el = document.getElementById('flash-error');
            if (el) {
                el.style.transition = 'opacity 0.6s ease';
                el.style.opacity = '0';
                setTimeout(function(){ el.remove(); }, 700);
            }
        }, 4500);
    </script>
<?php endif; ?>
<script>
    (function() {
        const stateKey = 'sidebarCollapseState';
        const saved = localStorage.getItem(stateKey);
        let collapseState = {};
        if (saved) {
            try { collapseState = JSON.parse(saved); } catch(e) { collapseState = {}; }
        }

        function saveState() {
            localStorage.setItem(stateKey, JSON.stringify(collapseState));
        }

        function toggleSection(section, force) {
            const group = document.getElementById('nav-' + section);
            const title = document.querySelector('.nav-section-title[data-target="' + section + '"]');
            if (!group || !title) return;
            const shouldCollapse = force !== undefined ? force : !(collapseState[section] === true);
            if (shouldCollapse) {
                group.style.display = 'none';
                title.classList.add('collapsed');
                collapseState[section] = true;
            } else {
                group.style.display = '';
                title.classList.remove('collapsed');
                collapseState[section] = false;
            }
            saveState();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.nav-section-title.collapsible').forEach(function(title) {
                const target = title.getAttribute('data-target');
                if (collapseState[target] === true) {
                    toggleSection(target, true);
                }
                title.addEventListener('click', function() {
                    toggleSection(target);
                });
            });
        });
    })();
</script>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-title">Cafe POS</div>
        <div class="sidebar-sub">CodeIgniter 4 Dev</div>

        <div class="nav-section-title collapsible" data-target="main">
            Main <span class="collapse-arrow">▾</span>
        </div>
        <div class="nav-group" id="nav-main">
            <a href="<?= site_url('/') ?>" class="nav-link <?= $menuAllowed('dashboard') ? '' : 'disabled-link'; ?> <?= $isActive(['','dashboard']) ? 'active' : ''; ?>">Dashboard</a>
        </div>

        <div class="nav-section-title collapsible" data-target="master">
            Master <span class="collapse-arrow">▾</span>
        </div>
        <div class="nav-group" id="nav-master">
            <a href="<?= site_url('master/products') ?>" class="nav-link small <?= $menuAllowed('master') ? '' : 'disabled-link'; ?> <?= $isActive(['master/products']) ? 'active' : ''; ?>">Menu / Produk</a>
            <a href="<?= site_url('master/categories') ?>" class="nav-link small <?= $menuAllowed('master') ? '' : 'disabled-link'; ?> <?= $isActive(['master/categories']) ? 'active' : ''; ?>">Kategori Menu</a>
            <a href="<?= site_url('master/raw-materials') ?>" class="nav-link small <?= $menuAllowed('master') ? '' : 'disabled-link'; ?> <?= $isActive(['master/raw-materials']) ? 'active' : ''; ?>">Bahan Baku</a>
            <a href="<?= site_url('master/suppliers') ?>" class="nav-link small <?= $menuAllowed('master') ? '' : 'disabled-link'; ?> <?= $isActive(['master/suppliers']) ? 'active' : ''; ?>">Supplier</a>
            <a href="<?= site_url('master/recipes') ?>" class="nav-link small <?= $menuAllowed('master') ? '' : 'disabled-link'; ?> <?= $isActive(['master/recipes']) ? 'active' : ''; ?>">Resep</a>
            <a href="#" class="nav-link small disabled-link" title="Planned">Audit Log (planned)</a>
        </div>

        <div class="nav-section-title collapsible" data-target="transactions">
            Transaksi <span class="collapse-arrow">▾</span>
        </div>
        <div class="nav-group" id="nav-transactions">
            <a href="<?= site_url('purchases') ?>" class="nav-link small <?= $menuAllowed('transactions') ? '' : 'disabled-link'; ?> <?= $isActive(['purchases']) ? 'active' : ''; ?>">Pembelian Bahan</a>
            <a href="<?= site_url('transactions/sales') ?>" class="nav-link small <?= $menuAllowed('transactions') ? '' : 'disabled-link'; ?> <?= $isActive(['transactions/sales']) ? 'active' : ''; ?>">POS Penjualan</a>
            <a href="#" class="nav-link small disabled-link" title="Planned">Retur Penjualan (planned)</a>
        </div>

        <div class="nav-section-title collapsible" data-target="inventory">
            Inventory <span class="collapse-arrow">▾</span>
        </div>
        <div class="nav-group" id="nav-inventory">
            <a href="<?= site_url('inventory/stock-movements') ?>" class="nav-link small <?= $menuAllowed('inventory') ? '' : 'disabled-link'; ?> <?= $isActive(['inventory/stock-movements']) ? 'active' : ''; ?>">Riwayat Stok (IN/OUT)</a>
            <a href="<?= site_url('inventory/stock-card') ?>" class="nav-link small <?= $menuAllowed('inventory') ? '' : 'disabled-link'; ?> <?= $isActive(['inventory/stock-card']) ? 'active' : ''; ?>">Kartu Stok per Bahan</a>
            <a href="#" class="nav-link small disabled-link" title="Planned">Stock Adjustment (planned)</a>
            <a href="#" class="nav-link small disabled-link" title="Planned">Stock & Selisih Fisik (planned)</a>
        </div>

        <div class="nav-section-title collapsible" data-target="reports">
            Laporan <span class="collapse-arrow">▾</span>
        </div>
        <div class="nav-group" id="nav-reports">
            <a href="<?= site_url('reports/sales/daily') ?>" class="nav-link small <?= $menuAllowed('reports') ? '' : 'disabled-link'; ?> <?= $isActive(['reports/sales/daily']) ? 'active' : ''; ?>">Penjualan Harian</a>
            <a href="<?= site_url('reports/sales/menu') ?>" class="nav-link small <?= $menuAllowed('reports') ? '' : 'disabled-link'; ?> <?= $isActive(['reports/sales/menu']) ? 'active' : ''; ?>">Penjualan per Menu</a>
            <a href="#" class="nav-link small disabled-link" title="Planned">Penjualan Bulanan (planned)</a>
            <a href="#" class="nav-link small disabled-link" title="Planned">Margin per Kategori (planned)</a>
            <a href="#" class="nav-link small disabled-link" title="Planned">Pembelian per Supplier (planned)</a>
            <a href="#" class="nav-link small disabled-link" title="Planned">Stok & Selisih (planned)</a>
            <a href="#" class="nav-link small disabled-link" title="Planned">Audit Log (planned)</a>
        </div>

        <div class="nav-section-title collapsible" data-target="overhead">
            Overhead <span class="collapse-arrow">▾</span>
        </div>
        <div class="nav-group" id="nav-overhead">
            <a href="<?= site_url('overheads') ?>" class="nav-link small <?= $menuAllowed('overhead') ? '' : 'disabled-link'; ?> <?= $isActive(['overheads']) ? 'active' : ''; ?>">Biaya Overhead</a>
            <a href="<?= site_url('overhead-categories') ?>" class="nav-link small <?= $menuAllowed('overhead') ? '' : 'disabled-link'; ?> <?= $isActive(['overhead-categories']) ? 'active' : ''; ?>">Kategori Overhead</a>
            <a href="#" class="nav-link small disabled-link" title="Planned">Overhead (Payroll) planned</a>
        </div>

        <div class="nav-section-title collapsible" data-target="posui">
            POS UI (Phase 2) <span class="collapse-arrow">▾</span>
        </div>
        <div class="nav-group" id="nav-posui">
            <a href="#" class="nav-link small disabled-link" title="Planned">Touchscreen POS (planned)</a>
        </div>
    </aside>


    <div class="main">
        <header class="topbar">
            <div>
                <div class="topbar-title"><?= esc($title ?? ''); ?></div>
                <?php if (!empty($subtitle)): ?>
                    <div class="topbar-subtitle"><?= esc($subtitle); ?></div>
                <?php endif; ?>
            </div>
            <div class="topbar-right">
                <span class="topbar-pill" style="margin-right: 8px;">
                    <?= esc(session('full_name') ?? session('username') ?? ''); ?>
                </span>
                <a href="<?= site_url('logout'); ?>" style="font-size:12px; color:#9ca3af; text-decoration:none;">
                    Logout
                </a>
            </div>
        </header>

        <main class="content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>
</body>
</html>
