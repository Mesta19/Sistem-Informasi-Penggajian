<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<style>
@media print {
    /* Sembunyikan sidebar, topbar, footer, dan tombol kembali saat cetak */
    #accordionSidebar,
    .topbar,
    .sticky-footer,
    .d-print-none,
    .scroll-to-top {
        display: none !important;
    }
    /* Atur layout content agar memenuhi halaman */
    #content-wrapper {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    #wrapper {
        display: block !important;
    }
    .card {
        border: 0 !important;
        box-shadow: none !important;
    }
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 d-print-none">
    <h1 class="h3 mb-0 text-gray-800">Slip Gaji Karyawan</h1>
    <div>
        <button onclick="window.print()" class="btn btn-sm btn-success shadow-sm mr-2">
            <i class="fas fa-print fa-sm text-white-50"></i> Cetak Slip
        </button>
        <a href="<?= base_url('admin/gaji') ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show d-print-none" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Slip Gaji Layout -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="text-center mb-4">
            <h2 class="font-weight-bold">SLIP GAJI KARYAWAN</h2>
            <h5 class="text-secondary">SISTEM INFORMASI PENGGAJIAN</h5>
            <hr>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="35%">ID Karyawan</td>
                        <td width="5%">:</td>
                        <td><strong><?= esc($gaji['id_karyawan']) ?></strong></td>
                    </tr>
                    <tr>
                        <td>Nama Karyawan</td>
                        <td>:</td>
                        <td><?= esc($gaji['nama_karyawan']) ?></td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td><?= esc($gaji['nama_jabatan']) ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="35%">Bulan / Tahun</td>
                        <td width="5%">:</td>
                        <td><?= date('F', mktime(0, 0, 0, $gaji['bulan'], 1)) ?> / <?= esc($gaji['tahun']) ?></td>
                    </tr>
                    <tr>
                        <td>Hari Hadir (Absen)</td>
                        <td>:</td>
                        <td>
                            Hadir: <?= esc($gaji['hari_hadir']) ?> Hari |
                            Sakit: <?= esc($gaji['hari_sakit'] ?? 0) ?> Hari |
                            Izin: <?= esc($gaji['hari_izin'] ?? 0) ?> Hari |
                            Alpha: <?= esc($gaji['hari_alpha']) ?> Hari
                        </td>
                    </tr>
                    <tr>
                        <td>Tanggal Terbit</td>
                        <td>:</td>
                        <td><?= esc($gaji['tanggal_bayar']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <!-- Tunjangan -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-success text-white py-2">
                        <h6 class="m-0 font-weight-bold">Tunjangan (Penerimaan)</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Komponen</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $hasTunjangan = false;
                                foreach ($detail as $d):
                                    // Gunakan data snapshot jika ada di array hasil query
                                    $nama_komponen = isset($d['nama_snapshot']) && $d['nama_snapshot'] !== null ? $d['nama_snapshot'] : (isset($d['nama_komponen']) ? $d['nama_komponen'] : '');
                                    $jenis = isset($d['jenis_snapshot']) && $d['jenis_snapshot'] !== null ? $d['jenis_snapshot'] : (isset($d['jenis']) ? $d['jenis'] : '');
                                    $nilai_satuan = isset($d['satuan_snapshot']) && (float)$d['satuan_snapshot'] > 0 ? (float)$d['satuan_snapshot'] : (isset($d['nilai_satuan']) ? (float)$d['nilai_satuan'] : (float)$d['nilai']);
                                    $qty = isset($d['qty_snapshot']) ? (int)$d['qty_snapshot'] : 1;

                                    if ($jenis === 'Tunjangan'):
                                        $hasTunjangan = true;
                                        $qtyText = '';
                                        if ($qty > 1) {
                                            $qtyText = ' <span class="text-muted">(' . $qty . 'x Rp ' . number_format($nilai_satuan, 0, ',', '.') . ')</span>';
                                        } elseif ($nilai_satuan > 0 && (float)$d['nilai'] !== $nilai_satuan) {
                                            $calcQty = round((float)$d['nilai'] / $nilai_satuan);
                                            $qtyText = ' <span class="text-muted">(' . $calcQty . 'x Rp ' . number_format($nilai_satuan, 0, ',', '.') . ')</span>';
                                        }
                                ?>
                                    <tr>
                                        <td><?= esc($nama_komponen) ?><?= $qtyText ?></td>
                                        <td class="text-right">Rp <?= number_format($d['nilai'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php
                                    endif;
                                endforeach;
                                if (!$hasTunjangan):
                                ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Tidak ada tunjangan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Potongan -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-danger text-white py-2">
                        <h6 class="m-0 font-weight-bold">Potongan (Pengurangan)</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Komponen</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $hasPotongan = false;
                                foreach ($detail as $d):
                                    $nama_komponen = isset($d['nama_snapshot']) && $d['nama_snapshot'] !== null ? $d['nama_snapshot'] : (isset($d['nama_komponen']) ? $d['nama_komponen'] : '');
                                    $jenis = isset($d['jenis_snapshot']) && $d['jenis_snapshot'] !== null ? $d['jenis_snapshot'] : (isset($d['jenis']) ? $d['jenis'] : '');
                                    $nilai_satuan = isset($d['satuan_snapshot']) && (float)$d['satuan_snapshot'] > 0 ? (float)$d['satuan_snapshot'] : (isset($d['nilai_satuan']) ? (float)$d['nilai_satuan'] : (float)$d['nilai']);
                                    $qty = isset($d['qty_snapshot']) ? (int)$d['qty_snapshot'] : 1;

                                    if ($jenis === 'Potongan'):
                                        $hasPotongan = true;
                                        $qtyText = '';
                                        if ($qty > 1) {
                                            $qtyText = ' <span class="text-muted">(' . $qty . 'x Rp ' . number_format($nilai_satuan, 0, ',', '.') . ')</span>';
                                        } elseif ($nilai_satuan > 0 && (float)$d['nilai'] !== $nilai_satuan) {
                                            $calcQty = round((float)$d['nilai'] / $nilai_satuan);
                                            $qtyText = ' <span class="text-muted">(' . $calcQty . 'x Rp ' . number_format($nilai_satuan, 0, ',', '.') . ')</span>';
                                        }
                                ?>
                                    <tr>
                                        <td><?= esc($nama_komponen) ?><?= $qtyText ?></td>
                                        <td class="text-right">Rp <?= number_format($d['nilai'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php
                                    endif;
                                endforeach;
                                if (!$hasPotongan):
                                ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Tidak ada potongan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <hr class="mt-4">

        <div class="row justify-content-end">
            <div class="col-md-5">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="50%">Gaji Pokok</td>
                        <td width="10%">:</td>
                        <td class="text-right">Rp <?= number_format($gaji['gaji_pokok'], 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td class="text-success font-weight-bold">Total Tunjangan (+)</td>
                        <td>:</td>
                        <td class="text-right text-success font-weight-bold">Rp <?= number_format($gaji['total_tunjangan'], 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td class="text-danger font-weight-bold">Total Potongan (-)</td>
                        <td>:</td>
                        <td class="text-right text-danger font-weight-bold">Rp <?= number_format($gaji['total_potongan'], 2, ',', '.') ?></td>
                    </tr>
                    <tr class="border-top">
                        <td class="h5 font-weight-bold text-dark">Gaji Bersih</td>
                        <td class="h5 font-weight-bold text-dark">:</td>
                        <td class="h5 font-weight-bold text-dark text-right">Rp <?= number_format($gaji['gaji_bersih'], 2, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-sm-6 text-center offset-sm-6">
                <p>Jakarta, <?= esc($gaji['tanggal_bayar']) ?></p>
                <p>Bendahara,</p>
                <div class="my-3">
                    <!-- QR Code Dummy menggunakan API qrserver gratis -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode('Sah oleh Bendahara: ' . ($gaji['nama_pemroses'] ?? 'Administrator') . ' - Terbit: Rp ' . number_format($gaji['gaji_bersih'], 0, ',', '.')) ?>" alt="Tanda Tangan Digital QR" class="img-thumbnail" style="width: 100px; height: 100px;">
                </div>
                <p class="font-weight-bold mb-0">( <?= esc($gaji['nama_pemroses'] ?? 'Administrator') ?> )</p>
                <small class="text-muted text-uppercase d-block" style="font-size: 10px; letter-spacing: 1px;">Tanda Tangan Digital</small>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
