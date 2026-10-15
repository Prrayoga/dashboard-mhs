<?php
require 'config/koneksi.php';
require 'config/auth.php';
// Kalau sudah login, tidak perlu lihat halaman login lagi
if (sudah_login()) {
    header("Location: index.php");
    exit;
}
$error = "";
$info = "";
// Pesan dari halaman lain (redirect)
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'belum_login') {
        $info = "Silakan login terlebih dahulu.";
    } elseif ($_GET['pesan'] == 'logout') {
        $info = "Anda berhasil logout.";
    } elseif ($_GET['pesan'] == 'registrasi') {
        $info = "Registrasi berhasil, silakan login.";
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    // Prepared statement: tanda ? diisi belakangan sehingga input
    // user tidak pernah dianggap sebagai bagian dari perintah SQL.
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, username, nama_lengkap, password FROM users WHERE username = ?"
    );
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($hasil);
    // password_verify membandingkan password yang diketik dengan HASH di database
    if ($user && password_verify($password, $user['password'])) {
        // Buat ID session baru setelah login (mencegah session fixation)
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama_lengkap'];
        header("Location: index.php");
        exit;
    } else {
        // Pesan sengaja dibuat umum: tidak memberi tahu apakah username atau password yang salah
        $error = "Username atau password salah.";
    }
}

?>

<form action="login.php" method="POST">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required autofocus>
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>
    <div style="margin-top:20px;">
        <button type="submit" class="btn btn-simpan" style="width:100%;">Login</button>
    </div>
</form>