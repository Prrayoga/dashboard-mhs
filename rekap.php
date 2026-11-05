<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_login();

$rekap_mk = mysqli_query(
    $koneksi,
    "SELECT mk.id, mk.kode_mk, mk.nama_mk, mk.sks, d.nama AS nama_dosen,
            COUNT(n.id) AS peserta,
            ROUND(AVG(n.nilai_angka), 2) AS rata_rata,
            MAX(n.nilai_angka) AS tertinggi,
            MIN(n.nilai_angka) AS terendah
     FROM mata_kuliah mk
     LEFT JOIN dosen d ON d.id = mk.dosen_id
     LEFT JOIN nilai n ON n.mata_kuliah_id = mk.id
     GROUP BY mk.id, mk.kode_mk, mk.nama_mk, mk.sks, d.nama
     ORDER BY mk.kode_mk"
);

$peringkat = mysqli_query(
    $koneksi,
    "SELECT m.id, m.nim, m.nama, j.nama_jurusan,
            ROUND(SUM(n.nilai_angka * mk.sks) / NULLIF(SUM(mk.sks), 0), 2) AS rata_berbobot,
            COUNT(n.id) AS jumlah_mk
     FROM mahasiswa m
     INNER JOIN jurusan j ON j.id = m.jurusan_id
     INNER JOIN nilai n ON n.mahasiswa_id = m.id
     INNER JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
     GROUP BY m.id, m.nim, m.nama, j.nama_jurusan
     ORDER BY rata_berbobot DESC, m.nama
     LIMIT 5"
);

$belum_nilai = mysqli_query(
    $koneksi,
    "SELECT m.id, m.nim, m.nama, j.nama_jurusan
     FROM mahasiswa m
     INNER JOIN jurusan j ON j.id = m.jurusan_id
     LEFT JOIN nilai n ON n.mahasiswa_id = m.id
     WHERE n.id IS NULL
     ORDER BY m.nama"
);

$judul_halaman = 'Rekap Nilai';
$menu_aktif = 'rekap';
require 'layout/header.php';
?>

<div class="card">
    <div class="card-header"><h2 class="card-title">Rekap per Mata Kuliah</h2></div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Kode</th><th>Mata Kuliah</th><th>Dosen</th><th>SKS</th><th>Peserta</th><th>Rata-rata</th><th>Nilai Tertinggi</th><th>Nilai Terendah</th></tr></thead>
                <tbody>
                    <?php if (mysqli_num_rows($rekap_mk) > 0) : ?>
                        <?php while ($baris = mysqli_fetch_assoc($rekap_mk)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($baris['kode_mk']) ?></td>
                                <td><?= htmlspecialchars($baris['nama_mk']) ?></td>
                                <td><?= htmlspecialchars($baris['nama_dosen'] ?? 'Belum ditentukan') ?></td>
                                <td><?= (int) $baris['sks'] ?></td>
                                <td><?= (int) $baris['peserta'] ?></td>
                                <td><?= $baris['rata_rata'] === null ? '-' : number_format((float) $baris['rata_rata'], 2) ?></td>
                                <td><?= $baris['tertinggi'] === null ? '-' : number_format((float) $baris['tertinggi'], 2) ?></td>
                                <td><?= $baris['terendah'] === null ? '-' : number_format((float) $baris['terendah'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr><td colspan="8"><div class="empty-state"><div class="empty-state-text">Belum ada mata kuliah.</div></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2 class="card-title">Lima Rata-rata Berbobot Tertinggi</h2></div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Peringkat</th><th>NIM</th><th>Nama</th><th>Jurusan</th><th>Mata Kuliah</th><th>Rata-rata Berbobot</th><th>Detail</th></tr></thead>
                <tbody>
                    <?php if (mysqli_num_rows($peringkat) > 0) : ?>
                        <?php $nomor = 1; while ($baris = mysqli_fetch_assoc($peringkat)) : ?>
                            <tr>
                                <td><?= $nomor++ ?></td>
                                <td><?= htmlspecialchars($baris['nim']) ?></td>
                                <td><?= htmlspecialchars($baris['nama']) ?></td>
                                <td><?= htmlspecialchars($baris['nama_jurusan']) ?></td>
                                <td><?= (int) $baris['jumlah_mk'] ?></td>
                                <td><?= number_format((float) $baris['rata_berbobot'], 2) ?> (<?= huruf_mutu((float) $baris['rata_berbobot']) ?>)</td>
                                <td><a href="detail.php?id=<?= (int) $baris['id'] ?>" class="btn btn-sm btn-detail">Lihat detail</a></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr><td colspan="7"><div class="empty-state"><div class="empty-state-text">Peringkat akan muncul setelah nilai dimasukkan.</div></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2 class="card-title">Mahasiswa yang Belum Memiliki Nilai</h2></div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>NIM</th><th>Nama</th><th>Jurusan</th><th>Detail</th></tr></thead>
                <tbody>
                    <?php if (mysqli_num_rows($belum_nilai) > 0) : ?>
                        <?php while ($baris = mysqli_fetch_assoc($belum_nilai)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($baris['nim']) ?></td>
                                <td><?= htmlspecialchars($baris['nama']) ?></td>
                                <td><?= htmlspecialchars($baris['nama_jurusan']) ?></td>
                                <td><a href="detail.php?id=<?= (int) $baris['id'] ?>" class="btn btn-sm btn-detail">Tambah nilai</a></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr><td colspan="4"><div class="empty-state"><div class="empty-state-text">Semua mahasiswa sudah memiliki nilai.</div></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'layout/footer.php'; ?>
