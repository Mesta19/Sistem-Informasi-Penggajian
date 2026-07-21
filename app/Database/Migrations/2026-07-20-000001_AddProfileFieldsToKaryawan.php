<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfileFieldsToKaryawan extends Migration
{
    public function up()
    {
        $fields = [
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => '16',
                'null'       => true,
                'after'      => 'status',
            ],
            'npwp' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'after'      => 'nik',
            ],
            'no_rekening' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => true,
                'after'      => 'npwp',
            ],
            'nama_bank' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'no_rekening',
            ],
            'catatan_admin' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'nama_bank',
            ],
        ];

        // Tambah kolom jika belum ada
        $this->forge->addColumn('karyawan', $fields);

        // Ubah enum/tipe kolom status dan default-nya ke 'Belum Lengkap'
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE karyawan MODIFY COLUMN status ENUM('Belum Lengkap', 'Menunggu Persetujuan', 'Aktif', 'Butuh Revisi', 'Tidak Aktif') DEFAULT 'Belum Lengkap'");
    }

    public function down()
    {
        $this->forge->dropColumn('karyawan', ['nik', 'npwp', 'no_rekening', 'nama_bank', 'catatan_admin']);

        $db = \Config\Database::connect();
        $db->query("ALTER TABLE karyawan MODIFY COLUMN status VARCHAR(50) DEFAULT 'Aktif'");
    }
}
