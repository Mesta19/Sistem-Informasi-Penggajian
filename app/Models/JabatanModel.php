<?php

namespace App\Models;

use CodeIgniter\Model;

class JabatanModel extends Model
{
    protected $table            = 'jabatan';
    protected $primaryKey       = 'id_jabatan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_jabatan', 'gaji_pokok'];

    public function getAll()
    {
        return $this->findAll();
    }

    public function getById($id)
    {
        return $this->find($id);
    }

    public function create($data)
    {
        return $this->insert($data);
    }

    // signature matches parent to avoid PHP warnings
    public function update($id = null, $data = null): bool
    {
        return parent::update($id, $data);
    }

    public function delete($id = null, bool $purge = false)
    {
        return parent::delete($id, $purge);
    }
}
