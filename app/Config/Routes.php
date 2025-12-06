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
    $routes->get('/', 'Dashboard::index');
    $routes->get('/dashboard', 'Dashboard::index');

    // Master Products
    $routes->group('master', ['namespace' => 'App\Controllers\Master'], static function($routes) {
        $routes->get('products', 'Products::index');
        $routes->get('products/create', 'Products::create');
        $routes->post('products/store', 'Products::store');
        $routes->get('products/edit/(:num)', 'Products::edit/$1');
        $routes->post('products/update/(:num)', 'Products::update/$1');
        $routes->post('products/delete/(:num)', 'Products::delete/$1');
    });

});


