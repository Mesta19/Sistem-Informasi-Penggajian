<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\GajiModel;
use App\Models\JabatanModel;

class Dashboard extends BaseController
{
    public function index()
    {
        cekLogin();
        $role = session()->get('user.role');
        $id_karyawan = session()->get('user.id_karyawan');

        $karyawanModel = new KaryawanModel();
        $gajiModel = new GajiModel();
        $jabatanModel = new JabatanModel();

        // Panggil helper pembuat/pemeriksa kolom agar selalu update
        $karyawanModel->ensureColumnsExist();

        $bulan = (int)date('m');
        $tahun = (int)date('Y');

        if ($role === 'Karyawan') {
            $karyawanInfo = null;
            if ($id_karyawan) {
                $karyawanInfo = $karyawanModel->select('karyawan.*, jabatan.nama_jabatan')
                                              ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan', 'left')
                                              ->find($id_karyawan);
            }

            if (!$karyawanInfo) {
                $karyawanInfo = [
                    'id_karyawan'   => null,
                    'nama_karyawan' => '',
                    'nik'           => '',
                    'npwp'          => '',
                    'nama_bank'     => '',
                    'no_rekening'   => '',
                    'no_telepon'    => '',
                    'alamat'        => '',
                    'status_profil' => 'draft',
                    'status'        => 'Belum Lengkap',
                    'nama_jabatan'  => '-'
                ];
            }

            $data = [
                'role'           => $role,
                'karyawanInfo'   => $karyawanInfo,
                'gajiLatest'     => $id_karyawan ? $gajiModel->where('id_karyawan', $id_karyawan)
                                             ->orderBy('tahun', 'DESC')
                                             ->orderBy('bulan', 'DESC')
                                             ->findAll() : []
            ];
            return view('admin/dashboard', $data);
        }

        $data = [
            'role'           => $role,
            'totalKaryawan'  => $karyawanModel->countAktif(),
            'totalGaji'      => $gajiModel->getTotalGajiBulanIni($bulan, $tahun),
            'jumlahSlip'     => $gajiModel->where('bulan', $bulan)->where('tahun', $tahun)->countAllResults(),
            'totalJabatan'   => $jabatanModel->countAllResults(),
            'karyawanLatest' => $karyawanModel->select('karyawan.*, jabatan.nama_jabatan')
                                             ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan')
                                             ->orderBy('karyawan.tanggal_masuk', 'DESC')
                                             ->limit(5)
                                             ->findAll()
        ];

        return view('admin/dashboard', $data);
    }

    public function updateProfile()
    {
        cekLogin();
        $role = session()->get('user.role');
        if ($role !== 'Karyawan') {
            return redirect()->to('admin/dashboard')->with('error', 'Aksi tidak diizinkan.');
        }

        $id_karyawan = session()->get('user.id_karyawan');
        $id_user = session()->get('user.id_user');

        $karyawanModel = new KaryawanModel();
        $userModel = new \App\Models\UserModel();

        // Ambil input form
        $nama_karyawan = trim($this->request->getPost('nama_karyawan') ?? '');
        $no_telepon  = trim($this->request->getPost('no_telepon') ?? '');
        $alamat      = trim($this->request->getPost('alamat') ?? '');
        $nik         = trim($this->request->getPost('nik') ?? '');
        $npwp        = trim($this->request->getPost('npwp') ?? '');
        $no_rekening = trim($this->request->getPost('no_rekening') ?? '');
        $nama_bank   = trim($this->request->getPost('nama_bank') ?? '');

        // Validasi input
        if (empty($nama_karyawan)) {
            return redirect()->back()->with('error', 'Nama lengkap wajib diisi.')->withInput();
        }
        if (empty($no_telepon)) {
            return redirect()->back()->with('error', 'Nomor telepon wajib diisi.')->withInput();
        }
        if (empty($alamat)) {
            return redirect()->back()->with('error', 'Alamat lengkap wajib diisi.')->withInput();
        }
        if (empty($nik)) {
            return redirect()->back()->with('error', 'NIK wajib diisi.')->withInput();
        }
        if (!preg_match('/^[0-9]{16}$/', $nik)) {
            return redirect()->back()->with('error', 'NIK harus terdiri dari 16 digit angka.')->withInput();
        }
        if (empty($no_rekening)) {
            return redirect()->back()->with('error', 'Nomor rekening wajib diisi.')->withInput();
        }
        if (empty($nama_bank)) {
            return redirect()->back()->with('error', 'Nama bank wajib dipilih/diisi.')->withInput();
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $isInsert = false;
        if (!$id_karyawan) {
            // Generate id_karyawan baru
            $karyawanModel->ensureColumnsExist();
            $lastId = $karyawanModel->getLastId();
            if ($lastId) {
                $num = (int)substr($lastId, 3);
                $nextNum = $num + 1;
            } else {
                $nextNum = 1;
            }
            $id_karyawan = 'KAR' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            $isInsert = true;
        }

        $dataSave = [
            'id_karyawan'   => $id_karyawan,
            'nama_karyawan' => $nama_karyawan,
            'no_telepon'    => $no_telepon,
            'alamat'        => $alamat,
            'nik'           => $nik,
            'npwp'          => $npwp,
            'no_rekening'   => $no_rekening,
            'nama_bank'     => $nama_bank,
            'status'        => 'Menunggu Persetujuan',
            'status_profil' => 'pending',
            'tgl_pengajuan' => date('Y-m-d H:i:s'),
            'catatan_admin' => null
        ];

        if ($isInsert) {
            // Insert karyawan baru
            $karyawanModel->insert($dataSave);
            // Hubungkan id_karyawan ke user
            $userModel->update($id_user, ['id_karyawan' => $id_karyawan]);
        } else {
            // Update karyawan yang ada
            $karyawanModel->update($id_karyawan, $dataSave);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses profil karyawan.')->withInput();
        }

        if ($isInsert) {
            // Perbarui session agar ter-link
            $userData = session()->get('user');
            $userData['id_karyawan'] = $id_karyawan;
            $userData['nama'] = $nama_karyawan;
            session()->set('user', $userData);
        }

        return redirect()->to('admin/dashboard')->with('success', 'Profil berhasil dikirim! Menunggu persetujuan dari Admin.');
    }
}
