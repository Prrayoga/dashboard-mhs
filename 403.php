<?php
// Halaman 'akses ditolak'. Dipanggil oleh wajib_role(), tetapi aman
// juga jika dibuka langsung lewat address bar.
require_once 'config/auth.php';
wajib_login();
http_response_code(403);

$judul_halaman = 'Akses Ditolak';
$menu_aktif    = '';
require 'layout/header.php';
?>

<div class="card">
    <div class="error-403">
        <div class="kode">403</div>
        <h2>Akses Ditolak</h2>
        <p>
            Akun Anda (<strong><?= htmlspecialchars(role_user()) ?></strong>)
            tidak memiliki hak untuk membuka halaman ini.
        </p>
        <a href="<?= halaman_beranda() ?>" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</div>

<?php require 'layout/footer.php'; ?>