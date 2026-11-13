<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('mahasiswa');
$mid = id_mahasiswa_login();

$daftar_nilai = ambil_semua($koneksi,
    "SELECT mk.kode_mk, mk.nama_mk, mk.sks, d.nama AS dosen, n.nilai_angka
     FROM nilai n
     INNER JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
     LEFT JOIN dosen d ON d.id = mk.dosen_id
     WHERE n.mahasiswa_id = ?
     ORDER BY mk.kode_mk", "i", $mid);

$total_sks = 0;
$total_bobot = 0;
foreach ($daftar_nilai as $r) {
    $total_sks   += $r['sks'];
    $total_bobot += $r['nilai_angka'] * $r['sks'];
}
$rata_berbobot = $total_sks > 0 ? $total_bobot / $total_sks : null;

$judul_halaman = 'Nilai Saya';
$menu_aktif    = 'nilai';
require 'layout/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><span>📝</span> Daftar Nilai</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th><th>Kode</th><th>Mata Kuliah</th><th>SKS</th>
                        <th>Dosen</th><th>Nilai</th><th>Mutu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($daftar_nilai as $r) : $huruf = huruf_mutu($r['nilai_angka']); ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($r['kode_mk']) ?></td>
                            <td><?= htmlspecialchars($r['nama_mk']) ?></td>
                            <td><?= $r['sks'] ?></td>
                            <td>
                                <?php if ($r['dosen'] !== null) : ?>
                                    <?= htmlspecialchars($r['dosen']) ?>
                                <?php else : ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($r['nilai_angka'], 2) ?></td>
                            <td><strong><?= $huruf ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($daftar_nilai) == 0) : ?>
                        <tr><td colspan="7" class="text-center">Belum ada nilai.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($rata_berbobot !== null) : ?>
        <div class="card-footer">
            Total SKS: <strong><?= $total_sks ?></strong> &middot;
            Rata-rata berbobot: <strong><?= number_format($rata_berbobot, 2) ?></strong>
            (<?= huruf_mutu($rata_berbobot) ?>)
        </div>
    <?php endif; ?>
</div>

<?php require 'layout/footer.php'; ?>