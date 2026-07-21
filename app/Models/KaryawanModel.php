<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'karyawan';
    protected $primaryKey       = 'id_karyawan';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_karyawan',
        'nama_karyawan',
        'no_telepon',
        'alamat',
        'tanggal_masuk',
        'status',
        'id_jabatan',
        'nik',
        'npwp',
        'no_rekening',
        'nama_bank',
        'catatan_admin',
        'status_profil',
        'tgl_pengajuan'
    ];

    public function ensureColumnsExist()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('karyawan');

        $alterStatements = [];
        if (!in_array('nik', $fields)) {
            $alterStatements[] = "ADD COLUMN nik VARCHAR(16) NULL AFTER status";
        }
        if (!in_array('npwp', $fields)) {
            $alterStatements[] = "ADD COLUMN npwp VARCHAR(20) NULL AFTER nik";
        }
        if (!in_array('no_rekening', $fields)) {
            $alterStatements[] = "ADD COLUMN no_rekening VARCHAR(30) NULL AFTER npwp";
        }
        if (!in_array('nama_bank', $fields)) {
            $alterStatements[] = "ADD COLUMN nama_bank VARCHAR(50) NULL AFTER no_rekening";
        }
        if (!in_array('status_profil', $fields)) {
            $alterStatements[] = "ADD COLUMN status_profil ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft' AFTER status";
        }
        if (!in_array('catatan_admin', $fields)) {
            $alterStatements[] = "ADD COLUMN catatan_admin TEXT NULL AFTER status_profil";
        }
        if (!in_array('tgl_pengajuan', $fields)) {
            $alterStatements[] = "ADD COLUMN tgl_pengajuan DATETIME NULL AFTER catatan_admin";
        }

        if (!empty($alterStatements)) {
            $db->query("ALTER TABLE karyawan " . implode(', ', $alterStatements));
        }

        // Pastikan ENUM status sudah mencakup semua opsi yang dibutuhkan
        $db->query("ALTER TABLE karyawan MODIFY COLUMN status ENUM('Belum Lengkap', 'Menunggu Persetujuan', 'Aktif', 'Butuh Revisi', 'Tidak Aktif') DEFAULT 'Belum Lengkap'");
        $db->query("ALTER TABLE karyawan MODIFY COLUMN nama_karyawan VARCHAR(100) NULL");
        $db->query("ALTER TABLE karyawan MODIFY COLUMN id_jabatan INT NULL");
        $db->query("ALTER TABLE karyawan MODIFY COLUMN tanggal_masuk DATE NULL");
    }

    public function getAll()
    {
        return $this->select('karyawan.*, jabatan.nama_jabatan, jabatan.gaji_pokok')
                    ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan', 'left')
                    ->findAll();
    }

    public function getById($id)
    {
        return $this->find($id);
    }

    public function getLastId()
    {
        $row = $this->orderBy('id_karyawan', 'DESC')->first();
        return $row ? $row['id_karyawan'] : null;
    }

    public function create($data)
    {
        $lastId = $this->getLastId();
        if ($lastId) {
            $num = (int)substr($lastId, 3);
            $nextNum = $num + 1;
        } else {
            $nextNum = 1;
        }
        $data['id_karyawan'] = 'KAR' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
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

    public function countAktif()
    {
        return $this->where('status', 'Aktif')->countAllResults();
    }
}
