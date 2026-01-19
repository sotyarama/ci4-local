<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Routes Configuration
 *
 * Tujuan:
 * - Mendefinisikan routing untuk aplikasi POS Temu Rasa(CI4)
 *
 * Catatan penting (jangan diubah tanpa sengaja):
 * - Ada 2 lapis proteksi: auth + role filter
 * - Beberapa route tampak redundant/berulang demi kompatibilitas
 *
 * @var RouteCollection $routes
 */

// ======================================================
// Public Routes (Tanpa Auth)
// ======================================================
$routes->get('login',         'Auth\Login::index');
$routes->post('login/attempt', 'Auth\Login::attempt');
$routes->get('logout',        'Auth\Logout::index');
$routes->get('auth/forgot',   'Auth\ForgotPassword::index');
$routes->post('auth/forgot',  'Auth\ForgotPassword::send');
$routes->get('auth/reset',    'Auth\ResetPassword::index');
$routes->post('auth/reset',   'Auth\ResetPassword::update');


// ======================================================
// App Routes (namespace: App\Controllers\App)
// ======================================================
$routes->group('app', ['namespace' => 'App\Controllers\App'], static function ($routes) {
    $routes->get('playground', 'Playground::index');
});

// ======================================================
// Protected Routes (Auth + Role)
// ======================================================
$routes->group('', ['filter' => 'auth'], static function ($routes) {

    $routes->group('', ['filter' => 'role'], static function ($routes) {

        // ------------------------------
        // Dashboard
        // ------------------------------
        $routes->get('/',          'Dashboard::index');
        $routes->get('/dashboard', 'Dashboard::index');


        // ------------------------------
        // Master Data (namespace: App\Controllers\Master)
        // ------------------------------
        $routes->group('master', ['namespace' => 'App\Controllers\Master'], static function ($routes) {

            // Products
            $routes->get('products',                'Products::index');
            $routes->get('products/create',         'Products::create');
            $routes->post('products/store',          'Products::store');
            $routes->get('products/edit/(:num)',    'Products::edit/$1');
            $routes->post('products/update/(:num)',  'Products::update/$1');
            $routes->post('products/delete/(:num)',  'Products::delete/$1');

            // Menu Options (group + options)
            $routes->get('menu-options',            'MenuOptions::index');
            $routes->post('menu-options/save',       'MenuOptions::save');

            // Menu Categories
            $routes->get('categories',              'MenuCategories::index');
            $routes->get('categories/create',       'MenuCategories::create');
            $routes->post('categories/store',        'MenuCategories::store');
            $routes->get('categories/edit/(:num)',  'MenuCategories::edit/$1');
            $routes->post('categories/update/(:num)', 'MenuCategories::update/$1');
            $routes->post('categories/delete/(:num)', 'MenuCategories::delete/$1');

            // Raw Materials
            $routes->get('raw-materials',              'RawMaterials::index');
            $routes->get('raw-materials/create',       'RawMaterials::create');
            $routes->post('raw-materials/store',        'RawMaterials::store');
            $routes->get('raw-materials/edit/(:num)',  'RawMaterials::edit/$1');
            $routes->post('raw-materials/update/(:num)', 'RawMaterials::update/$1');
            $routes->post('raw-materials/delete/(:num)', 'RawMaterials::delete/$1');

            // Customers
            $routes->get('customers',               'Customers::index');
            $routes->get('customers/create',        'Customers::create');
            $routes->post('customers/store',         'Customers::store');
            $routes->get('customers/edit/(:num)',   'Customers::edit/$1');
            $routes->post('customers/update/(:num)', 'Customers::update/$1');
            $routes->post('customers/delete/(:num)', 'Customers::delete/$1');

            // Suppliers
            $routes->get('suppliers',               'Suppliers::index');
            $routes->get('suppliers/create',        'Suppliers::create');
            $routes->post('suppliers/store',         'Suppliers::store');
            $routes->get('suppliers/edit/(:num)',   'Suppliers::edit/$1');
            $routes->post('suppliers/update/(:num)', 'Suppliers::update/$1');
            $routes->post('suppliers/delete/(:num)', 'Suppliers::delete/$1');

            // Units
            $routes->get('units',                'Units::index');
            $routes->get('units/create',         'Units::create');
            $routes->post('units/store',          'Units::store');
            $routes->get('units/edit/(:num)',    'Units::edit/$1');
            $routes->post('units/update/(:num)',  'Units::update/$1');
            $routes->post('units/delete/(:num)',  'Units::delete/$1');
        });


        // ------------------------------
        // Master Recipes (Resep per Menu)
        // namespace: App\Controllers\Master
        // ------------------------------
        $routes->group('master/recipes', ['namespace' => 'App\Controllers\Master'], static function ($routes) {
            $routes->get('/',              'Recipes::index');
            $routes->get('create',         'Recipes::create');
            $routes->post('store',          'Recipes::store');
            $routes->get('edit/(:num)',    'Recipes::edit/$1');
            $routes->post('update/(:num)',  'Recipes::update/$1');
        });


        // ------------------------------
        // Transactions - Purchases
        // namespace: App\Controllers\Transactions
        // ------------------------------
        $routes->group('purchases', ['namespace' => 'App\Controllers\Transactions'], static function ($routes) {
            $routes->get('/',             'Purchases::index');
            $routes->get('create',        'Purchases::create');
            $routes->post('store',         'Purchases::store');
            $routes->get('detail/(:num)', 'Purchases::detail/$1');
        });


        // ------------------------------
        // Transactions - Sales
        // Catatan: group ini masih pakai filter auth (redundan),
        // tapi dipertahankan agar behavior tetap sama.
        // ------------------------------
        $routes->group('transactions', ['filter' => 'auth'], static function ($routes) {
            $routes->get('sales',               'Transactions\Sales::index');
            $routes->get('sales/create',        'Transactions\Sales::create');
            $routes->post('sales/store',         'Transactions\Sales::store');
            $routes->get('sales/detail/(:num)', 'Transactions\Sales::detail/$1');
            $routes->get('sales/kitchen-ticket/(:num)', 'Transactions\Sales::kitchenTicket/$1');
            $routes->post('sales/void/(:num)',   'Transactions\Sales::void/$1');
            $routes->get('kitchen',             'Transactions\Sales::kitchenQueue');
            $routes->post('kitchen/done/(:num)', 'Transactions\Sales::kitchenDone/$1');
        });


        // ------------------------------
        // Inventory
        // ------------------------------
        $routes->get('inventory/stock-movements',   'Inventory\StockMovements::index');
        $routes->get('inventory/stock-card',        'Inventory\StockMovements::card');

        // Inventory planned stubs
        $routes->get('inventory/stock-adjustments', 'Inventory\StockAdjustments::index');
        $routes->get('inventory/stock-opname',      'Inventory\StockOpname::index');


        // ------------------------------
        // Reports
        // ------------------------------
        $routes->get('reports/sales/menu',          'Reports\SalesSummary::perMenu');
        $routes->get('reports/sales/category',      'Reports\SalesSummary::perCategory');
        $routes->get('reports/sales/time',          'Reports\SalesSummary::byTime');
        $routes->get('reports/sales/customer',      'Reports\SalesSummary::perCustomer');
        $routes->get('reports/sales/customer/(:num)', 'Reports\SalesSummary::customerDetail/$1');

        $routes->get('reports/purchases/supplier',  'Reports\PurchaseSummary::perSupplier');
        $routes->get('reports/purchases/material',  'Reports\PurchaseSummary::perMaterial');

        $routes->get('reports/stock/variance',      'Reports\StockSummary::variance');


        // ------------------------------
        // Overheads
        // ------------------------------
        $routes->get('overheads',                        'Overheads::index');
        $routes->get('overheads/create',                 'Overheads::create');
        $routes->post('overheads/store',                  'Overheads::store');

        $routes->get('overhead-categories',              'OverheadCategories::index');
        $routes->get('overhead-categories/create',       'OverheadCategories::create');
        $routes->post('overhead-categories/store',        'OverheadCategories::store');
        $routes->get('overhead-categories/edit/(:num)',  'OverheadCategories::edit/$1');
        $routes->post('overhead-categories/update/(:num)', 'OverheadCategories::update/$1');
        $routes->post('overhead-categories/toggle',       'OverheadCategories::toggle');

        $routes->get('overheads/payroll',                'OverheadsPayroll::index');
        $routes->get('overheads/payroll/create',         'OverheadsPayroll::create');
        $routes->post('overheads/payroll/store',          'OverheadsPayroll::store');
        $routes->get('overheads/payroll/edit/(:num)',    'OverheadsPayroll::edit/$1');
        $routes->post('overheads/payroll/update/(:num)',  'OverheadsPayroll::update/$1');
        $routes->post('overheads/payroll/delete/(:num)',  'OverheadsPayroll::delete/$1');


        // ------------------------------
        // Users (Owner/Auditor)
        // ------------------------------
        $routes->group('users', ['filter' => 'role:owner,auditor'], static function ($routes) {
            $routes->get('/',             'Users::index');
            $routes->get('create',        'Users::create');
            $routes->post('store',         'Users::store');
            $routes->get('edit/(:num)',   'Users::edit/$1');
            $routes->post('update/(:num)', 'Users::update/$1');
            $routes->post('delete/(:num)', 'Users::delete/$1');
        });


        // ------------------------------
        // Audit Logs
        // ------------------------------
        $routes->get('audit-logs', 'AuditLogs::index');


        // ------------------------------
        // Activity Logs (User-facing)
        // ------------------------------
        $routes->get('logs', 'Logs::index');


        // ------------------------------
        // Brand Guide
        // ------------------------------
        $routes->get('brand-guide', 'BrandGuide::index');

        // ------------------------------
        // Guides
        // ------------------------------
        $routes->get('branding', 'Guides::branding');
        $routes->get('how-to-use', 'Guides::howToUse');


        // ------------------------------
        // POS Touchscreen UI (Phase 2 stub)
        // ------------------------------
        $routes->get('pos/touch', 'Pos\Touchscreen::index');
    });
});
