<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\KaryawanModel;

class UserController extends BaseController
{
    public function index()
    {
        cekRole('Admin');
        $model = new UserModel();
        $data['users'] = $model->select('user.*, karyawan.nama_karyawan')
                               ->join('karyawan', 'karyawan.id_karyawan = user.id_karyawan', 'left')
                               ->paginate(10, 'user');
        $data['pager'] = $model->pager;
        return view('admin/user/index', $data);
    }

    public function create()
    {
        cekLogin();
        return view('admin/user/form');
    }

    public function store()
    {
        cekLogin();
        $model = new UserModel();
        $karyawanModel = new KaryawanModel();

        $role = $this->request->getPost('role');
        $id_karyawan = null;

        $data = [
            'username'    => $this->request->getPost('username'),
            'password'    => $this->request->getPost('password'),
            'role'        => $role,
            'aktif'       => $this->request->getPost('aktif') !== null ? (int)$this->request->getPost('aktif') : 1,
            'id_karyawan' => null
        ];

        // Validasi kriteria password (min 8 karakter, ada huruf dan angka)
        $password = $data['password'];
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return redirect()->back()->with('error', 'Password harus minimal 8 karakter dan mengandung kombinasi huruf serta angka.')->withInput();
        }

        // Validate username uniqueness
        $existing = $model->getByUsername($data['username']);
        if ($existing) {
            return redirect()->back()->with('error', 'Username sudah digunakan.')->withInput();
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Buat record Karyawan kosong terlebih dahulu jika rolenya adalah Karyawan
        if ($role === 'Karyawan') {
            // Pastikan kolom baru ter-generate terlebih dahulu
            $karyawanModel->ensureColumnsExist();

            // Dapatkan ID Karyawan berikutnya (KARxxx)
            $lastId = $karyawanModel->getLastId();
            if ($lastId) {
                $num = (int)substr($lastId, 3);
                $nextNum = $num + 1;
            } else {
                $nextNum = 1;
            }
            $id_karyawan = 'KAR' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

            // Insert data karyawan kosong dengan default status 'Belum Lengkap' dan status_profil 'draft'
            $karyawanModel->insert([
                'id_karyawan'   => $id_karyawan,
                'nama_karyawan' => null, // diisi nanti oleh karyawan sendiri
                'status'        => 'Belum Lengkap',
                'status_profil' => 'draft',
                'id_jabatan'    => null, // jabatan akan diisi nanti
            ]);

            $data['id_karyawan'] = $id_karyawan;
        }

        // 2. Simpan user baru ke database
        $model->create($data);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menambahkan user.')->withInput();
        }

        return redirect()->to('admin/user')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        cekLogin();
        $model = new UserModel();

        $data['user'] = $model->getById($id);
        if (!$data['user']) {
            return redirect()->to('admin/user')->with('error', 'User tidak ditemukan.');
        }

        return view('admin/user/form', $data);
    }

    public function update($id)
    {
        cekLogin();
        $model = new UserModel();

        $role = $this->request->getPost('role');

        $data = [
            'username' => $this->request->getPost('username'),
            'role'     => $role,
            'aktif'    => $this->request->getPost('aktif') !== null ? (int)$this->request->getPost('aktif') : 1,
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            // Validasi kriteria password (min 8 karakter, ada huruf dan angka)
            if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                return redirect()->back()->with('error', 'Password harus minimal 8 karakter dan mengandung kombinasi huruf serta angka.')->withInput();
            }
            $data['password'] = $password;
        }

        // Validate username uniqueness but exclude self
        $existing = $model->where('username', $data['username'])
                         ->where('id_user !=', $id)
                         ->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Username sudah digunakan.')->withInput();
        }

        if ($model->update($id, $data)) {
            return redirect()->to('admin/user')->with('success', 'User berhasil diubah.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengubah user.')->withInput();
        }
    }

    public function delete($id)
    {
        cekLogin();
        $model = new UserModel();
        if ($model->delete($id)) {
            return redirect()->to('admin/user')->with('success', 'User berhasil dihapus.');
        } else {
            return redirect()->to('admin/user')->with('error', 'Gagal menghapus user.');
        }
    }
}
