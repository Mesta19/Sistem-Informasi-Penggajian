# Sistem Informasi Penggajian Karyawan (Sisfo Gaji)

Sistem Informasi Penggajian Karyawan berbasis web yang dirancang menggunakan framework **CodeIgniter 4** dan database **MySQL**. Sistem ini mendukung manajemen data karyawan, pengelolaan komponen gaji (tunjangan dan potongan), proses penggajian bulanan dinamis, cetak slip gaji dengan QR Code tanda tangan, serta alur pendaftaran mandiri (self-service) bagi karyawan untuk melengkapi data profil mereka.

---

## 📸 Demo Aplikasi

### 🖥️ Dashboard Admin
![Dashboard Admin](DemoGit/Dashhboard%20admin.png)

### 📊 Daftar Karyawan
![Daftar Karyawan](DemoGit/Daftar%20Karyawan.png)

### 💼 Manajemen Jabatan
![Daftar Jabatan](DemoGit/Daftar%20jabatan.png)

### 💵 Komponen Gaji (Tunjangan & Potongan)
![Komponen Gaji](DemoGit/Komponen%20gaji.png)

### 💰 Proses & Daftar Gaji
![Daftar Gaji](DemoGit/Daftar%20gaji.png)

### 🔑 Manajemen Akun & User
![Daftar Akun](DemoGit/Daftar%20akun.png)

### 👤 Dashboard & Profil Karyawan (Self-Service)
![Dashboard Karyawan](DemoGit/Dashboard%20Karyawan.png)

---

## 🚀 Fitur Utama

### 🔐 Autentikasi & Otorisasi (RBAC)
* Pembatasan hak akses berbasis peran (**Admin** dan **Karyawan**).
* Keamanan login yang tangguh dengan enkripsi password (hash) dan proteksi **CSRF (Cross-Site Request Forgery)** pada setiap form transaksi.

### 👥 Modul Administrator
* **Dashboard Admin**: Ringkasan data statistik sistem (Total Karyawan Aktif, Total Pengeluaran Gaji, Jumlah Slip, Jumlah Jabatan).
* **Data Karyawan**: Mengelola data dasar karyawan, melihat detail data profil, serta menetapkan jabatan karyawan langsung pada baris daftar tabel karyawan.
* **Persetujuan Profil**: Memverifikasi pengisian data mandiri karyawan (Setuju/ACC atau Tolak dengan catatan revisi).
* **Manajemen Jabatan**: Mengelola data jabatan dan nominal gaji pokok.
* **Komponen Gaji**: Menentukan komponen tunjangan (penambah) dan potongan (pengurang) standar yang dinamis.
* **Transaksi Penggajian**: Memproses hitung gaji karyawan per periode bulan/tahun dengan input kehadiran manual (Hadir, Sakit, Izin, Alpha) serta memilih komponen tunjangan/potongan secara fleksibel.
* **Cetak Slip Gaji**: Mencetak slip gaji karyawan yang dilengkapi dengan snapshot data dinamis, perhitungan otomatis, dan QR Code verifikasi.

### 👤 Modul Karyawan (Self-Service)
* **Dashboard Karyawan**: Ringkasan data profil diri serta histori slip gaji yang pernah diproses.
* **Pengisian Profil Mandiri**: Melengkapi informasi Nama Lengkap, NIK (16 digit), NPWP, nomor rekening bank, nomor telepon, dan alamat domisili untuk diajukan ke admin.
* **Cetak Slip Mandiri**: Melihat histori slip gaji bulanan mereka dan mencetaknya secara mandiri.

---

## 🛠️ Teknologi yang Digunakan

* **Backend Framework**: CodeIgniter 4
* **Database**: MySQL / MariaDB
* **Frontend**: HTML5, CSS3, JavaScript (Vanilla ES6), Bootstrap 4 (SB Admin 2 Template), SweetAlert2
* **Server Environment**: XAMPP (Apache, PHP 8.1+)

---

## ⚙️ Panduan Instalasi & Penggunaan

### 1. Prasyarat Sistem
* PHP versi 8.1 ke atas (pastikan ekstensi `intl`, `mbstring`, `mysqli` aktif di `php.ini`).
* MySQL/MariaDB.

### 2. Klon / Salin Proyek
Pindahkan folder proyek ini ke direktori server lokal Anda (misalnya `C:/xampp/htdocs/penggajian-serkom`).

### 3. Setup Database
1. Buka phpMyAdmin atau client database MySQL Anda.
2. Buat database baru bernama `penggajian_db`.
3. Import file database yang berada di `/database/penggajian_db.sql`.

### 4. Konfigurasi File `.env`
Salin file `env` menjadi `.env` di root folder proyek, kemudian sesuaikan kredensial koneksi database Anda:
```env
database.default.hostname = localhost
database.default.database = penggajian_db
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

### 5. Jalankan Server Pengembangan
Buka terminal di root folder proyek, kemudian jalankan perintah Spark bawaan CI4:
```bash
php spark serve
```
Akses aplikasi melalui browser di tautan: **`http://localhost:8080`**

### 6. Akun Login Bawaan (Default Admin)
* **Username**: `admin`
* **Password**: `admin123`
