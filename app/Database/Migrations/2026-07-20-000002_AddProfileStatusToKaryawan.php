<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfileStatusToKaryawan extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $fields = [];
        if (!$db->fieldExists('status_profil', 'karyawan')) {
            $fields['status_profil'] = [
                'type'       => "ENUM('draft', 'pending', 'approved', 'rejected')",
                'default'    => 'draft',
                'null'       => false,
                'after'      => 'status',
            ];
        }
        if (!$db->fieldExists('tgl_pengajuan', 'karyawan')) {
            $fields['tgl_pengajuan'] = [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'catatan_admin',
            ];
        }
        if (!empty($fields)) {
            $this->forge->addColumn('karyawan', $fields);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $fieldsToRemove = [];
        if ($db->fieldExists('status_profil', 'karyawan')) {
            $fieldsToRemove[] = 'status_profil';
        }
        if ($db->fieldExists('tgl_pengajuan', 'karyawan')) {
            $fieldsToRemove[] = 'tgl_pengajuan';
        }
        if (!empty($fieldsToRemove)) {
            $this->forge->dropColumn('karyawan', $fieldsToRemove);
        }
    }
}
