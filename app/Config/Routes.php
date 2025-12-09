<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Login & Logout
$routes->get('login', 'Auth\Login::index');
$routes->post('login/attempt', 'Auth\Login::attempt');
$routes->get('logout', 'Auth\Logout::index');

$routes->group('', ['filter' => 'auth'], static function($routes) {
    $routes->group('', ['filter' => 'role'], static function($routes) {

        $routes->get('/', 'Dashboard::index');
        $routes->get('/dashboard', 'Dashboard::index');

        // Master Products
        $routes->group('master', ['namespace' => 'App\Controllers\Master'], static function($routes) {
            // Produk
            $routes->get('products', 'Products::index');
            $routes->get('products/create', 'Products::create');
            $routes->post('products/store', 'Products::store');
            $routes->get('products/edit/(:num)', 'Products::edit/$1');
            $routes->post('products/update/(:num)', 'Products::update/$1');
            $routes->post('products/delete/(:num)', 'Products::delete/$1');

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
        $routes->group('master/recipes', ['namespace' => 'App\Controllers\Master'], static function($routes) {
            $routes->get('/', 'Recipes::index');
            $routes->get('create', 'Recipes::create');
            $routes->post('store', 'Recipes::store');
            $routes->get('edit/(:num)', 'Recipes::edit/$1');
            $routes->post('update/(:num)', 'Recipes::update/$1');
        });


        // Transactions - Purchases
        $routes->group('purchases', ['namespace' => 'App\Controllers\Transactions'], static function($routes) {
            $routes->get('/', 'Purchases::index');
            $routes->get('create', 'Purchases::create');
            $routes->post('store', 'Purchases::store');
            $routes->get('detail/(:num)', 'Purchases::detail/$1');
        });

        // Transactions - Sales
        $routes->group('transactions', ['filter' => 'auth'], static function($routes) {
            // Sales
            $routes->get('sales',               'Transactions\Sales::index');
            $routes->get('sales/create',        'Transactions\Sales::create');
            $routes->post('sales/store',        'Transactions\Sales::store');
            $routes->get('sales/detail/(:num)', 'Transactions\Sales::detail/$1');

        });

        // Inventory - Stock Movements
        $routes->get('inventory/stock-movements', 'Inventory\StockMovements::index');
        $routes->get('inventory/stock-card', 'Inventory\StockMovements::card');

        // Reports - Sales Daily Summary
        $routes->get('reports/sales/daily', 'Reports\SalesSummary::daily');
        $routes->get('reports/sales/menu', 'Reports\SalesSummary::perMenu');

        // Overheads
        $routes->get('overheads', 'Overheads::index');
        $routes->get('overheads/create', 'Overheads::create');
        $routes->post('overheads/store', 'Overheads::store');
        $routes->get('overhead-categories', 'OverheadCategories::index');
        $routes->get('overhead-categories/create', 'OverheadCategories::create');
        $routes->post('overhead-categories/store', 'OverheadCategories::store');

    });


});
