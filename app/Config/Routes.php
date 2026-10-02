<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Autentikasi
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::doLogin');
$routes->get('logout', 'AuthController::logout');

// Rute Terproteksi (Admin & Pengelola)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Katalog & Master Barang
    $routes->get('barang', 'BarangController::index');
    $routes->get('barang/form', 'BarangController::form');
    $routes->get('barang/form/(:num)', 'BarangController::form/$1');
    $routes->post('barang/save', 'BarangController::save');
    $routes->post('barang/delete/(:num)', 'BarangController::delete/$1');

    // Kategori Barang
    $routes->get('kategori', 'KategoriController::index');
    $routes->post('kategori/save', 'KategoriController::save');
    $routes->post('kategori/delete/(:num)', 'KategoriController::delete/$1');

    // Riwayat Keluar-Masuk & Laba Rugi Real-Time
    $routes->get('stok', 'StokController::index');
    $routes->post('stok/proses', 'StokController::proses');

    // Manajemen Pengguna (Khusus Admin)
    $routes->group('user', ['filter' => 'auth:admin'], static function ($routes) {
        $routes->get('', 'UserController::index');
        $routes->post('save', 'UserController::save');
        $routes->post('delete/(:num)', 'UserController::delete/$1');
    });
});
