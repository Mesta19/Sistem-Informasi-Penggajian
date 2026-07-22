<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Proses Gaji Massal</h1>
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
        <h6 class="m-0 font-weight-bold text-primary">Proses Slip Gaji Massal Karyawan Aktif</h6>
    </div>
    <div class="card-body">
        <?php if (empty($karyawan)): ?>
            <div class="alert alert-info">
                Tidak ada karyawan aktif yang tersedia untuk diproses.
            </div>
        <?php else: ?>
            <form action="<?= base_url('admin/gaji/massal/proses') ?>" method="post" id="form-massal">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6">
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
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tahun">Tahun Periode</label>
                            <select class="form-control" id="tahun" name="tahun" required>
                                <option value="">-- Pilih Tahun --</option>
                                <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>
                                    <option value="<?= $y ?>" <?= ($y == date('Y')) ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="mb-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-gray-800 font-weight-bold">Daftar Karyawan</h5>
                    <div class="custom-control custom-checkbox bg-light p-2 rounded border">
                        <input type="checkbox" class="custom-control-input" id="check-all-employees">
                        <label class="custom-control-label font-weight-bold text-gray-800" for="check-all-employees" style="cursor: pointer;">Pilih Semua Karyawan</label>
                    </div>
                </div>

                <div class="accordion" id="accordionKaryawan">
                    <?php foreach ($karyawan as $index => $k): ?>
                        <div class="card mb-3 border-left-primary shadow-sm employee-card">
                            <!-- Accordion Header -->
                            <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between" id="heading_<?= $k['id_karyawan'] ?>">
                                <div class="custom-control custom-checkbox mr-3">
                                    <input type="checkbox" class="custom-control-input checkbox-ikutkan" id="ikutkan_<?= $k['id_karyawan'] ?>" name="karyawan[<?= $k['id_karyawan'] ?>][ikutkan]" value="1">
                                    <label class="custom-control-label font-weight-bold text-gray-800" for="ikutkan_<?= $k['id_karyawan'] ?>">
                                        <?= esc($k['nama_karyawan']) ?> (<?= esc($k['id_karyawan'] ) ?>) - <span class="text-primary"><?= esc($k['nama_jabatan'] ?: 'Belum Ada Jabatan') ?></span>
                                    </label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-secondary mr-3 status-badge" id="status_badge_<?= $k['id_karyawan'] ?>" data-karyawan-id="<?= $k['id_karyawan'] ?>">Belum diisi</span>
                                    <button class="btn btn-link btn-sm text-decoration-none collapsed" type="button" data-toggle="collapse" data-target="#collapse_<?= $k['id_karyawan'] ?>" aria-expanded="false" aria-controls="collapse_<?= $k['id_karyawan'] ?>">
                                        <i class="fas fa-chevron-down"></i> Detail
                                    </button>
                                </div>
                            </div>

                            <!-- Accordion Body -->
                            <div id="collapse_<?= $k['id_karyawan'] ?>" class="collapse" aria-labelledby="heading_<?= $k['id_karyawan'] ?>" data-parent="#accordionKaryawan">
                                <div class="card-body bg-white">
                                    <!-- Input Kehadiran Manual -->
                                    <h6 class="font-weight-bold text-info mb-3"><i class="fas fa-calendar-check"></i> Input Kehadiran</h6>
                                    <div class="row mb-3 input-kehadiran-group" data-karyawan-id="<?= $k['id_karyawan'] ?>">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Hari Hadir</label>
                                                <input type="number" class="form-control input-kehadiran" name="karyawan[<?= $k['id_karyawan'] ?>][hari_hadir]" min="0" max="31" value="26">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Sakit (Hari)</label>
                                                <input type="number" class="form-control input-kehadiran" name="karyawan[<?= $k['id_karyawan'] ?>][hari_sakit]" min="0" max="31" value="0">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Izin (Hari)</label>
                                                <input type="number" class="form-control input-kehadiran" name="karyawan[<?= $k['id_karyawan'] ?>][hari_izin]" min="0" max="31" value="0">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Alpha (Hari)</label>
                                                <input type="number" class="form-control input-kehadiran" name="karyawan[<?= $k['id_karyawan'] ?>][hari_alpha]" min="0" max="31" value="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Tunjangan -->
                                        <div class="col-md-6">
                                            <div class="card mb-3 border-left-success">
                                                <div class="card-header bg-light font-weight-bold text-success py-2">
                                                    Pilih Tunjangan (Penambah)
                                                </div>
                                                <div class="card-body">
                                                    <?php $hasTunjangan = false; foreach ($komponen as $comp): if ($comp['jenis'] === 'Tunjangan'): $hasTunjangan = true; ?>
                                                        <div class="form-row align-items-center mb-2">
                                                            <div class="col-auto">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input checkbox-komponen" name="karyawan[<?= $k['id_karyawan'] ?>][komponen_gaji][]" value="<?= $comp['id_komponen'] ?>" id="comp_t_<?= $k['id_karyawan'] ?>_<?= $comp['id_komponen'] ?>" onchange="toggleQtyInputMassal(this, 'qty_t_<?= $k['id_karyawan'] ?>_<?= $comp['id_komponen'] ?>')">
                                                                    <label class="custom-control-label font-weight-bold" for="comp_t_<?= $k['id_karyawan'] ?>_<?= $comp['id_komponen'] ?>"><?= esc($comp['nama_komponen']) ?></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3 ml-auto">
                                                                <div class="input-group input-group-sm">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">x</span>
                                                                    </div>
                                                                    <input type="number" class="form-control" name="karyawan[<?= $k['id_karyawan'] ?>][qty_komponen][<?= $comp['id_komponen'] ?>]" id="qty_t_<?= $k['id_karyawan'] ?>_<?= $comp['id_komponen'] ?>" value="1" min="1" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <span class="text-muted small">@ Rp <?= number_format($comp['nilai'], 0, ',', '.') ?></span>
                                                            </div>
                                                        </div>
                                                    <?php endif; endforeach; if (!$hasTunjangan): ?>
                                                        <p class="text-muted small mb-0">Belum ada komponen tunjangan.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Potongan -->
                                        <div class="col-md-6">
                                            <div class="card mb-3 border-left-danger">
                                                <div class="card-header bg-light font-weight-bold text-danger py-2">
                                                    Pilih Potongan (Pengurang)
                                                </div>
                                                <div class="card-body">
                                                    <?php $hasPotongan = false; foreach ($komponen as $comp): if ($comp['jenis'] === 'Potongan'): $hasPotongan = true; ?>
                                                        <div class="form-row align-items-center mb-2">
                                                            <div class="col-auto">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input checkbox-komponen" name="karyawan[<?= $k['id_karyawan'] ?>][komponen_gaji][]" value="<?= $comp['id_komponen'] ?>" id="comp_p_<?= $k['id_karyawan'] ?>_<?= $comp['id_komponen'] ?>" onchange="toggleQtyInputMassal(this, 'qty_p_<?= $k['id_karyawan'] ?>_<?= $comp['id_komponen'] ?>')">
                                                                    <label class="custom-control-label font-weight-bold" for="comp_p_<?= $k['id_karyawan'] ?>_<?= $comp['id_komponen'] ?>"><?= esc($comp['nama_komponen']) ?></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3 ml-auto">
                                                                <div class="input-group input-group-sm">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">x</span>
                                                                    </div>
                                                                    <input type="number" class="form-control" name="karyawan[<?= $k['id_karyawan'] ?>][qty_komponen][<?= $comp['id_komponen'] ?>]" id="qty_p_<?= $k['id_karyawan'] ?>_<?= $comp['id_komponen'] ?>" value="1" min="1" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <span class="text-muted small">@ Rp <?= number_format($comp['nilai'], 0, ',', '.') ?></span>
                                                            </div>
                                                        </div>
                                                    <?php endif; endforeach; if (!$hasPotongan): ?>
                                                        <p class="text-muted small mb-0">Belum ada komponen potongan.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-warning font-weight-bold">Proses Semua Gaji</button>
                    <a href="<?= base_url('admin/gaji') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleQtyInputMassal(checkbox, inputId) {
    var input = document.getElementById(inputId);
    if (checkbox.checked) {
        input.removeAttribute('disabled');
    } else {
        input.setAttribute('disabled', 'disabled');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    let csrfToken = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    function updateCsrf(token, hash) {
        csrfToken = token;
        csrfHash = hash;
        document.querySelectorAll('input[name="' + csrfToken + '"]').forEach(function(input) {
            input.value = csrfHash;
        });
    }

    // Ajax check for paid status
    function updateSalaryStatusBadges() {
        const bulan = document.getElementById('bulan').value;
        const tahun = document.getElementById('tahun').value;
        if (!bulan || !tahun) return;

        let requestData = {
            bulan: bulan,
            tahun: tahun,
            karyawan_ids: [] // Check all active
        };
        requestData[csrfToken] = csrfHash;

        $.ajax({
            url: '<?= base_url("admin/gaji/check-pembayaran") ?>',
            method: 'POST',
            data: requestData,
            success: function(response) {
                if (response.status === 'success') {
                    if (response.csrf_token && response.csrf_hash) {
                        updateCsrf(response.csrf_token, response.csrf_hash);
                    }
                    const sudahBayarIds = response.sudah_bayar.map(item => item.id_karyawan);

                    document.querySelectorAll('.status-badge').forEach(function(badge) {
                        const kid = badge.getAttribute('data-karyawan-id');
                        const checkboxIkutkan = document.getElementById('ikutkan_' + kid);

                        if (sudahBayarIds.includes(kid)) {
                            badge.textContent = 'Sudah dibayar';
                            badge.className = 'badge badge-danger mr-3 status-badge';
                            // Uncheck and disable the checkbox for already paid
                            if (checkboxIkutkan) {
                                checkboxIkutkan.checked = false;
                                checkboxIkutkan.disabled = true;
                                // Add styling to indicate disabled row
                                checkboxIkutkan.closest('.employee-card').style.opacity = '0.7';
                            }
                        } else {
                            if (checkboxIkutkan && checkboxIkutkan.disabled) {
                                checkboxIkutkan.disabled = false;
                                checkboxIkutkan.closest('.employee-card').style.opacity = '1';
                            }

                            // Re-evaluate badge status based on checkboxIkutkan
                            if (checkboxIkutkan && checkboxIkutkan.checked) {
                                badge.textContent = 'Sudah diisi';
                                badge.className = 'badge badge-success mr-3 status-badge';
                            } else {
                                badge.textContent = 'Belum diisi';
                                badge.className = 'badge badge-secondary mr-3 status-badge';
                            }
                        }
                    });
                }
            }
        });
    }

    // Trigger status check on period change
    document.getElementById('bulan').addEventListener('change', updateSalaryStatusBadges);
    document.getElementById('tahun').addEventListener('change', updateSalaryStatusBadges);

    // Initial run
    updateSalaryStatusBadges();

    // Otomatis centang checkbox "ikutkan" jika input di dalam card diubah/diisi
    const employeeCards = document.querySelectorAll('.employee-card');
    employeeCards.forEach(function (card) {
        const checkboxIkutkan = card.querySelector('.checkbox-ikutkan');
        const statusBadge = card.querySelector('.status-badge');
        if (!checkboxIkutkan) return;

        const inputs = card.querySelectorAll('input.input-kehadiran, input.checkbox-komponen');
        inputs.forEach(function (input) {
            input.addEventListener('change', function () {
                if (checkboxIkutkan.disabled) return;
                // Saat ada input diubah, auto centang checkbox ikutkan
                checkboxIkutkan.checked = true;

                // Ubah status badge ke Sudah diisi
                statusBadge.textContent = 'Sudah diisi';
                statusBadge.className = 'badge badge-success mr-3 status-badge';
            });
        });

        // Event listener saat checkbox utama diubah secara manual
        checkboxIkutkan.addEventListener('change', function () {
            if (checkboxIkutkan.checked) {
                statusBadge.textContent = 'Sudah diisi';
                statusBadge.className = 'badge badge-success mr-3 status-badge';
            } else {
                statusBadge.textContent = 'Belum diisi';
                statusBadge.className = 'badge badge-secondary mr-3 status-badge';
            }
        });
    });

    // Validasi submit form massal
    const formMassal = document.getElementById('form-massal');
    if (formMassal) {
        formMassal.addEventListener('submit', function (e) {
            e.preventDefault(); // Hentikan submit bawaan untuk di-handle SweetAlert2

            const checkedKaryawan = document.querySelectorAll('.checkbox-ikutkan:checked:not(:disabled)');
            if (checkedKaryawan.length === 0) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Silakan centang minimal satu karyawan yang belum diproses.',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            const karyawanIds = Array.from(checkedKaryawan).map(cb => cb.id.replace('ikutkan_', ''));
            const bulan = document.getElementById('bulan').value;
            const tahun = document.getElementById('tahun').value;

            let requestData = {
                bulan: bulan,
                tahun: tahun,
                karyawan_ids: karyawanIds
            };
            requestData[csrfToken] = csrfHash;

            // Fetch latest status via AJAX right before submit to double check
            $.ajax({
                url: '<?= base_url("admin/gaji/check-pembayaran") ?>',
                method: 'POST',
                data: requestData,
                success: function(response) {
                    if (response.status === 'success') {
                        if (response.csrf_token && response.csrf_hash) {
                            updateCsrf(response.csrf_token, response.csrf_hash);
                        }
                        const diproses = response.belum_bayar;
                        const dilewati = response.sudah_bayar;

                        if (diproses.length === 0) {
                            Swal.fire({
                                title: 'Informasi',
                                text: 'Tidak ada karyawan baru yang dapat diproses (semua yang dipilih sudah digaji pada periode ini).',
                                icon: 'info',
                                confirmButtonColor: '#3085d6'
                            });
                            return;
                        }

                        let htmlContent = '<div class="text-left font-weight-normal">';

                        htmlContent += '<h6 class="font-weight-bold text-success">Karyawan yang akan diproses (' + diproses.length + '):</h6>';
                        htmlContent += '<ul class="pl-3 mb-3">';
                        diproses.forEach(function(k) {
                            htmlContent += '<li>' + k.nama_karyawan + ' (' + k.id_karyawan + ')</li>';
                        });
                        htmlContent += '</ul>';

                        if (dilewati.length > 0) {
                            htmlContent += '<h6 class="font-weight-bold text-danger">Karyawan yang dilewati (Sudah digaji) (' + dilewati.length + '):</h6>';
                            htmlContent += '<ul class="pl-3 text-muted small">';
                            dilewati.forEach(function(k) {
                                htmlContent += '<li>' + k.nama_karyawan + ' (' + k.id_karyawan + ')</li>';
                            });
                            htmlContent += '</ul>';
                        }

                        htmlContent += '</div>';

                        Swal.fire({
                            title: 'Konfirmasi Proses Gaji Massal',
                            html: htmlContent,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#ffc107',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Proses!',
                            cancelButtonText: 'Batal',
                            customClass: {
                                htmlContainer: 'my-swal-html'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Disable the already paid checkboxes before submitting so they are not sent to server
                                document.querySelectorAll('.checkbox-ikutkan:disabled').forEach(function(cb) {
                                    cb.checked = false;
                                });
                                formMassal.submit();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal memvalidasi status pembayaran.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghubungkan ke server.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });
    }

    // Checkbox Pilih Semua Karyawan
    const checkAll = document.getElementById('check-all-employees');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.checkbox-ikutkan:not(:disabled)');
            checkboxes.forEach(function (cb) {
                cb.checked = checkAll.checked;
                // Memicu event change manual agar status badge ikut berubah
                cb.dispatchEvent(new Event('change'));
            });
        });
    }
});
</script>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
