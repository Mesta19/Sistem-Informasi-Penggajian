<?php

namespace App\Models;

use CodeIgniter\Model;

class KomponenGajiModel extends Model
{
    protected $table            = 'komponen_gaji';
    protected $primaryKey       = 'id_komponen';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_komponen', 'jenis', 'nilai'];

    public function getAll()
    {
        return $this->findAll();
    }

    public function getAllTunjangan()
    {
        return $this->where('jenis', 'Tunjangan')->findAll();
    }

    public function getAllPotongan()
    {
        return $this->where('jenis', 'Potongan')->findAll();
    }

    public function getById($id)
    {
        return $this->find($id);
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
}
