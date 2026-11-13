<?php

function kelas_aktif($nama_menu, $menu_aktif)
{
    return $nama_menu === $menu_aktif ? 'aktif' : '';
}
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">🎓 SIMAHASISWA</div>

    <ul class="sidebar-menu">

        <?php if (adalah_admin()) : ?>
            <!-- ================= MENU ADMIN ================= -->
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
            <li>
                <a href="rekap.php" class="<?= kelas_aktif('rekap', $menu_aktif) ?>">
                    <span class="menu-ikon">📊</span> Rekap Nilai
                </a>
            </li>

            <li class="sidebar-title">Pengguna</li>
            <li>
                <a href="akun.php" class="<?= kelas_aktif('akun', $menu_aktif) ?>">
                    <span class="menu-ikon">🔑</span> Kelola Akun
                </a>
            </li>

        <?php else : ?>
            <!-- =============== MENU MAHASISWA =============== -->
            <li class="sidebar-title">Menu Utama</li>
            <li>
                <a href="mhs_dashboard.php" class="<?= kelas_aktif('dashboard', $menu_aktif) ?>">
                    <span class="menu-ikon">🏠</span> Dashboard
                </a>
            </li>

            <li class="sidebar-title">Akademik</li>
            <li>
                <a href="mhs_nilai.php" class="<?= kelas_aktif('nilai', $menu_aktif) ?>">
                    <span class="menu-ikon">📝</span> Nilai Saya
                </a>
            </li>
            <li>
                <a href="mhs_matkul.php" class="<?= kelas_aktif('matkul', $menu_aktif) ?>">
                    <span class="menu-ikon">📚</span> Mata Kuliah
                </a>
            </li>

            <li class="sidebar-title">Profil</li>
            <li>
                <a href="mhs_profil.php" class="<?= kelas_aktif('profil', $menu_aktif) ?>">
                    <span class="menu-ikon">👤</span> Profil Saya
                </a>
            </li>
        <?php endif; ?>

        <!-- ============ MENU UNTUK SEMUA ROLE ============ -->
        <li class="sidebar-title">Akun</li>
        <li>
            <a href="ubah_password.php" class="<?= kelas_aktif('password', $menu_aktif) ?>">
                <span class="menu-ikon">🔒</span> Ubah Password
            </a>
        </li>
        <li>
            <a href="logout.php">
                <span class="menu-ikon">🚪</span> Logout
            </a>
        </li>
    </ul>
</aside>