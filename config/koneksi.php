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
