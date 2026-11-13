<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('mahasiswa');
$mid = id_mahasiswa_login();

// 1. Biodata + jurusan (INNER JOIN)
$bio = ambil_baris($koneksi,
    "SELECT m.nim, m.nama, j.nama_jurusan
     FROM mahasiswa m
     INNER JOIN jurusan j ON j.id = m.jurusan_id
     WHERE m.id = ?", "i", $mid);
if (!$bio) { echo "Data mahasiswa tidak ditemukan."; exit; }

// 2. Ringkasan akademik
$ringkas = ambil_baris($koneksi,
    "SELECT COUNT(n.id) AS jml_mk,
            COALESCE(SUM(mk.sks), 0) AS total_sks,
            ROUND(SUM(n.nilai_angka * mk.sks) / SUM(mk.sks), 2) AS rata_berbobot
     FROM nilai n
     INNER JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
     WHERE n.mahasiswa_id = ?", "i", $mid);

// 3. Tiga nilai terbaru
$terbaru = ambil_semua($koneksi,
    "SELECT mk.nama_mk, n.nilai_angka
     FROM nilai n
     INNER JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
     WHERE n.mahasiswa_id = ?
     ORDER BY n.id DESC
     LIMIT 3", "i", $mid);

$judul_halaman = 'Dashboard Mahasiswa';
$menu_aktif    = 'dashboard';
require 'layout/header.php';
?>

<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Selamat datang, <?= htmlspecialchars($bio['nama']) ?>! 👋</h2>
        <p>
            <?= htmlspecialchars($bio['nim']) ?> &middot;
            <?= htmlspecialchars($bio['nama_jurusan']) ?>
        </p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary"><span>📚</span></div>
        <div class="stat-info">
            <div class="stat-value"><?= $ringkas['jml_mk'] ?></div>
            <div class="stat-label">Mata Kuliah Bernilai</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><span>🎯</span></div>
        <div class="stat-info">
            <div class="stat-value"><?= $ringkas['total_sks'] ?></div>
            <div class="stat-label">Total SKS</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><span>⭐</span></div>
        <div class="stat-info">
            <div class="stat-value">
                <?= $ringkas['rata_berbobot'] === null ? '-' : $ringkas['rata_berbobot'] ?>
            </div>
            <div class="stat-label">Rata-rata Berbobot</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><span>📝</span> Nilai Terbaru</h3>
        <a href="mhs_nilai.php" class="btn btn-sm btn-secondary">Lihat semua nilai &raquo;</a>
    </div>
    <div class="card-body" style="padding:0;">
        <?php if (count($terbaru) == 0) : ?>
            <div class="empty-state">
                <div class="empty-state-icon">📂</div>
                <div class="empty-state-text">Belum ada nilai.</div>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>Mata Kuliah</th><th>Nilai</th><th>Mutu</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($terbaru as $t) : $huruf = huruf_mutu($t['nilai_angka']); ?>
                            <tr>
                                <td><?= htmlspecialchars($t['nama_mk']) ?></td>
                                <td><?= number_format($t['nilai_angka'], 2) ?></td>
                                <td><strong><?= $huruf ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'layout/footer.php'; ?>