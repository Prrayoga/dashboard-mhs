<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
require_once 'config/upload.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$error = "";

// Ambil data lama untuk ditampilkan di form dan mempertahankan file yang tidak diganti.
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT m.*, j.nama_jurusan FROM mahasiswa m INNER JOIN jurusan j ON j.id = m.jurusan_id WHERE m.id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jurusan_id = (int) ($_POST['jurusan_id'] ?? 0);
    $email = trim($_POST['email'] ?? '');

    if ($nim === "" || $nama === "" || $email === "") {
        $error = "NIM, nama, dan email wajib diisi.";
    } elseif (!jurusan_ada($koneksi, $jurusan_id)) {
        $error = "Pilih jurusan yang tersedia.";
    }

    $foto_nama = $data['foto'];
    $foto_baru = null;
    $dokumen_nama = $data['dokumen'] ?? null;
    $dokumen_baru = null;

    if ($error === "") {
        $foto_baru = upload_foto($_FILES['foto'] ?? ['error' => UPLOAD_ERR_NO_FILE], $error);
        if ($foto_baru !== null) {
            $foto_nama = $foto_baru;
        }
    }
    if ($error === "") {
        $dokumen_baru = upload_dokumen($_FILES['dokumen'] ?? ['error' => UPLOAD_ERR_NO_FILE], $error);
        if ($dokumen_baru !== null) {
            $dokumen_nama = $dokumen_baru;
        }
    }
    if ($error !== "") {
        hapus_foto($foto_baru);
        hapus_dokumen($dokumen_baru);
    }

    if ($error === "") {
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE mahasiswa SET nim=?, nama=?, jurusan_id=?, email=?, dokumen=?, foto=? WHERE id=?"
        );
        mysqli_stmt_bind_param($stmt, "ssisssi", $nim, $nama, $jurusan_id, $email, $dokumen_nama, $foto_nama, $id);
        if (mysqli_stmt_execute($stmt)) {
            if ($foto_baru !== null) {
                hapus_foto($data['foto']);
            }
            if ($dokumen_baru !== null) {
                hapus_dokumen($data['dokumen'] ?? null);
            }
            header("Location: index.php?pesan=edit");
            exit;
        } else {
            hapus_foto($foto_baru);
            hapus_dokumen($dokumen_baru);
            $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    }
}

$judul_halaman = 'Edit Mahasiswa';
$menu_aktif = 'mahasiswa';
$daftar_jurusan = daftar_jurusan($koneksi);

require 'layout/header.php';
?>

<?php if (!empty($error)) : ?>
    <div class="alert alert-danger">
        <span>⚠️</span> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3 class="card-title">
            <span>✏️</span> Edit Data Mahasiswa
        </h3>
    </div>
    <div class="card-body">
        <form action="edit.php?id=<?= $data['id'] ?>" method="POST" enctype="multipart/form-data">

            <div class="form-grid">
                <div class="form-group">
                    <label for="nim">NIM</label>
                    <input type="text" id="nim" name="nim" value="<?= htmlspecialchars($data['nim']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="jurusan">Jurusan</label>
                    <select id="jurusan_id" name="jurusan_id" required>
                        <option value="">Pilih jurusan</option>
                        <?php while ($jurusan = mysqli_fetch_assoc($daftar_jurusan)) : ?>
                            <option value="<?= (int) $jurusan['id'] ?>" <?= (int) $data['jurusan_id'] === (int) $jurusan['id'] ? 'selected' : '' ?>><?= htmlspecialchars($jurusan['nama_jurusan']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="dokumen">Ganti Dokumen (kosongkan jika tidak ingin mengganti)</label>
                    <?php if (!empty($data['dokumen']) && file_exists('uploads/dokumen/' . basename($data['dokumen']))) : ?>
                        <div class="text-muted" style="margin-bottom: 8px;">
                            Dokumen saat ini:
                            <a href="uploads/dokumen/<?= htmlspecialchars(basename($data['dokumen']), ENT_QUOTES, 'UTF-8') ?>">Lihat dokumen</a>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="dokumen" name="dokumen" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt">
                </div>

                <div class="form-group">
                    <label for="foto">Foto Mahasiswa</label>
                    <?php if (!empty($data['foto']) && file_exists('uploads/' . $data['foto'])) : ?>
                        <div style="margin-bottom: 8px;">
                            <img src="uploads/<?= htmlspecialchars($data['foto']) ?>" alt="Foto saat ini" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png">
                    <small class="text-muted" style="display: block; margin-top: 4px;">Pilih file baru jika ingin mengganti foto.</small>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-warning">🔄 Perbarui Data</button>
                <a href="index.php" class="btn btn-batal">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php
require 'layout/footer.php';
?>
