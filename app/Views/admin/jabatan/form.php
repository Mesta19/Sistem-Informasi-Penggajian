<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<?php
$isEdit = isset($jabatan);
$title = $isEdit ? 'Edit Jabatan' : 'Tambah Jabatan';
$actionUrl = $isEdit ? base_url('admin/jabatan/update/' . $jabatan['id_jabatan']) : base_url('admin/jabatan/store');
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    <a href="<?= base_url('admin/jabatan') ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Jabatan</h6>
    </div>
    <div class="card-body">
        <form action="<?= $actionUrl ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="nama_jabatan">Nama Jabatan</label>
                <input type="text" class="form-control" id="nama_jabatan" name="nama_jabatan" value="<?= esc(old('nama_jabatan', $jabatan['nama_jabatan'] ?? '')) ?>" required>
            </div>

            <div class="form-group">
                <label for="gaji_pokok">Gaji Pokok (Rupiah)</label>
                <input type="number" step="0.01" class="form-control" id="gaji_pokok" name="gaji_pokok" value="<?= esc(old('gaji_pokok', $jabatan['gaji_pokok'] ?? '')) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/jabatan') ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
