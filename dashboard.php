<?php
require 'config/koneksi.php';
require 'config/auth.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

$judul_halaman = 'Dashboard';
$menu_aktif = 'dashboard';

// Ambil statistik data
$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM mahasiswa");
$total_mhs = mysqli_fetch_assoc($query_total)['total'] ?? 0;

$query_jurusan = mysqli_query($koneksi, "SELECT COUNT(DISTINCT jurusan) as total FROM mahasiswa");
$total_jurusan = mysqli_fetch_assoc($query_jurusan)['total'] ?? 0;

$query_users = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users");
$total_users = mysqli_fetch_assoc($query_users)['total'] ?? 0;

// Ambil 5 mahasiswa terbaru
$query_terbaru = mysqli_query($koneksi, "SELECT * FROM mahasiswa ORDER BY id DESC LIMIT 5");

require 'layout/header.php';
?>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?>! 👋</h2>
        <p>Sistem Informasi Manajemen Data Mahasiswa (SIMAHASISWA). Kelola data akademik dengan cepat, mudah, dan terorganisir.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="create.php" class="btn btn-primary" style="background:#ffffff; color:#1f2d3d; font-weight:600;">➕ Tambah Mahasiswa</a>
        <a href="index.php" class="btn btn-secondary" style="background:rgba(255,255,255,0.15); color:#fff; border-color:transparent;">📋 Lihat Semua</a>
    </div>
</div>

<!-- Statistik Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <span>👨‍🎓</span>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($total_mhs) ?></div>
            <div class="stat-label">Total Mahasiswa</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <span>📚</span>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($total_jurusan) ?></div>
            <div class="stat-label">Program Studi / Jurusan</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">
            <span>👥</span>
        </div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($total_users) ?></div>
            <div class="stat-label">Pengguna Terdaftar</div>
        </div>
    </div>
</div>

<!-- Data Terbaru Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <span>🕒</span> Data Mahasiswa Terkini
        </h3>
        <a href="index.php" class="btn btn-sm btn-secondary">Lihat Semua Data →</a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Email</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query_terbaru) > 0) : ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_terbaru)) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['nim']) ?></strong></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><span class="badge badge-jurusan"><?= htmlspecialchars($row['jurusan']) ?></span></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td style="text-align: center;">
                                    <div class="action-buttons">
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-edit" title="Edit">✏️ Edit</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">📂</div>
                                    <div class="empty-state-text">Belum ada data mahasiswa yang terdaftar.</div>
                                    <a href="create.php" class="btn btn-sm btn-primary">➕ Tambah Mahasiswa Baru</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require 'layout/footer.php';
?>
