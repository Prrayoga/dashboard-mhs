<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('mahasiswa');
$mid = id_mahasiswa_login();

$p = ambil_baris($koneksi,
    "SELECT m.nim, m.nama, m.email, m.foto, m.dokumen, j.nama_jurusan
     FROM mahasiswa m
     INNER JOIN jurusan j ON j.id = m.jurusan_id
     WHERE m.id = ?", "i", $mid);
if (!$p) { echo "Data mahasiswa tidak ditemukan."; exit; }

$judul_halaman = 'Profil Saya';
$menu_aktif    = 'profil';
require 'layout/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><span>👤</span> Biodata Mahasiswa</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($p['foto']) && file_exists('uploads/' . $p['foto'])) : ?>
            <img src="uploads/<?= htmlspecialchars($p['foto']) ?>"
                 alt="Foto <?= htmlspecialchars($p['nama']) ?>"
                 style="width: 90px; height: 90px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd8ce; margin-bottom: 16px;">
        <?php endif; ?>

        <dl class="detail-grid">
            <div><dt>NIM</dt><dd><?= htmlspecialchars($p['nim']) ?></dd></div>
            <div><dt>Nama</dt><dd><?= htmlspecialchars($p['nama']) ?></dd></div>
            <div><dt>Jurusan</dt><dd><?= htmlspecialchars($p['nama_jurusan']) ?></dd></div>
            <div><dt>Email</dt><dd><?= htmlspecialchars($p['email']) ?></dd></div>
            <div>
                <dt>Dokumen</dt>
                <dd>
                    <?php if (!empty($p['dokumen']) && file_exists('uploads/dokumen/' . basename($p['dokumen']))) : ?>
                        <a href="uploads/dokumen/<?= htmlspecialchars(basename($p['dokumen']), ENT_QUOTES, 'UTF-8') ?>"
                           class="dok-link" download>
                            <?= strtoupper(pathinfo($p['dokumen'], PATHINFO_EXTENSION)) ?>
                        </a>
                    <?php else : ?>
                        <span class="text-muted">Belum ada dokumen</span>
                    <?php endif; ?>
                </dd>
            </div>
        </dl>

        <p class="text-muted" style="margin-top: 18px;">Untuk mengubah data pribadi, hubungi admin.</p>
    </div>
</div>

<?php require 'layout/footer.php'; ?>