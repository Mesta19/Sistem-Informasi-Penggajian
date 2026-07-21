<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Daftar Karyawan</h1>
    <a href="<?= base_url('admin/karyawan/persetujuan') ?>" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-list fa-sm text-white-50"></i> List Persetujuan
    </a>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Data Karyawan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 35%">Nama Karyawan</th>
                        <th style="width: 30%">Jabatan</th>
                        <th style="width: 15%">Status</th>
                        <th style="width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($karyawan)): ?>
                        <?php
                        $no = 1 + (10 * ((service('request')->getVar('page_karyawan') ?? 1) - 1));
                        foreach ($karyawan as $k):
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= esc($k['nama_karyawan'] ?: '(Belum Isi Profil)') ?></strong>
                                    <?php if (!empty($k['nama_bank'])): ?>
                                        <br><small class="text-muted"><i class="fas fa-university mr-1"></i><?= esc($k['nama_bank']) ?> - Rek. <?= esc($k['no_rekening']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="<?= base_url('admin/karyawan/update-jabatan/' . $k['id_karyawan']) ?>" method="post" class="d-flex align-items-center">
                                        <?= csrf_field() ?>
                                        <select name="id_jabatan" class="form-control form-control-sm mr-2" style="max-width: 180px;">
                                            <option value="">-- Pilih Jabatan --</option>
                                            <?php foreach ($jabatan as $j): ?>
                                                <option value="<?= $j['id_jabatan'] ?>" <?= ($k['id_jabatan'] == $j['id_jabatan']) ? 'selected' : '' ?>>
                                                    <?= esc($j['nama_jabatan']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-success shadow-sm" title="Simpan Jabatan">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <?php if ($k['status'] === 'Aktif'): ?>
                                        <span class="badge badge-success px-2 py-1">Aktif</span>
                                    <?php elseif ($k['status'] === 'Menunggu Persetujuan'): ?>
                                        <span class="badge badge-warning text-dark px-2 py-1">Menunggu Persetujuan</span>
                                    <?php elseif ($k['status'] === 'Butuh Revisi'): ?>
                                        <span class="badge badge-danger px-2 py-1" title="<?= esc($k['catatan_admin']) ?>">Butuh Revisi</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1"><?= esc($k['status'] ?: 'Belum Lengkap') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap">
                                        <a href="<?= base_url('admin/karyawan/edit/' . $k['id_karyawan']) ?>" class="btn btn-sm btn-warning mr-1 mb-1 shadow-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <a href="<?= base_url('admin/karyawan/delete/' . $k['id_karyawan']) ?>" class="btn btn-sm btn-danger btn-delete mb-1 shadow-sm" data-message="Apakah Anda yakin ingin menghapus karyawan ini?">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data karyawan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (isset($pager)): ?>
            <div class="mt-3">
                <?= $pager->links('karyawan', 'bootstrap_pagination') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
