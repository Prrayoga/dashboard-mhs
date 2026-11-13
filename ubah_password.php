<?php
require 'config/koneksi.php';
require 'config/auth.php';
wajib_login(); // semua role boleh membuka

$uid    = (int) $_SESSION['user_id']; // akun sendiri, dari session
$error  = "";
$sukses = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lama    = $_POST['password_lama'] ?? '';
    $baru    = $_POST['password_baru'] ?? '';
    $konfirm = $_POST['konfirmasi'] ?? '';

    $stmt = mysqli_prepare($koneksi, "SELECT password FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $uid);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$user || !password_verify($lama, $user['password'])) {
        $error = "Password lama salah.";
    } elseif (strlen($baru) < 6) {
        $error = "Password baru minimal 6 karakter.";
    } elseif ($baru !== $konfirm) {
        $error = "Konfirmasi password baru tidak sama.";
    } elseif ($baru === $lama) {
        $error = "Password baru tidak boleh sama dengan password lama.";
    } else {
        $hash = password_hash($baru, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($koneksi, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hash, $uid);
        mysqli_stmt_execute($stmt);
        session_regenerate_id(true);
        $sukses = "Password berhasil diubah.";
    }
}

$judul_halaman = 'Ubah Password';
$menu_aktif    = 'password';
require 'layout/header.php';
?>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title"><span>🔒</span> Form Ubah Password</h3>
    </div>
    <div class="card-body">
        <?php if ($sukses != "") : ?>
            <div class="alert alert-success"><?= htmlspecialchars($sukses) ?></div>
        <?php endif; ?>
        <?php if ($error != "") : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="ubah_password.php" method="POST">
            <div class="form-group">
                <label for="password_lama">Password Lama</label>
                <input type="password" id="password_lama" name="password_lama" required>
            </div>

            <div class="form-group">
                <label for="password_baru">Password Baru (minimal 6 karakter)</label>
                <input type="password" id="password_baru" name="password_baru" required>
            </div>

            <div class="form-group">
                <label for="konfirmasi">Konfirmasi Password Baru</label>
                <input type="password" id="konfirmasi" name="konfirmasi" required>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-simpan">💾 Simpan Password</button>
            </div>
        </form>
    </div>
</div>

<?php require 'layout/footer.php'; ?>