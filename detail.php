<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('admin');

$id = (int) ($_GET['id'] ?? 0);
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT m.*, j.nama_jurusan
     FROM mahasiswa m
     INNER JOIN jurusan j ON j.id = m.jurusan_id
     WHERE m.id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$mahasiswa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$mahasiswa) {
    header("Location: index.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $mata_kuliah_id = (int) ($_POST['mata_kuliah_id'] ?? 0);
        $nilai_input = trim($_POST['nilai_angka'] ?? '');

        if ($mata_kuliah_id < 1 || !is_numeric($nilai_input) || (float) $nilai_input < 0 || (float) $nilai_input > 100) {
            $error = "Pilih mata kuliah dan isi nilai antara 0 sampai 100.";
        } else {
            $nilai_angka = (float) $nilai_input;
            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO nilai (mahasiswa_id, mata_kuliah_id, nilai_angka) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "iid", $id, $mata_kuliah_id, $nilai_angka);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: detail.php?id=$id&pesan=tambah");
                exit;
            }
            $error = mysqli_errno($koneksi) === 1062
                ? "Nilai untuk mata kuliah tersebut sudah ada."
                : "Nilai gagal disimpan. Periksa pilihan mata kuliah dan coba lagi.";
        }
    } elseif ($aksi === 'hapus') {
        $nilai_id = (int) ($_POST['nilai_id'] ?? 0);
        $stmt = mysqli_prepare($koneksi, "DELETE FROM nilai WHERE id = ? AND mahasiswa_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $nilai_id, $id);
        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1) {
            header("Location: detail.php?id=$id&pesan=hapus");
            exit;
        }
        $error = "Nilai tidak ditemukan atau gagal dihapus.";
    }
}

$stmt_nilai = mysqli_prepare(
    $koneksi,
    "SELECT n.id, n.nilai_angka, mk.kode_mk, mk.nama_mk, mk.sks, d.nama AS nama_dosen
     FROM nilai n
     INNER JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
     LEFT JOIN dosen d ON d.id = mk.dosen_id
     WHERE n.mahasiswa_id = ?
     ORDER BY mk.kode_mk"
);
mysqli_stmt_bind_param($stmt_nilai, "i", $id);
mysqli_stmt_execute($stmt_nilai);
$daftar_nilai = mysqli_stmt_get_result($stmt_nilai);

$stmt_agregat = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(n.id) AS jumlah_nilai,
            COALESCE(SUM(mk.sks), 0) AS total_sks,
            ROUND(SUM(n.nilai_angka * mk.sks) / NULLIF(SUM(mk.sks), 0), 2) AS rata_berbobot
     FROM nilai n
     INNER JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
     WHERE n.mahasiswa_id = ?"
);
mysqli_stmt_bind_param($stmt_agregat, "i", $id);
mysqli_stmt_execute($stmt_agregat);
$agregat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_agregat));
$rata_berbobot = $agregat['rata_berbobot'] === null ? null : (float) $agregat['rata_berbobot'];

$stmt_opsi = mysqli_prepare(
    $koneksi,
    "SELECT mk.id, mk.kode_mk, mk.nama_mk, mk.sks
     FROM mata_kuliah mk
     LEFT JOIN nilai n ON n.mata_kuliah_id = mk.id AND n.mahasiswa_id = ?
     WHERE n.id IS NULL
     ORDER BY mk.kode_mk"
);
mysqli_stmt_bind_param($stmt_opsi, "i", $id);
mysqli_stmt_execute($stmt_opsi);
$mata_kuliah_tersedia = mysqli_stmt_get_result($stmt_opsi);

$judul_halaman = 'Detail Mahasiswa';
$menu_aktif = 'mahasiswa';
require 'layout/header.php';
?>

<?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'tambah') : ?>
    <div class="alert alert-success">Nilai berhasil ditambahkan.</div>
<?php elseif (isset($_GET['pesan']) && $_GET['pesan'] === 'hapus') : ?>
    <div class="alert alert-success">Nilai berhasil dihapus.</div>
<?php endif; ?>
<?php if ($error !== '') : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Biodata Mahasiswa</h2>
        <a href="index.php" class="btn btn-sm btn-secondary">Kembali ke daftar</a>
    </div>
    <div class="card-body">
        <dl class="detail-grid">
            <div><dt>NIM</dt><dd><?= htmlspecialchars($mahasiswa['nim']) ?></dd></div>
            <div><dt>Nama</dt><dd><?= htmlspecialchars($mahasiswa['nama']) ?></dd></div>
            <div><dt>Jurusan</dt><dd><?= htmlspecialchars($mahasiswa['nama_jurusan']) ?></dd></div>
            <div><dt>Email</dt><dd><?= htmlspecialchars($mahasiswa['email']) ?></dd></div>
            <?php if (!empty($mahasiswa['dokumen']) && file_exists('uploads/dokumen/' . basename($mahasiswa['dokumen']))) : ?>
                <div><dt>Dokumen</dt><dd><a href="uploads/dokumen/<?= htmlspecialchars(basename($mahasiswa['dokumen']), ENT_QUOTES, 'UTF-8') ?>">Lihat dokumen</a></dd></div>
            <?php endif; ?>
        </dl>
        <div class="grade-summary">
            <span>Jumlah mata kuliah: <strong><?= (int) $agregat['jumlah_nilai'] ?></strong></span>
            <span>Total SKS: <strong><?= (int) $agregat['total_sks'] ?></strong></span>
            <span>Rata-rata berbobot: <strong><?= $rata_berbobot === null ? '-' : number_format($rata_berbobot, 2) . ' (' . huruf_mutu($rata_berbobot) . ')' ?></strong></span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2 class="card-title">Nilai per Mata Kuliah</h2></div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Kode</th><th>Mata Kuliah</th><th>Dosen</th><th>SKS</th><th>Nilai</th><th>Huruf</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php if (mysqli_num_rows($daftar_nilai) > 0) : ?>
                        <?php while ($nilai = mysqli_fetch_assoc($daftar_nilai)) : ?>
                            <?php $angka = (float) $nilai['nilai_angka']; ?>
                            <tr>
                                <td><?= htmlspecialchars($nilai['kode_mk']) ?></td>
                                <td><?= htmlspecialchars($nilai['nama_mk']) ?></td>
                                <td><?= htmlspecialchars($nilai['nama_dosen'] ?? 'Belum ditentukan') ?></td>
                                <td><?= (int) $nilai['sks'] ?></td>
                                <td><?= number_format($angka, 2) ?></td>
                                <td><?= huruf_mutu($angka) ?></td>
                                <td>
                                    <form action="detail.php?id=<?= $id ?>" method="POST" class="inline-form" onsubmit="return confirm('Hapus nilai mata kuliah ini?')">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="nilai_id" value="<?= (int) $nilai['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-hapus">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr><td colspan="7"><div class="empty-state"><div class="empty-state-text">Mahasiswa ini belum memiliki nilai.</div></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2 class="card-title">Tambah Nilai</h2></div>
    <div class="card-body">
        <?php if (mysqli_num_rows($mata_kuliah_tersedia) > 0) : ?>
            <form action="detail.php?id=<?= $id ?>" method="POST">
                <input type="hidden" name="aksi" value="tambah">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="mata_kuliah_id">Mata Kuliah</label>
                        <select id="mata_kuliah_id" name="mata_kuliah_id" required>
                            <option value="">Pilih mata kuliah</option>
                            <?php while ($mk = mysqli_fetch_assoc($mata_kuliah_tersedia)) : ?>
                                <option value="<?= (int) $mk['id'] ?>"><?= htmlspecialchars($mk['kode_mk'] . ' - ' . $mk['nama_mk'] . ' (' . $mk['sks'] . ' SKS)') ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nilai_angka">Nilai (0-100)</label>
                        <input type="number" id="nilai_angka" name="nilai_angka" min="0" max="100" step="0.01" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Nilai</button>
            </form>
        <?php else : ?>
            <p class="text-muted">Semua mata kuliah sudah memiliki nilai untuk mahasiswa ini.</p>
        <?php endif; ?>
    </div>
</div>

<?php require 'layout/footer.php'; ?>
