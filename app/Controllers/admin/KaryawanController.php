<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\JabatanModel;

class KaryawanController extends BaseController
{
    public function index()
    {
        cekRole('Admin');
        $model = new KaryawanModel();
        $jabatanModel = new JabatanModel();
        // Pastikan kolom baru ter-generate sebelum data diload
        $model->ensureColumnsExist();
        $data['karyawan'] = $model->select('karyawan.*, jabatan.nama_jabatan, jabatan.gaji_pokok')
                                  ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan', 'left')
                                  ->paginate(10, 'karyawan');
        $data['pager'] = $model->pager;
        $data['jabatan'] = $jabatanModel->getAll();
        return view('admin/karyawan/index', $data);
    }

    public function persetujuan()
    {
        cekRole('Admin');
        $model = new KaryawanModel();
        $model->ensureColumnsExist();

        $status = $this->request->getVar('status') ?? 'pending';

        $query = $model->select('karyawan.*, jabatan.nama_jabatan, user.username')
                       ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan', 'left')
                       ->join('user', 'user.id_karyawan = karyawan.id_karyawan', 'left');

        if ($status !== 'semua') {
            $query->where('karyawan.status_profil', $status);
        }

        $query->orderBy('karyawan.tgl_pengajuan', 'DESC');

        $data['karyawan'] = $query->paginate(10, 'karyawan');
        $data['pager'] = $model->pager;
        $data['status_filter'] = $status;

        return view('admin/karyawan/persetujuan', $data);
    }

    public function create()
    {
        cekLogin();
        return view('admin/karyawan/form');
    }

    public function store()
    {
        cekLogin();
        $model = new KaryawanModel();

        // Panggil helper pembuat/pemeriksa kolom agar selalu update
        $model->ensureColumnsExist();

        $data = [
            'nama_karyawan' => $this->request->getPost('nama_karyawan'),
            'no_telepon'    => $this->request->getPost('no_telepon'),
            'alamat'        => $this->request->getPost('alamat'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'status'        => $this->request->getPost('status') ?? 'Belum Lengkap',
            'id_jabatan'    => null,
            'nik'           => $this->request->getPost('nik') ?: null,
            'npwp'          => $this->request->getPost('npwp') ?: null,
            'no_rekening'   => $this->request->getPost('no_rekening') ?: null,
            'nama_bank'     => $this->request->getPost('nama_bank') ?: null,
            'catatan_admin' => $this->request->getPost('catatan_admin') ?: null,
        ];

        if ($model->create($data)) {
            return redirect()->to('admin/karyawan')->with('success', 'Data karyawan berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data karyawan.')->withInput();
        }
    }

    public function edit($id)
    {
        cekLogin();
        $model = new KaryawanModel();

        $data['karyawan'] = $model->getById($id);
        if (!$data['karyawan']) {
            return redirect()->to('admin/karyawan')->with('error', 'Data karyawan tidak ditemukan.');
        }

        return view('admin/karyawan/form', $data);
    }

    public function update($id)
    {
        cekLogin();
        $model = new KaryawanModel();

        $karyawan = $model->find($id);
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $data = [
            'nama_karyawan' => $this->request->getPost('nama_karyawan'),
            'no_telepon'    => $this->request->getPost('no_telepon') ?: $karyawan['no_telepon'],
            'alamat'        => $this->request->getPost('alamat') ?: $karyawan['alamat'],
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk') ?: $karyawan['tanggal_masuk'],
            'status'        => $this->request->getPost('status') ?: $karyawan['status'],
            'nik'           => $this->request->getPost('nik') ?: $karyawan['nik'],
            'npwp'          => $this->request->getPost('npwp') ?: $karyawan['npwp'],
            'no_rekening'   => $this->request->getPost('no_rekening') ?: $karyawan['no_rekening'],
            'nama_bank'     => $this->request->getPost('nama_bank') ?: $karyawan['nama_bank'],
            'catatan_admin' => $this->request->getPost('catatan_admin') ?: null,
        ];

        if ($model->update($id, $data)) {
            return redirect()->to('admin/karyawan')->with('success', 'Data karyawan berhasil diubah.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengubah data karyawan.')->withInput();
        }
    }

    public function delete($id)
    {
        cekLogin();
        $model = new KaryawanModel();
        if ($model->delete($id)) {
            return redirect()->to('admin/karyawan')->with('success', 'Data karyawan berhasil dihapus.');
        } else {
            return redirect()->to('admin/karyawan')->with('error', 'Gagal menghapus data karyawan.');
        }
    }

    public function updateJabatan($id)
    {
        cekRole('Admin');
        $model = new KaryawanModel();

        $karyawan = $model->find($id);
        if (!$karyawan) {
            return redirect()->to('admin/karyawan')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $id_jabatan = $this->request->getPost('id_jabatan') ?: null;

        if ($model->update($id, ['id_jabatan' => $id_jabatan])) {
            return redirect()->to('admin/karyawan')->with('success', 'Jabatan karyawan ' . esc($karyawan['nama_karyawan'] ?: $karyawan['id_karyawan']) . ' berhasil diperbarui.');
        } else {
            return redirect()->to('admin/karyawan')->with('error', 'Gagal memperbarui jabatan karyawan.');
        }
    }

    public function approve($id)
    {
        return $this->setujui($id);
    }

    public function reject($id)
    {
        return $this->tolak($id);
    }

    public function setujui($id)
    {
        cekRole('Admin');
        $model = new KaryawanModel();
        $karyawan = $model->find($id);
        if (!$karyawan) {
            return redirect()->to('admin/karyawan/persetujuan')->with('error', 'Karyawan tidak ditemukan.');
        }

        $data = [
            'status'        => 'Aktif',
            'status_profil' => 'approved',
            'catatan_admin' => null
        ];

        if ($model->update($id, $data)) {
            return redirect()->to('admin/karyawan/persetujuan')->with('success', 'Profil karyawan ' . esc($karyawan['nama_karyawan']) . ' berhasil disetujui (ACC).');
        } else {
            return redirect()->to('admin/karyawan/persetujuan')->with('error', 'Gagal menyetujui profil karyawan.');
        }
    }

    public function tolak($id)
    {
        cekRole('Admin');
        $model = new KaryawanModel();
        $karyawan = $model->find($id);
        if (!$karyawan) {
            return redirect()->to('admin/karyawan/persetujuan')->with('error', 'Karyawan tidak ditemukan.');
        }

        $catatan_admin = trim($this->request->getPost('catatan_admin') ?? '');
        if (empty($catatan_admin)) {
            return redirect()->to('admin/karyawan/persetujuan')->with('error', 'Alasan penolakan (catatan admin) wajib diisi jika menolak.');
        }

        $data = [
            'status'        => 'Butuh Revisi',
            'status_profil' => 'rejected',
            'catatan_admin' => $catatan_admin
        ];

        if ($model->update($id, $data)) {
            return redirect()->to('admin/karyawan/persetujuan')->with('success', 'Profil karyawan ' . esc($karyawan['nama_karyawan']) . ' telah ditolak.');
        } else {
            return redirect()->to('admin/karyawan/persetujuan')->with('error', 'Gagal memproses penolakan profil karyawan.');
        }
    }
}
