<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function showLogin()
    {
        // If already logged in -> redirect to dashboard
        if (session()->has('user')) {
            return redirect()->to('admin/dashboard');
        }

        // Get error from flash session
        $error = session()->getFlashdata('flash_error');

        return view('login', ['error' => $error]);
    }

    public function login()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/');
        }

        $username = trim($this->request->getPost('username') ?? '');
        $password = trim($this->request->getPost('password') ?? '');

        if (empty($username) || empty($password)) {
            session()->setFlashdata('flash_error', 'Username dan password wajib diisi.');
            return redirect()->to('/');
        }

        // Query database directly to get user with join on karyawan
        $db = \Config\Database::connect();
        $user = $db->table('user u')
                   ->select('u.*, k.nama_karyawan')
                   ->join('karyawan k', 'u.id_karyawan = k.id_karyawan', 'left')
                   ->where('u.username', $username)
                   ->where('u.aktif', 1)
                   ->get()
                   ->getRowArray();

        // Verify password
        if (!$user || !password_verify($password, $user['password'])) {
            session()->setFlashdata('flash_error', 'Username atau password salah.');
            return redirect()->to('/')->withInput();
        }

        // Save user info to session
        $userData = [
            'id_user'     => $user['id_user'],
            'username'    => $user['username'],
            'role'        => $user['role'],
            'nama'        => $user['nama_karyawan'] ?? $user['username'],
            'id_karyawan' => $user['id_karyawan'] ?? null,
        ];
        session()->set('user', $userData);

        return redirect()->to('admin/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
