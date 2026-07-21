<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<?php
$isEdit = isset($karyawan);
$title = $isEdit ? 'Edit Karyawan' : 'Tambah Karyawan';
$actionUrl = $isEdit ? base_url('admin/karyawan/update/' . $karyawan['id_karyawan']) : base_url('admin/karyawan/store');
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    <a href="<?= base_url('admin/karyawan') ?>" class="btn btn-sm btn-secondary shadow-sm">
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

<div class="row">
    <!-- Form Utama -->
    <div class="col-lg-12 col-md-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Formulir Karyawan</h6>
            </div>
            <div class="card-body">
                <form action="<?= $actionUrl ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="nama_karyawan" class="font-weight-bold">Nama Karyawan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_karyawan" name="nama_karyawan" value="<?= esc(old('nama_karyawan', $karyawan['nama_karyawan'] ?? '')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_masuk" class="font-weight-bold">Tanggal Masuk <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?= esc(old('tanggal_masuk', $karyawan['tanggal_masuk'] ?? '')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status" class="font-weight-bold">Status Verifikasi Profil</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Belum Lengkap" <?= (old('status', $karyawan['status'] ?? 'Belum Lengkap') === 'Belum Lengkap') ? 'selected' : '' ?>>Belum Lengkap</option>
                            <option value="Menunggu Persetujuan" <?= (old('status', $karyawan['status'] ?? '') === 'Menunggu Persetujuan') ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                            <option value="Aktif" <?= (old('status', $karyawan['status'] ?? '') === 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                            <option value="Butuh Revisi" <?= (old('status', $karyawan['status'] ?? '') === 'Butuh Revisi') ? 'selected' : '' ?>>Butuh Revisi</option>
                            <option value="Tidak Aktif" <?= (old('status', $karyawan['status'] ?? '') === 'Tidak Aktif') ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <?php if ($isEdit): ?>
                        <!-- Kolom Informasi Tambahan (Hanya terlihat sebagai read-only / detail rujukan di halaman edit admin) -->
                        <div class="border-top pt-3 mt-3">
                            <h6 class="font-weight-bold text-primary mb-3">Informasi Detail Profil Karyawan</h6>

                            <div class="form-group">
                                <label for="nik" class="font-weight-bold">NIK (Nomor Induk Kependudukan)</label>
                                <input type="text" class="form-control-plaintext bg-light pl-2" id="nik" value="<?= esc($karyawan['nik'] ?? '-') ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label for="npwp" class="font-weight-bold">NPWP</label>
                                <input type="text" class="form-control-plaintext bg-light pl-2" id="npwp" value="<?= esc($karyawan['npwp'] ?? '-') ?>" readonly>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="nama_bank" class="font-weight-bold">Nama Bank</label>
                                    <input type="text" class="form-control-plaintext bg-light pl-2" id="nama_bank" value="<?= esc($karyawan['nama_bank'] ?? '-') ?>" readonly>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="no_rekening" class="font-weight-bold">Nomor Rekening</label>
                                    <input type="text" class="form-control-plaintext bg-light pl-2" id="no_rekening" value="<?= esc($karyawan['no_rekening'] ?? '-') ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="no_telepon" class="font-weight-bold">No Telepon / WhatsApp</label>
                                <input type="text" class="form-control-plaintext bg-light pl-2" id="no_telepon" value="<?= esc($karyawan['no_telepon'] ?? '-') ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label for="alamat" class="font-weight-bold">Alamat Lengkap</label>
                                <textarea class="form-control-plaintext bg-light pl-2" id="alamat" rows="3" readonly><?= esc($karyawan['alamat'] ?? '-') ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="catatan_admin" class="font-weight-bold text-danger">Catatan Admin (Alasan Revisi/Penolakan)</label>
                                <textarea class="form-control border-danger text-danger" id="catatan_admin" name="catatan_admin" rows="2" placeholder="Masukkan alasan jika Anda menandai status di atas sebagai Butuh Revisi..."><?= esc(old('catatan_admin', $karyawan['catatan_admin'] ?? '')) ?></textarea>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary shadow-sm mr-1">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="<?= base_url('admin/karyawan') ?>" class="btn btn-secondary shadow-sm">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>