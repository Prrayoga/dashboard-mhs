<?php
require 'config/koneksi.php';
require 'config/auth.php';

if (sudah_login()) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $nama = trim($_POST['nama_lengkap']);
    $password = trim($_POST['password']);
    $konfirm = trim($_POST['konfirmasi']);

    // --- Validasi di sisi server ---
    if ($username == "" || $nama == "" || $password == "") {
        $error = "Semua field wajib diisi.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
        $error = "Username 4-20 karakter, hanya huruf, angka, dan underscore.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $konfirm) {
        $error = "Konfirmasi password tidak sama.";
    } else {
        // Cek apakah username sudah dipakai
        $cek = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);
        if (mysqli_stmt_num_rows($cek) > 0) {
            $error = "Username sudah dipakai, pilih yang lain.";
        } else {
            // Ubah password menjadi hash sebelum disimpan
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO users (username, nama_lengkap, password) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "sss", $username, $nama, $hash);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: login.php?pesan=registrasi");
                exit;
            } else {
                $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<form action="register.php" method="POST">
    <label for="nama_lengkap">Nama Lengkap</label>
    <input type="text" id="nama_lengkap" name="nama_lengkap" required
        value="<?= isset($nama) ? htmlspecialchars($nama) : '' ?>">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required
        value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>
    <label for="konfirmasi">Konfirmasi Password</label>
    <input type="password" id="konfirmasi" name="konfirmasi" required>
    <div style="margin-top:20px;">
        <button type="submit" class="btn btn-simpan" style="width:100%;">Daftar</button>
    </div>
</form>