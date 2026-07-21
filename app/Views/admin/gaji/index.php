<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Daftar Gaji</h1>
    <a href="<?= base_url('admin/gaji/proses') ?>" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-cogs fa-sm text-white-50"></i> Proses Gaji Baru
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

<!-- Filter Card -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('admin/gaji') ?>" class="form-inline">
            <div class="form-group mr-3">
                <label for="bulan" class="mr-2">Bulan:</label>
                <select class="form-control" id="bulan" name="bulan">
                    <option value="">-- Semua Waktu --</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($bulan !== null && $m == $bulan) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group mr-3">
                <label for="tahun" class="mr-2">Tahun:</label>
                <select class="form-control" id="tahun" name="tahun">
                    <option value="">-- Semua Waktu --</option>
                    <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?= $y ?>" <?= ($tahun !== null && $y == $tahun) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group mr-3">
                <label for="sort" class="mr-2">Urutan:</label>
                <select class="form-control" id="sort" name="sort">
                    <option value="waktu" <?= ($sort === 'waktu') ? 'selected' : '' ?>>Waktu terbaru</option>
                    <option value="terkecil" <?= ($sort === 'terkecil') ? 'selected' : '' ?>>Nominal Gaji Terkecil</option>
                    <option value="terbesar" <?= ($sort === 'terbesar') ? 'selected' : '' ?>>Nominal Gaji Terbesar</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mr-2">Filter</button>
            <a href="<?= base_url('admin/gaji') ?>" class="btn btn-secondary">Reset Filter</a>
        </form>
    </div>
</div>

<!-- Gaji Table Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Data Slip Gaji Karyawan
            (Periode:
            <?php
            if ($bulan !== null && $tahun !== null) {
                echo date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun));
            } elseif ($bulan !== null) {
                echo date('F', mktime(0, 0, 0, $bulan, 1)) . ' (Semua Tahun)';
            } elseif ($tahun !== null) {
                echo 'Tahun ' . $tahun;
            } else {
                echo 'Semua Waktu';
            }
            ?>)
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID Gaji</th>
                        <th>Nama Karyawan</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Potongan</th>
                        <th>Gaji Bersih</th>
                        <th>Tanggal Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($gaji)): ?>
                        <?php foreach ($gaji as $g): ?>
                            <tr>
                                <td><?= esc($g['id_gaji']) ?></td>
                                <td><?= esc($g['nama_karyawan']) ?></td>
                                <td>Rp <?= number_format($g['gaji_pokok'], 2, ',', '.') ?></td>
                                <td>Rp <?= number_format($g['total_tunjangan'], 2, ',', '.') ?></td>
                                <td>Rp <?= number_format($g['total_potongan'], 2, ',', '.') ?></td>
                                <td>Rp <?= number_format($g['gaji_bersih'], 2, ',', '.') ?></td>
                                <td><?= esc($g['tanggal_bayar']) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/gaji/slip/' . $g['id_gaji']) ?>" class="btn btn-sm btn-info mr-1 mb-1">
                                        <i class="fas fa-file-invoice-dollar"></i> Slip Gaji
                                    </a>
                                    <a href="<?= base_url('admin/gaji/delete/' . $g['id_gaji']) ?>" class="btn btn-sm btn-danger mb-1 btn-delete" data-message="Apakah Anda yakin ingin menghapus slip gaji ini?">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data slip gaji untuk periode ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (isset($pager)): ?>
            <div class="mt-3">
                <?= $pager->links('gaji', 'bootstrap_pagination') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
