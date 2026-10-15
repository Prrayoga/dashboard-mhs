<?php

function kelas_aktif($nama_menu, $menu_aktif)
{
    return $nama_menu === $menu_aktif ? 'aktif' : '';
}
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">🎓 SIMAHASISWA</div>
    <ul class="sidebar-menu">
        <li class="sidebar-title">Menu Utama</li>
        <li>
            <a href="dashboard.php" class="<?= kelas_aktif('dashboard', $menu_aktif) ?>">
                <span class="menu-ikon">🏠</span> Dashboard
            </a>
        </li>
        <li class="sidebar-title">Data</li>
        <li>
            <a href="index.php" class="<?= kelas_aktif('mahasiswa', $menu_aktif) ?>">
                <span class="menu-ikon">📋</span> Data Mahasiswa
            </a>
        </li>
        <li>
            <a href="create.php" class="<?= kelas_aktif('tambah', $menu_aktif) ?>">
                <span class="menu-ikon">➕</span> Tambah Mahasiswa
            </a>
        </li>
        <li class="sidebar-title">Akun</li>
        <li>
            <a href="logout.php">
                <span class="menu-ikon">🚪</span> Logout
            </a>
        </li>
    </ul>
</aside>