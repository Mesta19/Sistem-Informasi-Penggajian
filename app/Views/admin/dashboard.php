<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
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

<!-- Content Row -->
<?php if (session()->get('user.role') === 'Karyawan'): ?>
    <?php
    $statusKaryawan = $karyawanInfo['status'] ?? 'Belum Lengkap';
    $statusProfil = $karyawanInfo['status_profil'] ?? '';
    if (empty($statusProfil)) {
        if ($statusKaryawan === 'Menunggu Persetujuan') {
            $statusProfil = 'pending';
        } elseif ($statusKaryawan === 'Butuh Revisi') {
            $statusProfil = 'rejected';
        } elseif ($statusKaryawan === 'Aktif') {
            $statusProfil = 'approved';
        } else {
            $statusProfil = 'draft';
        }
    }
    $catatanAdmin = $karyawanInfo['catatan_admin'] ?? '';
    ?>

    <!-- 2 Kolom Layout Responsif: Kiri Riwayat & Info Dasar, Kanan Card Profil Mandiri -->
    <div class="row">
        <!-- Kolom Kiri (Desktop: 5/12, Mobile: 12/12) -->
        <div class="col-lg-5 col-md-12 mb-4">
            <!-- Informasi Akun & Dasar -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Karyawan</h6>
                    <?php if ($statusProfil === 'pending'): ?>
                        <span class="badge badge-warning text-dark p-2">Menunggu Persetujuan</span>
                    <?php elseif ($statusProfil === 'rejected'): ?>
                        <span class="badge badge-danger p-2">Ditolak</span>
                    <?php elseif ($statusProfil === 'approved'): ?>
                        <span class="badge badge-success p-2">Terverifikasi</span>
                    <?php else: ?>
                        <span class="badge badge-secondary p-2">Belum Lengkap</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($karyawanInfo)): ?>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th style="width: 45%">ID Karyawan</th>
                                <td>: <?= esc($karyawanInfo['id_karyawan']) ?></td>
                            </tr>
                            <tr>
                                <th>Nama Lengkap</th>
                                <td>: <?= esc($karyawanInfo['nama_karyawan']) ?></td>
                            </tr>
                            <tr>
                                <th>Jabatan</th>
                                <td>: <?= esc($karyawanInfo['nama_jabatan']) ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Masuk</th>
                                <td>: <?= date('d-m-Y', strtotime($karyawanInfo['tanggal_masuk'])) ?></td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning">Data Karyawan Anda belum dihubungkan. Hubungi Admin.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Riwayat Gaji -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Slip Gaji Anda</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Bulan/Tahun</th>
                                    <th>Gaji Bersih</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($gajiLatest)): ?>
                                    <?php foreach ($gajiLatest as $g): ?>
                                        <tr>
                                            <td><?= date('F', mktime(0, 0, 0, $g['bulan'], 10)) ?> <?= esc($g['tahun']) ?></td>
                                            <td>Rp <?= number_format($g['gaji_bersih'], 0, ',', '.') ?></td>
                                            <td>
                                                <a href="<?= base_url('admin/gaji/slip/' . $g['id_gaji']) ?>" class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fas fa-print"></i> Cetak
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada slip gaji yang diproses.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Card Profil Karyawan Mandiri (Desktop: 7/12, Mobile: 12/12) -->
        <div class="col-lg-7 col-md-12 mb-4">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-id-card mr-1"></i> Data Profil Karyawan
                    </h6>
                    <?php if ($statusProfil === 'pending'): ?>
                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock"></i> Menunggu Persetujuan</span>
                    <?php elseif ($statusProfil === 'approved'): ?>
                        <span class="badge badge-success px-2 py-1 bg-success text-white"><i class="fas fa-check text-white"></i> Terverifikasi</span>
                    <?php elseif ($statusProfil === 'rejected'): ?>
                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times"></i> Ditolak</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($karyawanInfo)): ?>
                        <!-- Info Status di ATAS Form -->
                        <?php if ($statusProfil === 'pending'): ?>
                            <div class="alert alert-warning">
                                Data profil Anda sedang menunggu persetujuan Admin.
                                Anda tetap bisa mengubah dan mengajukan ulang kapan saja.
                            </div>
                        <?php elseif ($statusProfil === 'rejected'): ?>
                            <div class="alert alert-danger">
                                Pengajuan sebelumnya ditolak.
                                <?php if (!empty($catatanAdmin)): ?>
                                    <br>Catatan Admin: <strong><?= esc($catatanAdmin) ?></strong>
                                <?php endif; ?>
                                <br>Silakan perbaiki data dan ajukan ulang.
                            </div>
                        <?php elseif ($statusProfil === 'approved'): ?>
                            <div class="alert alert-success">
                                Profil Anda telah diverifikasi.
                                Anda bisa mengajukan perubahan data kapan saja.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Silakan lengkapi data profil Anda, lalu klik Simpan dan Ajukan.
                            </div>
                        <?php endif; ?>

                        <!-- Form Pengisian & Pengajuan Mandiri (SELALU AKTIF) -->
                        <form action="<?= base_url('admin/dashboard/update-profile') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label for="nama_karyawan" class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_karyawan" name="nama_karyawan"
                                       value="<?= esc(old('nama_karyawan', $karyawanInfo['nama_karyawan'] ?? '')) ?>"
                                       placeholder="Masukkan nama lengkap Anda"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="nik" class="font-weight-bold">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nik" name="nik"
                                       value="<?= esc(old('nik', $karyawanInfo['nik'] ?? '')) ?>"
                                       maxlength="16" minlength="16" placeholder="Contoh: 3201234567890001"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       required>
                                <small class="form-text text-muted">Wajib 16 digit angka sesuai KTP.</small>
                            </div>

                            <div class="form-group">
                                <label for="npwp" class="font-weight-bold">Nomor Pokok Wajib Pajak (NPWP)</label>
                                <input type="text" class="form-control" id="npwp" name="npwp"
                                       value="<?= esc(old('npwp', $karyawanInfo['npwp'] ?? '')) ?>"
                                       placeholder="Contoh: 12.345.678.9-012.000">
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="nama_bank" class="font-weight-bold">Nama Bank <span class="text-danger">*</span></label>
                                    <select class="form-control" id="nama_bank" name="nama_bank" required>
                                        <option value="">-- Pilih Bank --</option>
                                        <?php
                                        $bankList = ['Bank BCA', 'Bank Mandiri', 'Bank BNI', 'Bank BRI', 'Bank CIMB Niaga', 'Bank Danamon', 'Bank Syariah Indonesia (BSI)', 'Bank Permata'];
                                        $currentBank = old('nama_bank', $karyawanInfo['nama_bank'] ?? '');
                                        ?>
                                        <?php foreach ($bankList as $b): ?>
                                            <option value="<?= $b ?>" <?= ($currentBank === $b) ? 'selected' : '' ?>><?= $b ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="no_rekening" class="font-weight-bold">Nomor Rekening <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="no_rekening" name="no_rekening"
                                           value="<?= esc(old('no_rekening', $karyawanInfo['no_rekening'] ?? '')) ?>"
                                           placeholder="Nomor rekening bank"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="no_telepon" class="font-weight-bold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                                       value="<?= esc(old('no_telepon', $karyawanInfo['no_telepon'] ?? '')) ?>"
                                       placeholder="Contoh: 081234567890"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="alamat" class="font-weight-bold">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3"
                                          placeholder="Tuliskan alamat domisili lengkap beserta RT/RW/Kelurahan/Kecamatan"
                                          required><?= esc(old('alamat', $karyawanInfo['alamat'] ?? '')) ?></textarea>
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> Simpan dan Ajukan Verifikasi
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">Data profil tidak dapat dimuat.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Dashboard Admin Tampilan Biasa -->
    <div class="row">
        <!-- Total Karyawan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Karyawan Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($totalKaryawan) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Gaji Bulan Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Gaji Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($totalGaji, 2, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slip Sudah Dibuat Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Slip Gaji Dibuat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($jumlahSlip) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jabatan Tersedia Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Jabatan Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($totalJabatan) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Tabel Karyawan Terbaru -->
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">5 Karyawan Terbaru</h6>
                    <a href="<?= base_url('admin/karyawan') ?>" class="btn btn-sm btn-primary">Lihat Semua Karyawan</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID Karyawan</th>
                                    <th>Nama Karyawan</th>
                                    <th>No Telepon</th>
                                    <th>Jabatan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($karyawanLatest)): ?>
                                    <?php foreach ($karyawanLatest as $k): ?>
                                        <tr>
                                            <td><?= esc($k['id_karyawan']) ?></td>
                                            <td><?= esc($k['nama_karyawan']) ?></td>
                                            <td><?= esc($k['no_telepon']) ?></td>
                                            <td><?= esc($k['nama_jabatan']) ?></td>
                                            <td><?= esc($k['tanggal_masuk']) ?></td>
                                            <td>
                                                <?php if ($k['status'] === 'Aktif'): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php elseif ($k['status'] === 'Menunggu Persetujuan'): ?>
                                                    <span class="badge badge-warning text-dark">Menunggu Persetujuan</span>
                                                <?php elseif ($k['status'] === 'Butuh Revisi'): ?>
                                                    <span class="badge badge-danger">Butuh Revisi</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><?= esc($k['status'] ?? 'Belum Lengkap') ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data karyawan terbaru.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
