<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Login & Logout
$routes->get('login', 'Auth\Login::index');
$routes->post('login/attempt', 'Auth\Login::attempt');
$routes->get('logout', 'Auth\Logout::index');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->group('', ['filter' => 'role'], static function ($routes) {

        $routes->get('/', 'Dashboard::index');
        $routes->get('/dashboard', 'Dashboard::index');

        // Master Products
        $routes->group('master', ['namespace' => 'App\Controllers\Master'], static function ($routes) {
            // Produk
            $routes->get('products', 'Products::index');
            $routes->get('products/create', 'Products::create');
            $routes->post('products/store', 'Products::store');
            $routes->get('products/edit/(:num)', 'Products::edit/$1');
            $routes->post('products/update/(:num)', 'Products::update/$1');
            $routes->post('products/delete/(:num)', 'Products::delete/$1');

            // Menu Categories
            $routes->get('categories', 'MenuCategories::index');
            $routes->get('categories/create', 'MenuCategories::create');
            $routes->post('categories/store', 'MenuCategories::store');
            $routes->get('categories/edit/(:num)', 'MenuCategories::edit/$1');
            $routes->post('categories/update/(:num)', 'MenuCategories::update/$1');
            $routes->post('categories/delete/(:num)', 'MenuCategories::delete/$1');

            // Raw Materials
            $routes->get('raw-materials', 'RawMaterials::index');
            $routes->get('raw-materials/create', 'RawMaterials::create');
            $routes->post('raw-materials/store', 'RawMaterials::store');
            $routes->get('raw-materials/edit/(:num)', 'RawMaterials::edit/$1');
            $routes->post('raw-materials/update/(:num)', 'RawMaterials::update/$1');
            $routes->post('raw-materials/delete/(:num)', 'RawMaterials::delete/$1');

            // Suppliers
            $routes->get('suppliers', 'Suppliers::index');
            $routes->get('suppliers/create', 'Suppliers::create');
            $routes->post('suppliers/store', 'Suppliers::store');
            $routes->get('suppliers/edit/(:num)', 'Suppliers::edit/$1');
            $routes->post('suppliers/update/(:num)', 'Suppliers::update/$1');
            $routes->post('suppliers/delete/(:num)', 'Suppliers::delete/$1');
        });

        // Master Recipes (Resep per Menu)
        $routes->group('master/recipes', ['namespace' => 'App\Controllers\Master'], static function ($routes) {
            $routes->get('/', 'Recipes::index');
            $routes->get('create', 'Recipes::create');
            $routes->post('store', 'Recipes::store');
            $routes->get('edit/(:num)', 'Recipes::edit/$1');
            $routes->post('update/(:num)', 'Recipes::update/$1');
        });


        // Transactions - Purchases
        $routes->group('purchases', ['namespace' => 'App\Controllers\Transactions'], static function ($routes) {
            $routes->get('/', 'Purchases::index');
            $routes->get('create', 'Purchases::create');
            $routes->post('store', 'Purchases::store');
            $routes->get('detail/(:num)', 'Purchases::detail/$1');
        });

        // Transactions - Sales
        $routes->group('transactions', ['filter' => 'auth'], static function ($routes) {
            // Sales
            $routes->get('sales',               'Transactions\Sales::index');
            $routes->get('sales/create',        'Transactions\Sales::create');
            $routes->post('sales/store',        'Transactions\Sales::store');
            $routes->get('sales/detail/(:num)', 'Transactions\Sales::detail/$1');
            $routes->post('sales/void/(:num)',  'Transactions\Sales::void/$1');
        });

        // Inventory - Stock Movements
        $routes->get('inventory/stock-movements', 'Inventory\StockMovements::index');
        $routes->get('inventory/stock-card', 'Inventory\StockMovements::card');
        // Inventory planned stubs
        $routes->get('inventory/stock-adjustments', 'Inventory\StockAdjustments::index');
        $routes->get('inventory/stock-opname', 'Inventory\StockOpname::index');

        // Reports - Sales
        $routes->get('reports/sales/menu', 'Reports\SalesSummary::perMenu');
        $routes->get('reports/sales/category', 'Reports\SalesSummary::perCategory');
        $routes->get('reports/sales/time', 'Reports\SalesSummary::byTime');
        $routes->get('reports/purchases/supplier', 'Reports\PurchaseSummary::perSupplier');
        $routes->get('reports/purchases/material', 'Reports\PurchaseSummary::perMaterial');
        $routes->get('reports/stock/variance', 'Reports\StockSummary::variance');

        // Overheads
        $routes->get('overheads', 'Overheads::index');
        $routes->get('overheads/create', 'Overheads::create');
        $routes->post('overheads/store', 'Overheads::store');
        $routes->get('overhead-categories', 'OverheadCategories::index');
        $routes->get('overhead-categories/create', 'OverheadCategories::create');
        $routes->post('overhead-categories/store', 'OverheadCategories::store');
        $routes->get('overhead-categories/edit/(:num)', 'OverheadCategories::edit/$1');
        $routes->post('overhead-categories/update/(:num)', 'OverheadCategories::update/$1');
        $routes->post('overhead-categories/toggle', 'OverheadCategories::toggle');
        $routes->get('overheads/payroll', 'OverheadsPayroll::index');
        $routes->get('overheads/payroll/create', 'OverheadsPayroll::create');
        $routes->post('overheads/payroll/store', 'OverheadsPayroll::store');
        $routes->get('overheads/payroll/edit/(:num)', 'OverheadsPayroll::edit/$1');
        $routes->post('overheads/payroll/update/(:num)', 'OverheadsPayroll::update/$1');
        $routes->post('overheads/payroll/delete/(:num)', 'OverheadsPayroll::delete/$1');

        // Audit Logs
        $routes->get('audit-logs', 'AuditLogs::index');

        //Brand Guide
        $routes->get('brand-guide', 'BrandGuide::index');

        // POS Touchscreen UI (Phase 2 stub)
        $routes->get('pos/touch', 'Pos\Touchscreen::index');
    });
});
