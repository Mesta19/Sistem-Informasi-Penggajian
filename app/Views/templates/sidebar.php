<?php
$current_uri = service('request')->getUri()->getPath();
if (!function_exists('isActive')) {
    function isActive($path, $current_uri) {
        if ($path === 'admin/dashboard') {
            return ($current_uri === 'admin/dashboard' || $current_uri === '') ? 'active' : '';
        }
        return (strpos($current_uri, $path) === 0) ? 'active' : '';
    }
}
?>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('admin/dashboard') ?>">
        <div class="sidebar-brand-icon">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SISFO GAJI</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?= isActive('admin/dashboard', $current_uri) ?>">
        <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <?php if (session()->get('user.role') === 'Admin'): ?>
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Manajemen Data
        </div>

        <!-- Nav Item - Data Karyawan -->
        <li class="nav-item <?= isActive('admin/karyawan', $current_uri) ?>">
            <a class="nav-link" href="<?= base_url('admin/karyawan') ?>">
                <i class="fas fa-fw fa-user"></i>
                <span>Data Karyawan</span>
            </a>
        </li>

        <!-- Nav Item - Jabatan -->
        <li class="nav-item <?= isActive('admin/jabatan', $current_uri) ?>">
            <a class="nav-link" href="<?= base_url('admin/jabatan') ?>">
                <i class="fas fa-fw fa-briefcase"></i>
                <span>Jabatan</span>
            </a>
        </li>

        <!-- Nav Item - Komponen Gaji -->
        <li class="nav-item <?= isActive('admin/komponen', $current_uri) ?>">
            <a class="nav-link" href="<?= base_url('admin/komponen') ?>">
                <i class="fas fa-fw fa-list"></i>
                <span>Komponen Gaji</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Transaksi & Akun
        </div>

        <!-- Nav Item - Penggajian -->
        <li class="nav-item <?= isActive('admin/gaji', $current_uri) ?>">
            <a class="nav-link" href="<?= base_url('admin/gaji') ?>">
                <i class="fas fa-fw fa-money-bill-wave"></i>
                <span>Penggajian</span>
            </a>
        </li>

        <!-- Nav Item - User / Akun -->
        <li class="nav-item <?= isActive('admin/user', $current_uri) ?>">
            <a class="nav-link" href="<?= base_url('admin/user') ?>">
                <i class="fas fa-fw fa-key"></i>
                <span>User / Akun</span>
            </a>
        </li>
    <?php endif; ?>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar (Output buffered from header.php) -->
        <?= $topbar_html ?? '' ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">
