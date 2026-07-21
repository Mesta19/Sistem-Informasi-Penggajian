<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Komponen Gaji</h1>
    <a href="<?= base_url('admin/komponen/create') ?>" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Komponen
    </a>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Komponen Table Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Master Data Tunjangan & Potongan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID Komponen</th>
                        <th>Nama Komponen</th>
                        <th>Jenis</th>
                        <th>Nilai Standar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($komponen)): ?>
                        <?php foreach ($komponen as $k): ?>
                            <tr>
                                <td><?= esc($k['id_komponen']) ?></td>
                                <td><?= esc($k['nama_komponen']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $k['jenis'] === 'Tunjangan' ? 'success' : 'danger' ?>">
                                        <?= esc($k['jenis']) ?>
                                    </span>
                                </td>
                                <td>Rp <?= number_format($k['nilai'], 2, ',', '.') ?></td>
                                <td>
                                    <a href="<?= base_url('admin/komponen/edit/' . $k['id_komponen']) ?>" class="btn btn-sm btn-warning mr-1 mb-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= base_url('admin/komponen/delete/' . $k['id_komponen']) ?>" class="btn btn-sm btn-danger btn-delete mb-1" data-message="Apakah Anda yakin ingin menghapus komponen ini?">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data komponen gaji.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
