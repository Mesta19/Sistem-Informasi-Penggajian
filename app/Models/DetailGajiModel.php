<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailGajiModel extends Model
{
    protected $table            = 'detail_gaji';
    protected $primaryKey       = 'id_detail';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_gaji', 'id_komponen', 'nilai', 'qty_snapshot', 'satuan_snapshot', 'nama_snapshot', 'jenis_snapshot'];

    public function getByGaji($id_gaji)
    {
        return $this->where('id_gaji', $id_gaji)->findAll();
    }

    public function create($data)
    {
        return $this->insert($data);
    }

    public function deleteByGaji($id_gaji)
    {
        return $this->where('id_gaji', $id_gaji)->delete();
    }
}
