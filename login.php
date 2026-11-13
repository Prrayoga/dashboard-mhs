<?php
require 'config/koneksi.php';
require 'config/auth.php';

// Sudah login? Arahkan ke beranda sesuai role
if (sudah_login()) {
    header("Location: " . halaman_beranda());
    exit;
}

$error = "";
$info = "";

if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'belum_login') {
        $info = "Silakan login terlebih dahulu.";
    } elseif ($_GET['pesan'] == 'logout') {
        $info = "Anda berhasil logout.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, username, nama_lengkap, password, role, mahasiswa_id, aktif
         FROM users WHERE username = ?"
    );
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$user || !password_verify($password, $user['password'])) {
        // Pesan sengaja umum
        $error = "Username atau password salah.";
    } elseif ((int) $user['aktif'] !== 1) {
        // Status akun baru diberitahukan setelah password terbukti benar
        $error = "Akun Anda dinonaktifkan. Hubungi admin.";
    } elseif ($user['role'] === 'mahasiswa' && empty($user['mahasiswa_id'])) {
        $error = "Akun Anda belum terhubung ke data mahasiswa. Hubungi admin.";
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id']      = $user['id'];
        $_SESSION['username']     = $user['username'];
        $_SESSION['nama']         = $user['nama_lengkap'];
        $_SESSION['role']         = $user['role'];
        $_SESSION['mahasiswa_id'] = $user['mahasiswa_id'];

        header("Location: " . halaman_beranda());
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMAHASISWA</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-card">
    <div class="auth-header">
        <div class="logo">🎓</div>
        <h1>SIMAHASISWA</h1>
        <p>Masuk ke Akun Anda</p>
    </div>

    <!-- Alert Notifikasi -->
    <?php if (!empty($info)) : ?>
        <div class="alert alert-info">
            <span>ℹ️</span> <?= htmlspecialchars($info) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger">
            <span>⚠️</span> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">Masuk ke Dashboard</button>
        </div>
    </form>
</div>

</body>
</html>