<?php
require 'config/koneksi.php';
require 'config/auth.php';
require_once 'config/upload.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Hapus file foto jika ada
    $cek = mysqli_query($koneksi, "SELECT foto FROM mahasiswa WHERE id = $id");
    if ($data = mysqli_fetch_assoc($cek)) {
        hapus_foto($data['foto']);
    }

    $query = "DELETE FROM mahasiswa WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php?pesan=hapus");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
