<?php
// Ambil huruf pertama nama untuk ditampilkan di lingkaran avatar
$nama_user = $_SESSION['nama'] ?? 'User';
$inisial = !empty($nama_user) ? strtoupper(mb_substr($nama_user, 0, 1)) : 'U';
$username_user = $_SESSION['username'] ?? 'admin';
?>
<header class="navbar">
    <div class="navbar-kiri">
        <!-- Tombol hamburger: tiga garis dibuat dari 3 elemen <span> -->
        <button type="button" class="hamburger" id="btn-hamburger" aria-label="Buka atau tutup menu">
            <span></span><span></span><span></span>
        </button>
        <span class="navbar-judul"><?= htmlspecialchars($judul_halaman ?? '') ?></span>
    </div>
    <div class="dropdown">
        <button type="button" class="dropdown-toggle" id="dropdown-user-btn">
            <span class="avatar"><?= htmlspecialchars($inisial) ?></span>
            <span class="nama-user"><?= htmlspecialchars($nama_user) ?> ▾</span>
        </button>
        <div class="dropdown-menu" id="dropdown-user-menu">
            <div class="dropdown-info">Login sebagai <strong><?= htmlspecialchars($username_user) ?></strong></div>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>