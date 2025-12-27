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
            <?= $navLink('reports/sales/customer', 'Penjualan per Customer', $menuAllowed('reports'), $isActive(['reports/sales/customer']), true); ?>
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