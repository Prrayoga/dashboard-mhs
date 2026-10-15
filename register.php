<?php
require 'config/koneksi.php';
require 'config/auth.php';

if (sudah_login()) {
    header("Location: dashboard.php");
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - SIMAHASISWA</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-card">
    <div class="auth-header">
        <div class="logo">🎓</div>
        <h1>SIMAHASISWA</h1>
        <p>Pendaftaran Akun Baru</p>
    </div>

    <!-- Alert Notifikasi Error -->
    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger">
            <span>⚠️</span> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" required
                value="<?= isset($nama) ? htmlspecialchars($nama) : '' ?>">
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="4-20 karakter huruf/angka" required
                value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
        </div>

        <div class="form-group">
            <label for="konfirmasi">Konfirmasi Password</label>
            <input type="password" id="konfirmasi" name="konfirmasi" placeholder="Ulangi password" required>
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">Daftar Akun</button>
        </div>
    </form>

    <div class="auth-footer">
        Sudah memiliki akun? <a href="login.php">Masuk Disini</a>
    </div>
</div>

</body>
</html>