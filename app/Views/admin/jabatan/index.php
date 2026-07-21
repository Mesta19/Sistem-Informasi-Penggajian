<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Daftar Jabatan</h1>
    <a href="<?= base_url('admin/jabatan/create') ?>" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Jabatan
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

<!-- Jabatan Table Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Jabatan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nama Jabatan</th>
                        <th>Gaji Pokok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($jabatan)): ?>
                        <?php foreach ($jabatan as $j): ?>
                            <tr>
                                <td><strong><?= esc($j['nama_jabatan']) ?></strong></td>
                                <td>Rp <?= number_format($j['gaji_pokok'], 2, ',', '.') ?></td>
                                <td>
                                    <a href="<?= base_url('admin/jabatan/edit/' . $j['id_jabatan']) ?>" class="btn btn-sm btn-warning mr-1 mb-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= base_url('admin/jabatan/delete/' . $j['id_jabatan']) ?>" class="btn btn-sm btn-danger btn-delete mb-1" data-message="Apakah Anda yakin ingin menghapus jabatan ini?">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data jabatan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
