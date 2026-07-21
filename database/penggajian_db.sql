CREATE DATABASE IF NOT EXISTS penggajian_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE penggajian_db;
-- Tabel jabatan (dibuat duluan karena direferensi karyawan)
CREATE TABLE jabatan (
    id_jabatan   INT PRIMARY KEY AUTO_INCREMENT,
    nama_jabatan VARCHAR(50) NOT NULL,
    gaji_pokok   DECIMAL(12,2) NOT NULL
);
-- Tabel karyawan
CREATE TABLE karyawan (
    id_karyawan   VARCHAR(10) PRIMARY KEY,   -- format: KAR001, KAR002, dst.
    nama_karyawan VARCHAR(100) NOT NULL,
    no_telepon    VARCHAR(15),
    alamat        TEXT,
    tanggal_masuk DATE NOT NULL,
    status        ENUM('Aktif','Tidak Aktif') DEFAULT 'Aktif',
    id_jabatan    INT NOT NULL,
    FOREIGN KEY (id_jabatan) REFERENCES jabatan(id_jabatan)
);
-- Tabel user (login sistem)
CREATE TABLE user (
    id_user     INT PRIMARY KEY AUTO_INCREMENT,
    username    VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,          -- simpan dalam bentuk hash (password_hash)
    role        ENUM('Admin','Karyawan') NOT NULL DEFAULT 'Karyawan',
    aktif       BOOLEAN DEFAULT TRUE,
    id_karyawan VARCHAR(10) DEFAULT NULL,       -- NULL jika role Admin
    FOREIGN KEY (id_karyawan) REFERENCES karyawan(id_karyawan)
);
-- Tabel komponen_gaji (master tunjangan & potongan)
CREATE TABLE komponen_gaji (
    id_komponen   INT PRIMARY KEY AUTO_INCREMENT,
    nama_komponen VARCHAR(100) NOT NULL,
    jenis         ENUM('Tunjangan','Potongan') NOT NULL,
    nilai         DECIMAL(12,2) NOT NULL
);
-- Tabel gaji (slip gaji per bulan)
CREATE TABLE gaji (
    id_gaji         INT PRIMARY KEY AUTO_INCREMENT,
    id_karyawan     VARCHAR(10) NOT NULL,
    bulan           INT NOT NULL CHECK (bulan BETWEEN 1 AND 12),
    tahun           INT NOT NULL,
    hari_hadir      INT DEFAULT 0,
    hari_sakit      INT DEFAULT 0,
    hari_izin       INT DEFAULT 0,
    hari_alpha      INT DEFAULT 0,
    gaji_pokok      DECIMAL(12,2) NOT NULL,
    total_tunjangan DECIMAL(12,2) DEFAULT 0,
    total_potongan  DECIMAL(12,2) DEFAULT 0,
    gaji_bersih     DECIMAL(12,2) NOT NULL,
    tanggal_bayar   DATE,
    id_pemroses     INT DEFAULT NULL,
    nama_pemroses   VARCHAR(255) DEFAULT NULL,
    UNIQUE (id_karyawan, bulan, tahun),
    FOREIGN KEY (id_karyawan) REFERENCES karyawan(id_karyawan)
);
-- Tabel detail_gaji (rincian komponen tiap slip)
CREATE TABLE detail_gaji (
    id_detail   INT PRIMARY KEY AUTO_INCREMENT,
    id_gaji     INT NOT NULL,
    id_komponen INT NOT NULL,
    nilai       DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_gaji)     REFERENCES gaji(id_gaji),
    FOREIGN KEY (id_komponen) REFERENCES komponen_gaji(id_komponen)
);
-- Data awal: akun admin default
INSERT INTO jabatan (nama_jabatan, gaji_pokok) VALUES ('Administrator', 0);
INSERT INTO user (username, password, role)
VALUES ('admin', '$2y$12$cajdMX4iHDPzZ6RZVV4Sv.Xq5Q41KLuJVfB2E9H1fgu3fBGu992ZS', 'Admin');
