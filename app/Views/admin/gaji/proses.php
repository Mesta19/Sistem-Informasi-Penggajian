<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Proses Gaji</h1>
    <a href="<?= base_url('admin/gaji') ?>" class="btn btn-sm btn-secondary shadow-sm">
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
        <h6 class="m-0 font-weight-bold text-primary">Hitung & Proses Slip Gaji</h6>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/gaji/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="id_karyawan">Pilih Karyawan</label>
                <select class="form-control" id="id_karyawan" name="id_karyawan" required>
                    <option value="">-- Pilih Karyawan --</option>
                    <?php foreach ($karyawan as $k): ?>
                        <option value="<?= $k['id_karyawan'] ?>" <?= (old('id_karyawan') === $k['id_karyawan']) ? 'selected' : '' ?>>
                            <?= esc($k['nama_karyawan']) ?> (<?= esc($k['id_karyawan']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="bulan">Bulan Periode</label>
                <select class="form-control" id="bulan" name="bulan" required>
                    <option value="">-- Pilih Bulan --</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($m == date('m')) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="tahun">Tahun Periode</label>
                <select class="form-control" id="tahun" name="tahun" required>
                    <option value="">-- Pilih Tahun --</option>
                    <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?= $y ?>" <?= ($y == date('Y')) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Input Kehadiran Manual -->
            <div class="card mb-4 border-left-info shadow">
                <div class="card-header bg-light py-2">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-calendar-check"></i> Input Kehadiran (Manual)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="hari_hadir">Hari Hadir</label>
                                <input type="number" class="form-control" id="hari_hadir" name="hari_hadir" min="0" max="31" value="26" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="hari_sakit">Sakit (Hari)</label>
                                <input type="number" class="form-control" id="hari_sakit" name="hari_sakit" min="0" max="31" value="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="hari_izin">Izin (Hari)</label>
                                <input type="number" class="form-control" id="hari_izin" name="hari_izin" min="0" max="31" value="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="hari_alpha">Alpha (Hari)</label>
                                <input type="number" class="form-control" id="hari_alpha" name="hari_alpha" min="0" max="31" value="0" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-success">Pilih Tunjangan (Penambah)</h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $hasTunjangan = false;
                            foreach ($komponen as $k):
                                if ($k['jenis'] === 'Tunjangan'):
                                    $hasTunjangan = true;
                            ?>
                                    <div class="form-row align-items-center mb-3">
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="komponen_gaji[]" value="<?= $k['id_komponen'] ?>" id="komp_<?= $k['id_komponen'] ?>" onchange="toggleQtyInput(this, 'qty_<?= $k['id_komponen'] ?>')">
                                                <label class="form-check-label font-weight-bold" for="komp_<?= $k['id_komponen'] ?>">
                                                    <?= esc($k['nama_komponen']) ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">x</span>
                                                </div>
                                                <input type="number" class="form-control" name="qty_komponen[<?= $k['id_komponen'] ?>]" id="qty_<?= $k['id_komponen'] ?>" value="1" min="1" disabled>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="text-muted small">@ Rp <?= number_format($k['nilai'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                            <?php
                                endif;
                            endforeach;
                            if (!$hasTunjangan):
                            ?>
                                <p class="text-muted small mb-0">Belum ada komponen tunjangan.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-danger">Pilih Potongan (Pengurang)</h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $hasPotongan = false;
                            foreach ($komponen as $k):
                                if ($k['jenis'] === 'Potongan'):
                                    $hasPotongan = true;
                            ?>
                                    <div class="form-row align-items-center mb-3">
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="komponen_gaji[]" value="<?= $k['id_komponen'] ?>" id="komp_<?= $k['id_komponen'] ?>" onchange="toggleQtyInput(this, 'qty_<?= $k['id_komponen'] ?>')">
                                                <label class="form-check-label font-weight-bold" for="komp_<?= $k['id_komponen'] ?>">
                                                    <?= esc($k['nama_komponen']) ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">x</span>
                                                </div>
                                                <input type="number" class="form-control" name="qty_komponen[<?= $k['id_komponen'] ?>]" id="qty_<?= $k['id_komponen'] ?>" value="1" min="1" disabled>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="text-muted small">@ Rp <?= number_format($k['nilai'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                            <?php
                                endif;
                            endforeach;
                            if (!$hasPotongan):
                            ?>
                                <p class="text-muted small mb-0">Belum ada komponen potongan.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            function toggleQtyInput(checkbox, inputId) {
                var input = document.getElementById(inputId);
                if (checkbox.checked) {
                    input.removeAttribute('disabled');
                } else {
                    input.setAttribute('disabled', 'disabled');
                }
            }
            </script>

            <button type="submit" class="btn btn-success">Proses Gaji</button>
            <a href="<?= base_url('admin/gaji') ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
