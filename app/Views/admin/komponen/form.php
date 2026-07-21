<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<?php
$isEdit = isset($komponen);
$title = $isEdit ? 'Edit Komponen Gaji' : 'Tambah Komponen Gaji';
$actionUrl = $isEdit ? base_url('admin/komponen/update/' . $komponen['id_komponen']) : base_url('admin/komponen/store');
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    <a href="<?= base_url('admin/komponen') ?>" class="btn btn-sm btn-secondary shadow-sm">
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
        <h6 class="m-0 font-weight-bold text-primary">Formulir Komponen Gaji</h6>
    </div>
    <div class="card-body">
        <form action="<?= $actionUrl ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="nama_komponen">Nama Komponen</label>
                <input type="text" class="form-control" id="nama_komponen" name="nama_komponen" value="<?= esc(old('nama_komponen', $komponen['nama_komponen'] ?? '')) ?>" required>
            </div>

            <div class="form-group">
                <label for="jenis">Jenis Komponen</label>
                <select class="form-control" id="jenis" name="jenis" required>
                    <option value="Tunjangan" <?= (old('jenis', $komponen['jenis'] ?? '') === 'Tunjangan') ? 'selected' : '' ?>>Tunjangan</option>
                    <option value="Potongan" <?= (old('jenis', $komponen['jenis'] ?? '') === 'Potongan') ? 'selected' : '' ?>>Potongan</option>
                </select>
            </div>

            <div class="form-group">
                <label for="nilai">Nilai Standar (Rupiah)</label>
                <input type="number" step="0.01" class="form-control" id="nilai" name="nilai" value="<?= esc(old('nilai', $komponen['nilai'] ?? '')) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/komponen') ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
