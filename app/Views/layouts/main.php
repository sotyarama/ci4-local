<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Cafe POS'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        (function() {
            try {
                var saved = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', saved);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <?php
    /**
     * Layout: main
     *
     * Tanggung jawab:
     * - Skeleton halaman (sidebar + topbar + content + footer)
     * - Styling dasar + load theme
     * - Navigasi sidebar (active state + role gating)
     * - Flash error toast
     */
    $assetVer = time(); // sengaja non-cache agar mudah dev (tanpa ubah behavior)
    ?>

    <link rel="stylesheet" href="<?= base_url('css/theme-temurasa.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/layout.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
</head>

<body>
    <?php
    // ======================================================
    // Prepare (role, current path, menu permission, active state)
    // ======================================================
    $role = strtolower((string) (session('role') ?? session('role_name') ?? ''));

    // Normalisasi current path:
    // - pakai uri service
    // - fallback: parse current_url jika basepath/public mengganggu
    // - buang prefix index.php/ jika ada
    $uri = service('uri');
    $currentPath = strtolower(trim($uri->getPath(), '/'));

    $reqUri = strtolower(trim(parse_url(current_url(false), PHP_URL_PATH) ?? '', '/'));
    if ($reqUri !== '') {
        $currentPath = $reqUri;
    }
    if (str_starts_with($currentPath, 'index.php/')) {
        $currentPath = substr($currentPath, strlen('index.php/'));
    }

    // Flash message (error)
    $flashError = session()->getFlashdata('error') ?? null;

    // Akses menu berdasarkan role (read-only auditor diselesaikan di filter)
    $roleAllow = [
        'owner'   => ['dashboard', 'master', 'transactions', 'inventory', 'reports', 'overhead'],
        'staff'   => ['dashboard', 'master', 'transactions', 'inventory', 'reports', 'overhead'],
        'auditor' => ['dashboard', 'master', 'transactions', 'inventory', 'reports', 'overhead'],
    ];

    $menuAllowed = static function (string $key) use ($role, $roleAllow): bool {
        if ($role === '') {
            return false;
        }
        return in_array($key, $roleAllow[$role] ?? [], true);
    };

    $canManageUsers = in_array($role, ['owner', 'auditor'], true);

    // Active state helper (match exact path or prefix)
    $isActive = static function (array $paths) use ($currentPath): bool {
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

    // Link renderer untuk sidebar (mengurangi duplikasi class)
    $navLink = static function (
        string $href,
        string $label,
        bool $allowed,
        bool $active,
        bool $small = true
    ): string {
        $classes = ['nav-link'];
        if ($small) $classes[] = 'small';
        if (! $allowed) $classes[] = 'disabled-link';
        if ($active) $classes[] = 'active';

        $classAttr = implode(' ', $classes);

        return '<a href="' . site_url($href) . '" class="' . esc($classAttr) . '">' . esc($label) . '</a>';
    };
    ?>

    <!-- ======================================================
     Flash Error Toast
======================================================= -->
    <?php if (! empty($flashError)): ?>
        <div id="flash-error"
            style="position:fixed; top:18px; left:50%; transform:translateX(-50%); z-index:9999;
                background:var(--tr-secondary-beige); border:1px solid var(--tr-accent-brown); color:var(--tr-text);
                padding:14px 18px; border-radius:12px; box-shadow:0 16px 40px rgba(58,58,58,0.24);
                font-size:13px; max-width:520px; width:calc(100% - 32px); text-align:center;">
            <?= esc($flashError); ?>
        </div>
        <script>
            // Fade-out toast setelah 4.5 detik (behavior dipertahankan)
            setTimeout(function() {
                var el = document.getElementById('flash-error');
                if (el) {
                    el.style.transition = 'opacity 0.6s ease';
                    el.style.opacity = '0';
                    setTimeout(function() {
                        el.remove();
                    }, 700);
                }
            }, 4500);
        </script>
    <?php endif; ?>

    <div class="layout">
        <!-- ==================================================
         Sidebar Navigation
    =================================================== -->
        <aside class="sidebar">
            <div class="sidebar-title">Cafe POS</div>
            <div class="sidebar-sub">CodeIgniter 4 Dev</div>

            <div class="sidebar-scroll">
                <!-- Main -->
                <div class="nav-section-title collapsible" data-target="main">
                    Main <span class="collapse-arrow">▾</span>
                </div>
                <div class="nav-group" id="nav-main">
                    <?= $navLink('/', 'Dashboard', $menuAllowed('dashboard'), $isActive(['', 'dashboard']), false); ?>
                    <?= $navLink('pos/touch', 'POS UI (Touch)', $menuAllowed('transactions'), $isActive(['pos/touch']), true); ?>
                </div>

                <!-- Master -->
                <div class="nav-section-title collapsible" data-target="master">
                    Master <span class="collapse-arrow">▾</span>
                </div>
                <div class="nav-group" id="nav-master">
                    <?= $navLink('master/products', 'Menu / Produk', $menuAllowed('master'), $isActive(['master/products']), true); ?>
                    <?= $navLink('master/menu-options', 'Menu Options', $menuAllowed('master'), $isActive(['master/menu-options']), true); ?>
                    <?= $navLink('master/categories', 'Kategori Menu', $menuAllowed('master'), $isActive(['master/categories']), true); ?>
                    <?= $navLink('master/raw-materials', 'Bahan Baku', $menuAllowed('master'), $isActive(['master/raw-materials']), true); ?>
                    <?= $navLink('master/suppliers', 'Supplier', $menuAllowed('master'), $isActive(['master/suppliers']), true); ?>
                    <?= $navLink('master/customers', 'Customer', $menuAllowed('master'), $isActive(['master/customers']), true); ?>
                    <?= $navLink('master/recipes', 'Resep', $menuAllowed('master'), $isActive(['master/recipes']), true); ?>
                    <?= $navLink('audit-logs', 'Audit Log', $menuAllowed('master'), $isActive(['audit-logs']), true); ?>
                </div>

                <!-- Transactions -->
                <div class="nav-section-title collapsible" data-target="transactions">
                    Transaksi <span class="collapse-arrow">▾</span>
                </div>
                <div class="nav-group" id="nav-transactions">
                    <?= $navLink('purchases', 'Pembelian Bahan', $menuAllowed('transactions'), $isActive(['purchases']), true); ?>
                    <?= $navLink('transactions/sales', 'POS Penjualan', $menuAllowed('transactions'), $isActive(['transactions/sales']), true); ?>
                    <?= $navLink('transactions/kitchen', 'Kitchen Queue', $menuAllowed('transactions'), $isActive(['transactions/kitchen']), true); ?>
                </div>

                <!-- Inventory -->
                <div class="nav-section-title collapsible" data-target="inventory">
                    Inventory <span class="collapse-arrow">▾</span>
                </div>
                <div class="nav-group" id="nav-inventory">
                    <?= $navLink('inventory/stock-movements', 'Riwayat Stok (IN/OUT)', $menuAllowed('inventory'), $isActive(['inventory/stock-movements']), true); ?>
                    <?= $navLink('inventory/stock-card', 'Kartu Stok per Bahan', $menuAllowed('inventory'), $isActive(['inventory/stock-card']), true); ?>
                    <?= $navLink('inventory/stock-adjustments', 'Stock Adjustment', $menuAllowed('inventory'), $isActive(['inventory/stock-adjustments']), true); ?>
                    <?= $navLink('inventory/stock-opname', 'Stock & Selisih Fisik', $menuAllowed('inventory'), $isActive(['inventory/stock-opname']), true); ?>
                </div>

                <!-- Reports -->
                <div class="nav-section-title collapsible" data-target="reports">
                    Laporan <span class="collapse-arrow">▾</span>
                </div>
                <div class="nav-group" id="nav-reports">
                    <?= $navLink('reports/sales/time', 'Penjualan by Time', $menuAllowed('reports'), $isActive(['reports/sales/time']), true); ?>
                    <?= $navLink('reports/sales/menu', 'Penjualan per Menu', $menuAllowed('reports'), $isActive(['reports/sales/menu']), true); ?>
                    <?= $navLink('reports/sales/category', 'Penjualan per Kategori', $menuAllowed('reports'), $isActive(['reports/sales/category']), true); ?>
                    <?= $navLink('reports/purchases/supplier', 'Pembelian per Supplier', $menuAllowed('reports'), $isActive(['reports/purchases/supplier']), true); ?>
                    <?= $navLink('reports/purchases/material', 'Pembelian per Bahan', $menuAllowed('reports'), $isActive(['reports/purchases/material']), true); ?>
                    <?= $navLink('reports/stock/variance', 'Stok & Selisih', $menuAllowed('reports'), $isActive(['reports/stock/variance']), true); ?>
                </div>

                <!-- Overhead -->
                <div class="nav-section-title collapsible" data-target="overhead">
                    Overhead <span class="collapse-arrow">▾</span>
                </div>
                <div class="nav-group" id="nav-overhead">
                    <?php $isOverheadPayroll = str_starts_with($currentPath, 'overheads/payroll'); ?>

                    <?= $navLink('overheads', 'Biaya Overhead', $menuAllowed('overhead'), (! $isOverheadPayroll && $isActive(['overheads'])), true); ?>
                    <?= $navLink('overhead-categories', 'Kategori Overhead', $menuAllowed('overhead'), $isActive(['overhead-categories']), true); ?>
                    <?= $navLink('overheads/payroll', 'Overhead (Payroll)', $menuAllowed('overhead'), $isOverheadPayroll, true); ?>
                </div>

                <!-- Settings -->
                <div class="nav-section-title collapsible" data-target="settings">
                    Settings <span class="collapse-arrow">ƒ-ó</span>
                </div>
                <div class="nav-group" id="nav-settings">
                    <?= $navLink('users', 'User Management', $canManageUsers, $isActive(['users']), true); ?>
                </div>
            </div>
        </aside>

        <!-- ==================================================
         Main Content
    =================================================== -->
        <div class="main">
            <header class="topbar">
                <div>
                    <div class="topbar-title"><?= esc($title ?? ''); ?></div>
                    <?php if (! empty($subtitle)): ?>
                        <div class="topbar-subtitle"><?= esc($subtitle); ?></div>
                    <?php endif; ?>
                </div>

                <div class="topbar-right">
                    <span class="topbar-pill" style="margin-right: 8px;">
                        <?= esc(session('full_name') ?? session('username') ?? ''); ?>
                    </span>
                    <button type="button" id="themeToggle" class="theme-toggle-btn" aria-label="Toggle theme" style="margin-right: 8px;">
                        🌙
                    </button>
                    <a href="<?= site_url('logout'); ?>" style="font-size:12px; text-decoration:none; color:inherit;">
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

    <!-- CSRF meta untuk AJAX / JS -->
    <meta name="csrf-name" content="<?= csrf_token(); ?>">
    <meta name="csrf-token" content="<?= csrf_hash(); ?>">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url('js/app.js') . '?v=' . $assetVer; ?>"></script>
    <script src="<?= base_url('js/theme-toggle.js') . '?v=' . $assetVer; ?>"></script>
</body>

</html>
