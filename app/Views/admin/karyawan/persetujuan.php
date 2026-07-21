<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">List Persetujuan Karyawan</h1>
    <a href="<?= base_url('admin/karyawan') ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar Karyawan
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

<!-- Filter Tabs -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-left-info">
            <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between">
                <div>
                    <span class="font-weight-bold text-dark mr-2"><i class="fas fa-filter mr-1 text-info"></i> Status Profil:</span>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Filter status">
                        <a href="<?= base_url('admin/karyawan/persetujuan?status=semua') ?>" class="btn btn-outline-info <?= $status_filter === 'semua' ? 'active' : '' ?>">Semua</a>
                        <a href="<?= base_url('admin/karyawan/persetujuan?status=pending') ?>" class="btn btn-outline-info <?= $status_filter === 'pending' ? 'active' : '' ?>">Menunggu (Pending)</a>
                        <a href="<?= base_url('admin/karyawan/persetujuan?status=approved') ?>" class="btn btn-outline-info <?= $status_filter === 'approved' ? 'active' : '' ?>">Disetujui</a>
                        <a href="<?= base_url('admin/karyawan/persetujuan?status=rejected') ?>" class="btn btn-outline-info <?= $status_filter === 'rejected' ? 'active' : '' ?>">Ditolak</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Profil</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <th>NIK</th>
                        <th>No. Telepon</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th style="width: 25%">Aksi</th>
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
                                <td><code><?= esc($k['username'] ?? '-') ?></code></td>
                                <td><strong><?= esc($k['nama_karyawan']) ?></strong></td>
                                <td><?= esc($k['nama_jabatan']) ?></td>
                                <td><?= esc($k['nik'] ?? '-') ?></td>
                                <td><?= esc($k['no_telepon'] ?? '-') ?></td>
                                <td><?= $k['tgl_pengajuan'] ? date('d-m-Y H:i', strtotime($k['tgl_pengajuan'])) : '-' ?></td>
                                <td>
                                    <?php if ($k['status_profil'] === 'approved'): ?>
                                        <span class="badge badge-success">Disetujui</span>
                                    <?php elseif ($k['status_profil'] === 'pending'): ?>
                                        <span class="badge badge-warning text-dark">Menunggu</span>
                                    <?php elseif ($k['status_profil'] === 'rejected'): ?>
                                        <span class="badge badge-danger" title="<?= esc($k['catatan_admin']) ?>">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Draft / Kosong</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap">
                                        <button type="button" class="btn btn-sm btn-info mr-1 mb-1 shadow-sm"
                                                data-toggle="modal" data-target="#modalDetail-<?= esc($k['id_karyawan']) ?>">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>

                                        <?php if ($k['status_profil'] === 'pending'): ?>
                                            <form action="<?= base_url('admin/karyawan/setujui/' . $k['id_karyawan']) ?>" method="post" class="mr-1 mb-1">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success shadow-sm btn-approve-confirm" data-message="Apakah Anda yakin ingin menyetujui pengajuan profil <?= esc($k['nama_karyawan']) ?>?">
                                                    <i class="fas fa-check"></i> Setujui
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-sm btn-danger mb-1 shadow-sm"
                                                    data-toggle="modal" data-target="#modalTolak-<?= esc($k['id_karyawan']) ?>">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Tidak ada pengajuan profil.</td>
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

<!-- Modals for details and rejection -->
<?php if (!empty($karyawan)): ?>
    <?php foreach ($karyawan as $k): ?>
        <!-- Modal Detail -->
        <div class="modal fade" id="modalDetail-<?= esc($k['id_karyawan']) ?>" tabindex="-1" role="dialog" aria-labelledby="detailLabel-<?= esc($k['id_karyawan']) ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-weight-bold text-primary" id="detailLabel-<?= esc($k['id_karyawan']) ?>">
                            <i class="fas fa-id-card mr-1"></i> Detail Profil: <?= esc($k['nama_karyawan']) ?>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped table-sm mb-0">
                            <tr>
                                <th style="width: 35%">ID Karyawan</th>
                                <td><?= esc($k['id_karyawan']) ?></td>
                            </tr>
                            <tr>
                                <th>Username / Akun</th>
                                <td><?= esc($k['username'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Nama Lengkap</th>
                                <td><?= esc($k['nama_karyawan']) ?></td>
                            </tr>
                            <tr>
                                <th>Jabatan</th>
                                <td><?= esc($k['nama_jabatan']) ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Masuk</th>
                                <td><?= date('d-m-Y', strtotime($k['tanggal_masuk'])) ?></td>
                            </tr>
                            <tr>
                                <th>NIK</th>
                                <td><?= esc($k['nik'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>NPWP</th>
                                <td><?= esc($k['npwp'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Nama Bank</th>
                                <td><?= esc($k['nama_bank'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Nomor Rekening</th>
                                <td><?= esc($k['no_rekening'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>No. Telepon / WA</th>
                                <td><?= esc($k['no_telepon'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td><?= nl2br(esc($k['alamat'] ?? '-')) ?></td>
                            </tr>
                            <?php if ($k['status_profil'] === 'rejected'): ?>
                                <tr class="table-danger">
                                    <th>Catatan Penolakan</th>
                                    <td class="text-danger font-weight-bold"><?= nl2br(esc($k['catatan_admin'])) ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tolak -->
        <?php if ($k['status_profil'] === 'pending'): ?>
            <div class="modal fade" id="modalTolak-<?= esc($k['id_karyawan']) ?>" tabindex="-1" role="dialog" aria-labelledby="tolakLabel-<?= esc($k['id_karyawan']) ?>" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title font-weight-bold text-danger" id="tolakLabel-<?= esc($k['id_karyawan']) ?>">
                                <i class="fas fa-times-circle mr-1"></i> Tolak Pengajuan Profil
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= base_url('admin/karyawan/tolak/' . $k['id_karyawan']) ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="modal-body">
                                <p>Anda akan menolak pengajuan profil untuk <strong><?= esc($k['nama_karyawan']) ?></strong>.</p>
                                <div class="form-group">
                                    <label for="catatan_admin-<?= esc($k['id_karyawan']) ?>" class="font-weight-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="catatan_admin-<?= esc($k['id_karyawan']) ?>" name="catatan_admin" rows="3" placeholder="Contoh: Nomor NIK tidak sesuai atau scan NPWP salah..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger btn-sm">Tolak & Minta Revisi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event listener untuk tombol aksi konfirmasi biasa (jika ada)
    const confirmButtons = document.querySelectorAll('.btn-action-confirm');
    confirmButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const message = this.getAttribute('data-message') || 'Apakah Anda yakin?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // SweetAlert2 untuk tombol Setujui
    const approveButtons = document.querySelectorAll('.btn-approve-confirm');
    approveButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const message = this.getAttribute('data-message') || 'Apakah Anda yakin ingin menyetujui pengajuan profil ini?';
            Swal.fire({
                title: 'Konfirmasi Setujui',
                text: message,
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
