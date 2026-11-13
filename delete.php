<?php
require 'config/koneksi.php';
require 'config/auth.php';
require_once 'config/upload.php';
wajib_role('admin');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmtCek = mysqli_prepare($koneksi, "SELECT foto, dokumen FROM mahasiswa WHERE id = ?");
    mysqli_stmt_bind_param($stmtCek, "i", $id);
    mysqli_stmt_execute($stmtCek);
    $hasilCek = mysqli_stmt_get_result($stmtCek);
    $data = mysqli_fetch_assoc($hasilCek);

    $stmt = mysqli_prepare($koneksi, "DELETE FROM mahasiswa WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        if ($data) {
            hapus_foto($data['foto']);
            hapus_dokumen($data['dokumen']);
        }
        header("Location: index.php?pesan=hapus");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
