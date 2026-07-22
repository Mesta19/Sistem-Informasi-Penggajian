<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\GajiModel;
use App\Models\DetailGajiModel;
use App\Models\KaryawanModel;

class GajiController extends BaseController
{
    public function index()
    {
        cekRole('Admin');
        $gajiModel = new GajiModel();

        // Ambil input get parameter. Jika bernilai kosong string, anggap null (Tampilkan semua waktu)
        $bulanInput = $this->request->getGet('bulan');
        $tahunInput = $this->request->getGet('tahun');
        $sort = $this->request->getGet('sort') ?: 'waktu';

        // Deteksi apakah filter dikirim atau tidak.
        // Jika parameter query string kosong atau tidak ada (misal akses langsung admin/gaji),
        // kita tampilkan semua waktu secara default agar data bulan/tahun sebelumnya tidak tersembunyi
        $bulan = ($bulanInput !== null && $bulanInput !== '') ? (int)$bulanInput : null;
        $tahun = ($tahunInput !== null && $tahunInput !== '') ? (int)$tahunInput : null;

        $query = $gajiModel->select('gaji.*, karyawan.nama_karyawan')
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

        $data = [
            'gaji'  => $query->paginate(10, 'gaji'),
            'pager' => $gajiModel->pager,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'sort'  => $sort,
        ];

        return view('admin/gaji/index', $data);
    }

    public function proses()
    {
        cekLogin();
        $karyawanModel = new KaryawanModel();
        $komponenModel = new \App\Models\KomponenGajiModel();

        $data['karyawan'] = $karyawanModel->where('status', 'Aktif')->findAll();
        $data['komponen'] = $komponenModel->findAll();
        return view('admin/gaji/proses', $data);
    }

    public function simpan()
    {
        cekLogin();
        $gajiModel = new GajiModel();

        $id_karyawan = $this->request->getPost('id_karyawan');
        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');
        $hari_hadir = (int)($this->request->getPost('hari_hadir') ?? 0);
        $hari_sakit = (int)($this->request->getPost('hari_sakit') ?? 0);
        $hari_izin = (int)($this->request->getPost('hari_izin') ?? 0);
        $hari_alpha = (int)($this->request->getPost('hari_alpha') ?? 0);
        $komponen_pilihan = $this->request->getPost('komponen_gaji') ?: [];
        $qty_komponen = $this->request->getPost('qty_komponen') ?: [];

        try {
            $id_gaji = $gajiModel->prosesGaji($id_karyawan, $bulan, $tahun, $komponen_pilihan, $qty_komponen, $hari_hadir, $hari_sakit, $hari_izin, $hari_alpha);
            return redirect()->to("admin/gaji/slip/{$id_gaji}")->with('success', 'Gaji berhasil diproses.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses gaji: ' . $e->getMessage())->withInput();
        }
    }

    public function slip($id)
    {
        cekLogin();
        $role = session()->get('user.role');
        $id_karyawan = session()->get('user.id_karyawan');
        $gajiModel = new GajiModel();
        $detailModel = new DetailGajiModel();

        $data['gaji'] = $gajiModel->getById($id);
        if (!$data['gaji']) {
            return redirect()->to($role === 'Karyawan' ? 'admin/dashboard' : 'admin/gaji')->with('error', 'Slip gaji tidak ditemukan.');
        }

        // Karyawan hanya bisa melihat slip miliknya sendiri
        if ($role === 'Karyawan' && $data['gaji']['id_karyawan'] !== $id_karyawan) {
            return redirect()->to('admin/dashboard')->with('error', 'Akses ditolak.');
        }

        $data['detail'] = $detailModel->getByGaji($id);
        return view('admin/gaji/slip', $data);
    }

    public function delete($id)
    {
        cekLogin();
        $gajiModel = new GajiModel();
        $detailModel = new DetailGajiModel();

        // Hapus detail_gaji dulu
        $detailModel->deleteByGaji($id);

        if ($gajiModel->delete($id)) {
            return redirect()->to('admin/gaji')->with('success', 'Slip gaji berhasil dihapus.');
        } else {
            return redirect()->to('admin/gaji')->with('error', 'Gagal menghapus slip gaji.');
        }
    }

    public function massal()
    {
        cekRole('Admin');
        $karyawanModel = new KaryawanModel();
        $komponenModel = new \App\Models\KomponenGajiModel();

        $data['karyawan'] = $karyawanModel->select('karyawan.*, jabatan.nama_jabatan, jabatan.gaji_pokok')
                                          ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan', 'left')
                                          ->where('karyawan.status', 'Aktif')
                                          ->findAll();
        $data['komponen'] = $komponenModel->findAll();

        return view('admin/gaji/massal', $data);
    }

    public function prosesMassal()
    {
        cekRole('Admin');
        $gajiModel = new GajiModel();
        $db = \Config\Database::connect();

        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');
        $karyawanInput = $this->request->getPost('karyawan') ?: [];

        if (empty($bulan) || empty($tahun) || empty($karyawanInput)) {
            return redirect()->back()->with('error', 'Parameter bulan, tahun, atau data karyawan tidak valid.')->withInput();
        }

        $sukses = 0;
        $gagal = 0;
        $pesanGagal = [];

        $db->transStart();

        foreach ($karyawanInput as $id_karyawan => $data) {
            // Cek apakah karyawan dicentang untuk diproses
            if (!isset($data['ikutkan']) || $data['ikutkan'] != 1) {
                continue;
            }

            // Mencegah slip gaji tertimpa
            $existing = $gajiModel->where('id_karyawan', $id_karyawan)
                                  ->where('bulan', $bulan)
                                  ->where('tahun', $tahun)
                                  ->first();
            if ($existing) {
                continue;
            }

            // Ambil input kehadiran
            $hari_hadir = (int)($data['hari_hadir'] ?? 0);
            $hari_sakit = (int)($data['hari_sakit'] ?? 0);
            $hari_izin = (int)($data['hari_izin'] ?? 0);
            $hari_alpha = (int)($data['hari_alpha'] ?? 0);

            // Ambil komponen pilihan & qty
            $komponen_pilihan = isset($data['komponen_gaji']) ? (array)$data['komponen_gaji'] : [];
            $qty_komponen = isset($data['qty_komponen']) ? (array)$data['qty_komponen'] : [];

            try {
                // Method prosesGaji akan update/insert
                $gajiModel->prosesGaji(
                    $id_karyawan,
                    $bulan,
                    $tahun,
                    $komponen_pilihan,
                    $qty_komponen,
                    $hari_hadir,
                    $hari_sakit,
                    $hari_izin,
                    $hari_alpha
                );
                $sukses++;
            } catch (\Exception $e) {
                $gagal++;
                $pesanGagal[] = "KAR: {$id_karyawan} (" . $e->getMessage() . ")";
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem, transaksi dibatalkan.')->withInput();
        }

        if ($sukses > 0 || $gagal > 0) {
            $msg = "Proses gaji massal selesai. Sukses: {$sukses} karyawan.";
            if ($gagal > 0) {
                $msg .= " Gagal: {$gagal} karyawan. Detail error: " . implode(', ', $pesanGagal);
                return redirect()->to('admin/gaji')->with('error', $msg);
            }
            return redirect()->to('admin/gaji')->with('success', $msg);
        }

        return redirect()->back()->with('error', 'Tidak ada karyawan yang dipilih untuk diproses.');
    }

    public function checkPembayaran()
    {
        cekRole('Admin');
        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');
        $karyawanIds = $this->request->getPost('karyawan_ids') ?: [];

        if (empty($bulan) || empty($tahun)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Bulan dan tahun wajib dipilih'
            ]);
        }

        $gajiModel = new GajiModel();
        $karyawanModel = new KaryawanModel();

        // Get details of all checked or all active employees
        $allKaryawan = $karyawanModel->where('status', 'Aktif')->findAll();
        $karyawanMap = [];
        foreach ($allKaryawan as $k) {
            $karyawanMap[$k['id_karyawan']] = $k['nama_karyawan'];
        }

        $sudahBayar = [];
        $belumBayar = [];

        // If karyawanIds is empty, we check all active employees (e.g. for initial page load status)
        $targetIds = !empty($karyawanIds) ? $karyawanIds : array_keys($karyawanMap);

        if (!empty($targetIds)) {
            $gajiList = $gajiModel->whereIn('id_karyawan', $targetIds)
                                  ->where('bulan', $bulan)
                                  ->where('tahun', $tahun)
                                  ->findAll();

            $paidIds = array_column($gajiList, 'id_karyawan');

            foreach ($targetIds as $id) {
                if (isset($karyawanMap[$id])) {
                    $item = [
                        'id_karyawan' => $id,
                        'nama_karyawan' => $karyawanMap[$id]
                    ];
                    if (in_array($id, $paidIds)) {
                        $sudahBayar[] = $item;
                    } else {
                        $belumBayar[] = $item;
                    }
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'sudah_bayar' => $sudahBayar,
            'belum_bayar' => $belumBayar,
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash()
        ]);
    }
}
