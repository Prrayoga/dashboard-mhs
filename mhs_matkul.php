<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('mahasiswa');
$mid = id_mahasiswa_login();

$katalog = ambil_semua($koneksi,
    "SELECT mk.kode_mk, mk.nama_mk, mk.sks, d.nama AS dosen, n.nilai_angka
     FROM mata_kuliah mk
     LEFT JOIN dosen d ON d.id = mk.dosen_id
     LEFT JOIN nilai n ON n.mata_kuliah_id = mk.id AND n.mahasiswa_id = ?
     ORDER BY mk.kode_mk", "i", $mid);

$judul_halaman = 'Mata Kuliah';
$menu_aktif    = 'matkul';
require 'layout/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><span>📚</span> Katalog Mata Kuliah</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Dosen</th><th>Status Saya</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($katalog as $k) : ?>
                        <tr>
                            <td><?= htmlspecialchars($k['kode_mk']) ?></td>
                            <td><?= htmlspecialchars($k['nama_mk']) ?></td>
                            <td><?= $k['sks'] ?></td>
                            <td>
                                <?php if ($k['dosen'] !== null) : ?>
                                    <?= htmlspecialchars($k['dosen']) ?>
                                <?php else : ?>
                                    <span class="text-muted">Belum ada dosen</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($k['nilai_angka'] !== null) :
                                    $huruf = huruf_mutu($k['nilai_angka']); ?>
                                    <strong><?= $huruf ?></strong>
                                    <?= number_format($k['nilai_angka'], 2) ?>
                                <?php else : ?>
                                    <span class="text-muted">Belum bernilai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($katalog) == 0) : ?>
                        <tr><td colspan="5" class="text-center">Belum ada mata kuliah.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'layout/footer.php'; ?>