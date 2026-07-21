<?php

namespace App\Models;

use CodeIgniter\Model;

class GajiModel extends Model
{
    protected $table            = 'gaji';
    protected $primaryKey       = 'id_gaji';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_karyawan', 'bulan', 'tahun', 'hari_hadir', 'hari_sakit', 'hari_izin', 'hari_alpha', 'gaji_pokok', 'total_tunjangan', 'total_potongan', 'gaji_bersih', 'tanggal_bayar', 'id_pemroses', 'nama_pemroses'];

    public function getAll($bulan = null, $tahun = null, $sort = 'waktu')
    {
        $query = $this->select('gaji.*, karyawan.nama_karyawan')
                      ->join('karyawan', 'karyawan.id_karyawan = gaji.id_karyawan');
        if ($bulan) {
            $query->where('gaji.bulan', $bulan);
        }
        if ($tahun) {
            $query->where('gaji.tahun', $tahun);
        }

        if ($sort === 'terkecil') {
            $query->orderBy('gaji.gaji_bersih', 'ASC');
        } elseif ($sort === 'terbesar') {
            $query->orderBy('gaji.gaji_bersih', 'DESC');
        } else {
            $query->orderBy('gaji.tanggal_bayar', 'DESC')
                  ->orderBy('gaji.id_gaji', 'DESC');
        }

        return $query->findAll();
    }

    public function getById($id)
    {
        return $this->select('gaji.*, karyawan.nama_karyawan, jabatan.nama_jabatan')
                    ->join('karyawan', 'karyawan.id_karyawan = gaji.id_karyawan')
                    ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan', 'left')
                    ->find($id);
    }

    public function getByKaryawan($id_karyawan, $bulan, $tahun)
    {
        return $this->where('id_karyawan', $id_karyawan)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->first();
    }

    public function create($data)
    {
        return $this->insert($data);
    }

    public function update($id = null, $data = null): bool
    {
        return parent::update($id, $data);
    }

    public function delete($id = null, bool $purge = false)
    {
        return parent::delete($id, $purge);
    }

    public function getTotalGajiBulanIni($bulan, $tahun)
    {
        $res = $this->selectSum('gaji_bersih')
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->first();
        return $res ? (float)$res['gaji_bersih'] : 0.0;
    }

    public function prosesGaji($id_karyawan, $bulan, $tahun, array $komponen_pilihan = [], array $qty_komponen = [], $hari_hadir = 0, $hari_sakit = 0, $hari_izin = 0, $hari_alpha = 0)
    {
        // 1. Ambil data karyawan & jabatan
        $karyawanModel = new \App\Models\KaryawanModel();
        $karyawan = $karyawanModel->select('karyawan.*, jabatan.gaji_pokok')
                                  ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan', 'left')
                                  ->find($id_karyawan);

        if (!$karyawan) {
            throw new \Exception("Karyawan tidak ditemukan");
        }

        if (empty($karyawan['id_jabatan'])) {
            throw new \Exception("Jabatan belum ada, silahkan atur");
        }

        $gaji_pokok = (float) $karyawan['gaji_pokok'];

        // Cek ketersediaan kolom secara dinamis lewat query langsung ke database mysql
        $db = \Config\Database::connect();
        $checkGajiCols = $db->query("SHOW COLUMNS FROM gaji")->getResultArray();
        $existingGajiCols = array_column($checkGajiCols, 'Field');

        if (!in_array('hari_sakit', $existingGajiCols)) {
            $db->query("ALTER TABLE gaji ADD COLUMN hari_sakit INT DEFAULT 0");
        }
        if (!in_array('hari_izin', $existingGajiCols)) {
            $db->query("ALTER TABLE gaji ADD COLUMN hari_izin INT DEFAULT 0");
        }
        if (!in_array('id_pemroses', $existingGajiCols)) {
            $db->query("ALTER TABLE gaji ADD COLUMN id_pemroses INT DEFAULT NULL");
        }
        if (!in_array('nama_pemroses', $existingGajiCols)) {
            $db->query("ALTER TABLE gaji ADD COLUMN nama_pemroses VARCHAR(255) DEFAULT NULL");
        }

        // Ambil info admin pemroses dari session
        $id_pemroses = session()->get('user.id_user');
        $nama_pemroses = session()->get('user.nama') ?: session()->get('user.username') ?: 'Administrator';

        // 3. Ambil komponen gaji pilihan
        $total_tunjangan = 0.0;
        $total_potongan = 0.0;
        $detail_records = [];

        if (!empty($komponen_pilihan)) {
            $komponenModel = new \App\Models\KomponenGajiModel();
            $komponens = $komponenModel->whereIn('id_komponen', $komponen_pilihan)->findAll();

            foreach ($komponens as $k) {
                $qty = isset($qty_komponen[$k['id_komponen']]) ? (int)$qty_komponen[$k['id_komponen']] : 1;
                if ($qty < 1) $qty = 1;

                $nilai_total = (float) $k['nilai'] * $qty;
                if ($k['jenis'] === 'Tunjangan') {
                    $total_tunjangan += $nilai_total;
                } else {
                    $total_potongan += $nilai_total;
                }
                $detail_records[] = [
                    'id_komponen' => $k['id_komponen'],
                    'nilai' => $nilai_total,
                    'qty' => $qty,
                    'nama_komponen_snapshot' => $k['nama_komponen'],
                    'jenis_snapshot' => $k['jenis'],
                    'nilai_satuan_snapshot' => (float)$k['nilai']
                ];
            }
        }

        $gaji_bersih = $gaji_pokok + $total_tunjangan - $total_potongan;

        // 4. Cek apakah slip sudah ada
        $existing = $this->where('id_karyawan', $id_karyawan)
                         ->where('bulan', $bulan)
                         ->where('tahun', $tahun)
                         ->first();

        $detailGajiModel = new \App\Models\DetailGajiModel();

        if ($existing) {
            $id_gaji = $existing['id_gaji'];
            // Hapus detail lama
            $detailGajiModel->deleteByGaji($id_gaji);

            // Update slip gaji
            $this->update($id_gaji, [
                'hari_hadir'      => $hari_hadir,
                'hari_sakit'      => $hari_sakit,
                'hari_izin'       => $hari_izin,
                'hari_alpha'      => $hari_alpha,
                'gaji_pokok'      => $gaji_pokok,
                'total_tunjangan' => $total_tunjangan,
                'total_potongan'  => $total_potongan,
                'gaji_bersih'     => $gaji_bersih,
                'tanggal_bayar'   => date('Y-m-d'),
                'id_pemroses'     => $id_pemroses,
                'nama_pemroses'   => $nama_pemroses
            ]);
        } else {
            // Insert slip baru
            $id_gaji = $this->insert([
                'id_karyawan'     => $id_karyawan,
                'bulan'           => $bulan,
                'tahun'           => $tahun,
                'hari_hadir'      => $hari_hadir,
                'hari_sakit'      => $hari_sakit,
                'hari_izin'       => $hari_izin,
                'hari_alpha'      => $hari_alpha,
                'gaji_pokok'      => $gaji_pokok,
                'total_tunjangan' => $total_tunjangan,
                'total_potongan'  => $total_potongan,
                'gaji_bersih'     => $gaji_bersih,
                'tanggal_bayar'   => date('Y-m-d'),
                'id_pemroses'     => $id_pemroses,
                'nama_pemroses'   => $nama_pemroses
            ]);
        }

        // 5. Simpan detail gaji
        if (!empty($detail_records)) {
            $db = \Config\Database::connect();
            $checkCols = $db->query("SHOW COLUMNS FROM detail_gaji")->getResultArray();
            $existingCols = array_column($checkCols, 'Field');

            $alterStatements = [];
            if (!in_array('qty_snapshot', $existingCols)) {
                $alterStatements[] = "ADD COLUMN qty_snapshot INT DEFAULT 1";
            }
            if (!in_array('satuan_snapshot', $existingCols)) {
                $alterStatements[] = "ADD COLUMN satuan_snapshot DECIMAL(12,2) DEFAULT 0";
            }
            if (!in_array('nama_snapshot', $existingCols)) {
                $alterStatements[] = "ADD COLUMN nama_snapshot VARCHAR(255) NULL";
            }
            if (!in_array('jenis_snapshot', $existingCols)) {
                $alterStatements[] = "ADD COLUMN jenis_snapshot VARCHAR(50) NULL";
            }

            if (!empty($alterStatements)) {
                $db->query("ALTER TABLE detail_gaji " . implode(', ', $alterStatements));
            }

            $batchData = [];
            foreach ($detail_records as $record) {
                $batchData[] = [
                    'id_gaji'         => $id_gaji,
                    'id_komponen'     => $record['id_komponen'],
                    'nilai'           => $record['nilai'],
                    'qty_snapshot'    => $record['qty'],
                    'satuan_snapshot' => $record['nilai_satuan_snapshot'],
                    'nama_snapshot'   => $record['nama_komponen_snapshot'],
                    'jenis_snapshot'  => $record['jenis_snapshot']
                ];
            }

            $db->table('detail_gaji')->insertBatch($batchData);
        }

        return $id_gaji;
    }
}
