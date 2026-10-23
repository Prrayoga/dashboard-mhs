<?php
// 1. Pengaturan koneksi database
$host = "localhost";
$username = "root";
$password = "";
$database = "db_belajar";

// 2. Membuat koneksi menggunakan mysqli
$koneksi = mysqli_connect($host, $username, $password, $database);

// 3. Cek apakah koneksi berhasil atau gagal
if (!$koneksi) {
    die("Koneksi ke database GAGAL: " . mysqli_connect_error());
}
// (Opsional) set karakter set agar tidak masalah dengan simbol/emoji
mysqli_set_charset($koneksi, "utf8mb4");

// Pastikan kolom foto tersedia pada tabel mahasiswa jika belum ada
$cek_kolom = mysqli_query($koneksi, "SHOW COLUMNS FROM mahasiswa LIKE 'foto'");
if ($cek_kolom && mysqli_num_rows($cek_kolom) == 0) {
    mysqli_query($koneksi, "ALTER TABLE mahasiswa ADD COLUMN foto VARCHAR(255) NULL AFTER email");
}
