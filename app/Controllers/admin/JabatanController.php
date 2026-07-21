<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\JabatanModel;

class JabatanController extends BaseController
{
    public function index()
    {
        cekRole('Admin');
        $model = new JabatanModel();
        $data['jabatan'] = $model->getAll();
        return view('admin/jabatan/index', $data);
    }

    public function create()
    {
        cekLogin();
        return view('admin/jabatan/form');
    }

    public function store()
    {
        cekLogin();
        $model = new JabatanModel();
        $data = [
            'nama_jabatan' => $this->request->getPost('nama_jabatan'),
            'gaji_pokok'   => $this->request->getPost('gaji_pokok'),
        ];

        if ($model->create($data)) {
            return redirect()->to('admin/jabatan')->with('success', 'Data jabatan berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data jabatan.')->withInput();
        }
    }

    public function edit($id)
    {
        cekLogin();
        $model = new JabatanModel();
        $data['jabatan'] = $model->getById($id);
        if (!$data['jabatan']) {
            return redirect()->to('admin/jabatan')->with('error', 'Data jabatan tidak ditemukan.');
        }
        return view('admin/jabatan/form', $data);
    }

    public function update($id)
    {
        cekLogin();
        $model = new JabatanModel();
        $data = [
            'nama_jabatan' => $this->request->getPost('nama_jabatan'),
            'gaji_pokok'   => $this->request->getPost('gaji_pokok'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to('admin/jabatan')->with('success', 'Data jabatan berhasil diubah.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengubah data jabatan.')->withInput();
        }
    }

    public function delete($id)
    {
        cekLogin();
        $model = new JabatanModel();
        try {
            if ($model->delete($id)) {
                return redirect()->to('admin/jabatan')->with('success', 'Data jabatan berhasil dihapus.');
            } else {
                return redirect()->to('admin/jabatan')->with('error', 'Gagal menghapus data jabatan.');
            }
        } catch (\Exception $e) {
            return redirect()->to('admin/jabatan')->with('error', 'Gagal menghapus jabatan. Pastikan jabatan tidak direferensikan oleh data karyawan.');
        }
    }
}
