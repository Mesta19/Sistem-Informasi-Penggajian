<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

/**
 * Cek apakah user sudah login.
 * Jika belum, redirect ke halaman login.
 */
function cekLogin() {
    if (!session()->has('user')) {
        session()->setFlashdata('flash_error', 'Silakan login terlebih dahulu.');
        header('Location: ' . base_url('/'));
        exit;
    }
}

/**
 * Cek role user yang sedang login.
 * Jika role tidak sesuai, redirect ke dashboard.
 */
function cekRole(string $role) {
    cekLogin();
    $currentRole = session()->get('user.role');
    if ($currentRole !== $role) {
        header('Location: ' . base_url('admin/dashboard'));
        exit;
    }
}

