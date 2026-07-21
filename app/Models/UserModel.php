<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['username', 'password', 'role', 'aktif', 'id_karyawan'];

    public function getAll()
    {
        return $this->select('user.*, karyawan.nama_karyawan')
                    ->join('karyawan', 'karyawan.id_karyawan = user.id_karyawan', 'left')
                    ->findAll();
    }

    public function getById($id)
    {
        return $this->find($id);
    }

    public function getByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function create($data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->insert($data);
    }

    public function update($id = null, $data = null): bool
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        return parent::update($id, $data);
    }

    public function delete($id = null, bool $purge = false)
    {
        return parent::delete($id, $purge);
    }
}
