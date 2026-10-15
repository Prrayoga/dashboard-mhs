<?php
require 'config/koneksi.php';
require 'config/auth.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $query = "DELETE FROM mahasiswa WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php?pesan=hapus");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
