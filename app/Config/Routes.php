<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'AuthController::showLogin');
$routes->post('/', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

$routes->group('admin', function($routes) {
    // Dashboard
    $routes->get('dashboard', 'admin\Dashboard::index');
    $routes->post('dashboard/update-profile', 'admin\Dashboard::updateProfile');

    // Karyawan
    $routes->get('karyawan', 'admin\KaryawanController::index');
    $routes->get('karyawan/persetujuan', 'admin\KaryawanController::persetujuan');
    $routes->get('karyawan/create', 'admin\KaryawanController::create');
    $routes->post('karyawan/store', 'admin\KaryawanController::store');
    $routes->get('karyawan/edit/(:any)', 'admin\KaryawanController::edit/$1');
    $routes->post('karyawan/update/(:any)', 'admin\KaryawanController::update/$1');
    $routes->post('karyawan/update-jabatan/(:any)', 'admin\KaryawanController::updateJabatan/$1');
    $routes->get('karyawan/delete/(:any)', 'admin\KaryawanController::delete/$1');
    $routes->post('karyawan/setujui/(:any)', 'admin\KaryawanController::setujui/$1');
    $routes->post('karyawan/tolak/(:any)', 'admin\KaryawanController::tolak/$1');
    $routes->post('karyawan/approve/(:any)', 'admin\KaryawanController::approve/$1');
    $routes->post('karyawan/reject/(:any)', 'admin\KaryawanController::reject/$1');

    // Jabatan
    $routes->get('jabatan', 'admin\JabatanController::index');
    $routes->get('jabatan/create', 'admin\JabatanController::create');
    $routes->post('jabatan/store', 'admin\JabatanController::store');
    $routes->get('jabatan/edit/(:num)', 'admin\JabatanController::edit/$1');
    $routes->post('jabatan/update/(:num)', 'admin\JabatanController::update/$1');
    $routes->get('jabatan/delete/(:num)', 'admin\JabatanController::delete/$1');
    $routes->get('jabatan/karyawan/(:num)', 'admin\JabatanController::karyawan/$1');
    $routes->post('jabatan/karyawan/simpan/(:num)', 'admin\JabatanController::karyawanSimpan/$1');

    // Komponen Gaji
    $routes->get('komponen', 'admin\KomponenController::index');
    $routes->get('komponen/create', 'admin\KomponenController::create');
    $routes->post('komponen/store', 'admin\KomponenController::store');
    $routes->get('komponen/edit/(:num)', 'admin\KomponenController::edit/$1');
    $routes->post('komponen/update/(:num)', 'admin\KomponenController::update/$1');
    $routes->get('komponen/delete/(:num)', 'admin\KomponenController::delete/$1');

    // Penggajian
    $routes->get('gaji', 'admin\GajiController::index');
    $routes->get('gaji/proses', 'admin\GajiController::proses');
    $routes->post('gaji/simpan', 'admin\GajiController::simpan');
    $routes->get('gaji/slip/(:num)', 'admin\GajiController::slip/$1');
    $routes->get('gaji/delete/(:num)', 'admin\GajiController::delete/$1');

    // User / Akun
    $routes->get('user', 'admin\UserController::index');
    $routes->get('user/create', 'admin\UserController::create');
    $routes->post('user/store', 'admin\UserController::store');
    $routes->get('user/edit/(:num)', 'admin\UserController::edit/$1');
    $routes->post('user/update/(:num)', 'admin\UserController::update/$1');
    $routes->get('user/delete/(:num)', 'admin\UserController::delete/$1');
});
