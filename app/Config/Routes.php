<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ---------- Rute Publik (Auth) ----------
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

// ---------- Rute Terproteksi (Wajib Login) ----------
$routes->group('', ['filter' => 'auth'], static function ($r) {
    $r->get('/', 'DashboardController::index');
    $r->get('dashboard', 'DashboardController::index');
    $r->get('dashboard/data', 'DashboardController::chartData');

    // Master: kategori
    $r->group('kategori', static function ($k) {
        $k->get('/', 'KategoriController::index');
        $k->post('save', 'KategoriController::save');
        $k->post('delete/(:num)', 'KategoriController::delete/$1');
    });

    // Master: barang
    $r->group('barang', static function ($b) {
        $b->get('/', 'BarangController::index');
        $b->get('form', 'BarangController::form');
        $b->get('form/(:num)', 'BarangController::form/$1');
        $b->post('save', 'BarangController::save');
        $b->post('delete/(:num)', 'BarangController::delete/$1');
        $b->get('riwayat/(:num)', 'BarangController::riwayat/$1');
    });

    // Master: supplier
    $r->group('supplier', static function ($s) {
        $s->get('/', 'SupplierController::index');
        $s->get('form', 'SupplierController::form');
        $s->get('form/(:num)', 'SupplierController::form/$1');
        $s->post('save', 'SupplierController::save');
        $s->post('delete/(:num)', 'SupplierController::delete/$1');
    });

    // Unit fisik (IMEI tracking)
    $r->group('unit', static function ($u) {
        $u->get('/', 'UnitController::index');
        $u->get('tersedia/(:num)', 'UnitController::tersediaJson/$1');
        $u->get('form', 'UnitController::form');
        $u->get('form/(:num)', 'UnitController::form/$1');
        $u->post('save', 'UnitController::save');
        $u->post('delete/(:num)', 'UnitController::delete/$1');
        $u->post('status/(:num)', 'UnitController::ubahStatus/$1');
    });

    // Transaksi Pembelian
    $r->group('pembelian', static function ($p) {
        $p->get('/', 'PembelianController::index');
        $p->get('form', 'PembelianController::form');
        $p->get('form/(:num)', 'PembelianController::form/$1');
        $p->post('save', 'PembelianController::save');
        $p->get('detail/(:num)', 'PembelianController::detail/$1');
        $p->post('delete/(:num)', 'PembelianController::delete/$1');
    });

    // Transaksi Penjualan (Kasir)
    $r->group('penjualan', static function ($j) {
        $j->get('/', 'PenjualanController::index');
        $j->get('form', 'PenjualanController::form');
        $j->get('detail/(:num)', 'PenjualanController::detail/$1');
        $j->post('save', 'PenjualanController::save');
        $j->post('delete/(:num)', 'PenjualanController::delete/$1');
        $j->post('batal/(:num)', 'PenjualanController::batal/$1');
    });

    // Utang & Cicilan
    $r->group('utang', static function ($ut) {
        $ut->get('/', 'UtangController::index');
        $ut->get('detail/(:num)', 'UtangController::detail/$1');
        $ut->post('bayar/(:num)', 'UtangController::bayar/$1');
        $ut->post('delete/(:num)', 'UtangController::delete/$1');
    });

    // Laporan
    $r->get('laporan', 'LaporanController::index');
    $r->get('laporan/laba-rugi', 'LaporanController::labaRugi');
    $r->get('laporan/utang', 'LaporanController::utang');
    $r->get('laporan/stok', 'LaporanController::stok');

    // Manajemen Pengguna (Admin Only)
    $r->group('user', ['filter' => 'role:admin'], static function ($usr) {
        $usr->get('/', 'UserController::index');
        $usr->post('save', 'UserController::save');
        $usr->post('delete/(:num)', 'UserController::delete/$1');
    });
});
