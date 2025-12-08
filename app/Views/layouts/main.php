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
        }
        .nav-link {
            display: block;
            padding: 7px 8px;
            border-radius: 8px;
            font-size: 13px;
            color: #e5e7eb;
            text-decoration: none;
            margin-bottom: 3px;
        }
        .nav-link:hover {
            background: #0f172a;
        }
        .nav-link.small {
            font-size: 12px;
            color: #9ca3af;
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
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-title">Cafe POS</div>
        <div class="sidebar-sub">CodeIgniter 4 • Local Dev</div>

        <div class="nav-section-title">Main</div>
        <a href="<?= site_url('/') ?>" class="nav-link">Dashboard</a>

        <div class="nav-section-title">Master</div>
        <a href="<?= site_url('master/products') ?>" class="nav-link small">Menu / Produk</a>
        <a href="<?= site_url('master/categories') ?>" class="nav-link small">Kategori Menu</a>
        <a href="<?= site_url('master/raw-materials') ?>" class="nav-link small">Bahan Baku</a>
        <a href="<?= site_url('master/suppliers') ?>" class="nav-link small">Supplier</a>
        <a href="<?= site_url('master/recipes') ?>" class="nav-link small">Resep</a>

        <div class="nav-section-title">Transaksi</div>
        <a href="<?= site_url('purchases') ?>" class="nav-link small">Pembelian Bahan</a>
        <a href="<?= site_url('transactions/sales') ?>" class="nav-link small">POS Penjualan</a>

        <div class="nav-section-title">Inventory</div>
        <a href="<?= site_url('inventory/stock-movements') ?>" class="nav-link small">Riwayat Stok (IN/OUT)</a>
        <a href="<?= site_url('inventory/stock-card') ?>" class="nav-link small">Kartu Stok per Bahan</a>

        <div class="nav-section-title">Laporan</div>
        <a href="<?= site_url('reports/sales/daily') ?>" class="nav-link small">Penjualan Harian</a>
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
