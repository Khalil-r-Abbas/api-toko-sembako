<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->resource('products', ['except' => 'new,edit']);

    // transaksi bersifat immutable: tidak ada update
    $routes->resource('transactions', ['except' => 'new,edit,update']);

    // sale_items read-only (dibuat lewat POST /api/transactions)
    $routes->get('sale-items', 'SaleItems::index');
    $routes->get('sale-items/(:num)', 'SaleItems::show/$1');
});

$routes->get('token-manager', 'TokenManager::index');
$routes->post('token-manager/generate', 'TokenManager::generate');

//path untuk ke halaman utama menampilkan produk
$routes->get('produk', 'ProductPage::index');

//path untuk ke bagian cari resep
$routes->get('bikin-makan', 'BikinMakan::index');
$routes->get('bikin-makan/cari', 'BikinMakan::cari');
