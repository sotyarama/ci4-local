<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Cafe POS'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $assetVer = time(); ?>
    <link rel="stylesheet" href="<?= base_url('css/theme-temurasa.css') . '?v=' . $assetVer; ?>">

    <style>
        :root {
            --sidebar-width: 220px;
            --topbar-height: 56px;
            --footer-height: 36px;
        }
        body {
            margin: 0;
            background: var(--tr-bg);
            color: var(--tr-text);
        }
        .layout {
            display: flex;
            min-height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: var(--sidebar-width);
            background: var(--tr-secondary-beige);
            border-right: 1px solid var(--tr-border);
            padding: 0 14px 16px 14px;
            height: 100vh;
            position: sticky;
            top: 0;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            color: var(--tr-text);
        }
        .sidebar-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            padding: 16px 0 0 0;
            color: var(--tr-primary);
        }
        .sidebar-sub {
            font-size: 11px;
            color: var(--tr-muted-text);
            margin-bottom: 16px;
        }
        .sidebar-scroll {
            overflow-y: auto;
            flex: 1;
            padding-top: 8px;
        }
        .nav-section-title {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--tr-muted-text);
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
            color: var(--tr-text);
            text-decoration: none;
            margin-bottom: 3px;
            border: 1px solid transparent;
            transition: background 0.15s ease, border-color 0.15s ease;
        }
        .nav-link:hover {
            background: var(--tr-secondary-green);
            border-color: var(--tr-border);
        }
        .nav-link.small {
            font-size: 12px;
            color: var(--tr-muted-text);
        }
        .nav-link.active {
            background: var(--tr-secondary-beige);
            border: 1px solid var(--tr-primary);
            color: var(--tr-primary);
            font-weight: 600;
            box-shadow: 0 6px 18px rgba(122, 154, 108, 0.28);
        }
        .nav-group {
            margin-bottom: 4px;
        }
        .collapse-arrow {
            font-size: 12px;
            transition: transform 0.2s ease;
            color: var(--tr-muted-text);
        }
        .collapsed .collapse-arrow {
            transform: rotate(-90deg);
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .topbar {
            padding: 10px 18px;
            border-bottom: 1px solid var(--tr-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--tr-primary);
            position: fixed;
            left: var(--sidebar-width);
            right: 0;
            top: 0;
            height: var(--topbar-height);
            z-index: 10;
            color: #fff;
        }
        .topbar-title {
            font-size: 16px;
            font-weight: 500;
            color: #fff;
        }
        .topbar-subtitle {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.82);
        }
        .topbar-right {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.82);
        }
        .topbar-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }
        .dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--tr-accent-brown);
            margin-right: 6px;
        }

        .content {
            padding: 18px 20px 32px;
            overflow-y: auto;
            flex: 1;
            height: calc(100vh - var(--topbar-height) - var(--footer-height));
            margin-top: var(--topbar-height);
            margin-bottom: var(--footer-height);
            width: 100%;
        }

        /* Card default agar view lain tinggal pakai */
        .card {
            border-radius: 16px;
            background: var(--tr-surface);
            border: 1px solid var(--tr-border);
            box-shadow: var(--tr-shadow);
        }
        .disabled-link {
            opacity: 0.35;
            pointer-events: none;
            cursor: not-allowed;
        }
        .table-scroll-wrap {
            border: 1px solid var(--tr-border);
            border-radius: 10px;
            background: #fff;
        }
        /* Scrollbar styling */
        .sidebar::-webkit-scrollbar,
        .table-scroll-wrap::-webkit-scrollbar,
        .content::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .sidebar::-webkit-scrollbar-track,
        .table-scroll-wrap::-webkit-scrollbar-track,
        .content::-webkit-scrollbar-track {
            background: var(--tr-secondary-beige);
            border-radius: 10px;
        }
        .sidebar::-webkit-scrollbar-thumb,
        .table-scroll-wrap::-webkit-scrollbar-thumb,
        .content::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--tr-primary), var(--tr-secondary-green));
            border-radius: 10px;
            border: 1px solid var(--tr-secondary-beige);
        }
        .sidebar {
            scrollbar-color: var(--tr-primary) var(--tr-secondary-beige);
            scrollbar-width: thin;
        }
        .table-scroll-wrap,
        .content {
            scrollbar-color: var(--tr-secondary-green) var(--tr-secondary-beige);
            scrollbar-width: thin;
        }
        .footer {
            position: fixed;
            left: var(--sidebar-width);
            right: 0;
            bottom: 0;
            height: var(--footer-height);
            border-top: 1px solid var(--tr-border);
            background: var(--tr-secondary-beige);
            color: var(--tr-muted-text);
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 16px;
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
    // normalisasi: buang prefix index.php/ jika ada
    if (str_starts_with($currentPath, 'index.php/')) {
        $currentPath = substr($currentPath, strlen('index.php/'));
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
            if ($p !== '' && ($currentPath === $p || str_starts_with($currentPath, $p . '/'))) {
                return true;
            }
        }
        return false;
    };
?>
<?php if (! empty($flashError)): ?>
    <div id="flash-error" style="position:fixed; top:18px; left:50%; transform:translateX(-50%); z-index:9999; background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); color:var(--tr-text); padding:14px 18px; border-radius:12px; box-shadow:0 16px 40px rgba(58,58,58,0.24); font-size:13px; max-width:520px; width:calc(100% - 32px); text-align:center;">
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
<script>
    // Auto wrap table di dalam .card dengan container scroll, kecuali sudah punya pembungkus scroll
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.card table').forEach(function(tbl) {
            if (tbl.closest('.table-scroll-wrap')) {
                return;
            }
            var wrap = document.createElement('div');
            wrap.className = 'table-scroll-wrap';
            tbl.parentNode.insertBefore(wrap, tbl);
            wrap.appendChild(tbl);
        });
    });
</script>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-title">Cafe POS</div>
        <div class="sidebar-sub">CodeIgniter 4 Dev</div>
        <div class="sidebar-scroll">
            <div class="nav-section-title collapsible" data-target="main">
                Main <span class="collapse-arrow">▾</span>
            </div>
            <div class="nav-group" id="nav-main">
                <a href="<?= site_url('/') ?>" class="nav-link <?= $menuAllowed('dashboard') ? '' : 'disabled-link'; ?> <?= $isActive(['','dashboard']) ? 'active' : ''; ?>">Dashboard</a>
                <a href="<?= site_url('pos/touch') ?>" class="nav-link small <?= $menuAllowed('transactions') ? '' : 'disabled-link'; ?> <?= $isActive(['pos/touch']) ? 'active' : ''; ?>">POS UI (Touch)</a>
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
                <a href="<?= site_url('audit-logs') ?>" class="nav-link small <?= $menuAllowed('master') ? '' : 'disabled-link'; ?> <?= $isActive(['audit-logs']) ? 'active' : ''; ?>">Audit Log</a>
            </div>

            <div class="nav-section-title collapsible" data-target="transactions">
                Transaksi <span class="collapse-arrow">▾</span>
            </div>
            <div class="nav-group" id="nav-transactions">
                <a href="<?= site_url('purchases') ?>" class="nav-link small <?= $menuAllowed('transactions') ? '' : 'disabled-link'; ?> <?= $isActive(['purchases']) ? 'active' : ''; ?>">Pembelian Bahan</a>
                <a href="<?= site_url('transactions/sales') ?>" class="nav-link small <?= $menuAllowed('transactions') ? '' : 'disabled-link'; ?> <?= $isActive(['transactions/sales']) ? 'active' : ''; ?>">POS Penjualan</a>
            </div>

            <div class="nav-section-title collapsible" data-target="inventory">
                Inventory <span class="collapse-arrow">▾</span>
            </div>
            <div class="nav-group" id="nav-inventory">
                <a href="<?= site_url('inventory/stock-movements') ?>" class="nav-link small <?= $menuAllowed('inventory') ? '' : 'disabled-link'; ?> <?= $isActive(['inventory/stock-movements']) ? 'active' : ''; ?>">Riwayat Stok (IN/OUT)</a>
                <a href="<?= site_url('inventory/stock-card') ?>" class="nav-link small <?= $menuAllowed('inventory') ? '' : 'disabled-link'; ?> <?= $isActive(['inventory/stock-card']) ? 'active' : ''; ?>">Kartu Stok per Bahan</a>
                <a href="<?= site_url('inventory/stock-adjustments') ?>" class="nav-link small <?= $menuAllowed('inventory') ? '' : 'disabled-link'; ?> <?= $isActive(['inventory/stock-adjustments']) ? 'active' : ''; ?>">Stock Adjustment</a>
                <a href="<?= site_url('inventory/stock-opname') ?>" class="nav-link small <?= $menuAllowed('inventory') ? '' : 'disabled-link'; ?> <?= $isActive(['inventory/stock-opname']) ? 'active' : ''; ?>">Stock & Selisih Fisik</a>
            </div>

            <div class="nav-section-title collapsible" data-target="reports">
                Laporan <span class="collapse-arrow">▾</span>
            </div>
            <div class="nav-group" id="nav-reports">
                <a href="<?= site_url('reports/sales/time') ?>" class="nav-link small <?= $menuAllowed('reports') ? '' : 'disabled-link'; ?> <?= $isActive(['reports/sales/time']) ? 'active' : ''; ?>">Penjualan by Time</a>
                <a href="<?= site_url('reports/sales/menu') ?>" class="nav-link small <?= $menuAllowed('reports') ? '' : 'disabled-link'; ?> <?= $isActive(['reports/sales/menu']) ? 'active' : ''; ?>">Penjualan per Menu</a>
                <a href="<?= site_url('reports/sales/category') ?>" class="nav-link small <?= $menuAllowed('reports') ? '' : 'disabled-link'; ?> <?= $isActive(['reports/sales/category']) ? 'active' : ''; ?>">Penjualan per Kategori</a>
                <a href="<?= site_url('reports/purchases/supplier') ?>" class="nav-link small <?= $menuAllowed('reports') ? '' : 'disabled-link'; ?> <?= $isActive(['reports/purchases/supplier']) ? 'active' : ''; ?>">Pembelian per Supplier</a>
                <a href="<?= site_url('reports/purchases/material') ?>" class="nav-link small <?= $menuAllowed('reports') ? '' : 'disabled-link'; ?> <?= $isActive(['reports/purchases/material']) ? 'active' : ''; ?>">Pembelian per Bahan</a>
                <a href="<?= site_url('reports/stock/variance') ?>" class="nav-link small <?= $menuAllowed('reports') ? '' : 'disabled-link'; ?> <?= $isActive(['reports/stock/variance']) ? 'active' : ''; ?>">Stok & Selisih</a>
            </div>

            <div class="nav-section-title collapsible" data-target="overhead">
                Overhead <span class="collapse-arrow">▾</span>
            </div>
            <div class="nav-group" id="nav-overhead">
                <?php $isOverheadPayroll = str_starts_with($currentPath, 'overheads/payroll'); ?>
                <a href="<?= site_url('overheads') ?>" class="nav-link small <?= $menuAllowed('overhead') ? '' : 'disabled-link'; ?> <?= (!$isOverheadPayroll && $isActive(['overheads'])) ? 'active' : ''; ?>">Biaya Overhead</a>
                <a href="<?= site_url('overhead-categories') ?>" class="nav-link small <?= $menuAllowed('overhead') ? '' : 'disabled-link'; ?> <?= $isActive(['overhead-categories']) ? 'active' : ''; ?>">Kategori Overhead</a>
                <a href="<?= site_url('overheads/payroll') ?>" class="nav-link small <?= $menuAllowed('overhead') ? '' : 'disabled-link'; ?> <?= $isOverheadPayroll ? 'active' : ''; ?>">Overhead (Payroll)</a>
            </div>

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
                <a href="<?= site_url('logout'); ?>" style="font-size:12px; color:rgba(255,255,255,0.9); text-decoration:none;">
                    Logout
                </a>
            </div>
        </header>

        <main class="content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>
<footer class="footer">
    <span>POS Cafe System</span>
    <span>CodeIgniter 4</span>
</footer>

<meta name="csrf-name" content="<?= csrf_token(); ?>">
<meta name="csrf-token" content="<?= csrf_hash(); ?>">
<script src="<?= base_url('js/app.js') . '?v=' . $assetVer; ?>"></script>
</body>
</html>
