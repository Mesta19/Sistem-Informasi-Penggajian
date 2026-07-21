<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\KomponenGajiModel;

class KomponenController extends BaseController
{
    public function index()
    {
        cekRole('Admin');
        $model = new KomponenGajiModel();
        $data['komponen'] = $model->getAll();
        return view('admin/komponen/index', $data);
    }

    public function create()
    {
        cekLogin();
        return view('admin/komponen/form');
    }

    public function store()
    {
        cekLogin();
        $model = new KomponenGajiModel();
        $data = [
            'nama_komponen' => $this->request->getPost('nama_komponen'),
            'jenis'         => $this->request->getPost('jenis'),
            'nilai'         => $this->request->getPost('nilai'),
        ];

        if ($model->create($data)) {
            return redirect()->to('admin/komponen')->with('success', 'Komponen gaji berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan komponen gaji.')->withInput();
        }
    }

    public function edit($id)
    {
        cekLogin();
        $model = new KomponenGajiModel();
        $data['komponen'] = $model->getById($id);
        if (!$data['komponen']) {
            return redirect()->to('admin/komponen')->with('error', 'Komponen gaji tidak ditemukan.');
        }
        return view('admin/komponen/form', $data);
    }

    public function update($id)
    {
        cekLogin();
        $model = new KomponenGajiModel();
        $data = [
            'nama_komponen' => $this->request->getPost('nama_komponen'),
            'jenis'         => $this->request->getPost('jenis'),
            'nilai'         => $this->request->getPost('nilai'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to('admin/komponen')->with('success', 'Komponen gaji berhasil diubah.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengubah komponen gaji.')->withInput();
        }
    }

    public function delete($id)
    {
        cekLogin();
        $model = new KomponenGajiModel();
        if ($model->delete($id)) {
            return redirect()->to('admin/komponen')->with('success', 'Komponen gaji berhasil dihapus.');
        } else {
            return redirect()->to('admin/komponen')->with('error', 'Gagal menghapus komponen gaji.');
        }
    }
}
