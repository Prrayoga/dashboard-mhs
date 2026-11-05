<?php
require 'config/koneksi.php';
require 'config/auth.php';
require_once 'config/upload.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $id_post = (int) $_POST['id'];
    $foto_lama = mysqli_real_escape_string($koneksi, $_POST['foto_lama'] ?? '');

    $foto_baru = $foto_lama;
    $foto_upload = upload_foto($_FILES['foto'] ?? ['error' => UPLOAD_ERR_NO_FILE], $error);
    if ($foto_upload !== null) {
        $foto_baru = $foto_upload;
    }

    if (empty($error)) {
        $foto_sql = !empty($foto_baru) ? "'$foto_baru'" : "NULL";
        $query = "UPDATE mahasiswa SET
            nim = '$nim',
            nama = '$nama',
            jurusan = '$jurusan',
            email = '$email',
            foto = $foto_sql
            WHERE id = $id_post";

        if (mysqli_query($koneksi, $query)) {
            if ($foto_upload !== null) {
                hapus_foto($foto_lama);
            }
            header("Location: index.php?pesan=edit");
            exit;
        } else {
            hapus_foto($foto_upload);
            $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    }
}

// Ambil data lama untuk ditampilkan di form
$query = "SELECT * FROM mahasiswa WHERE id = $id";
$hasil = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    header("Location: index.php");
    exit;
}

$judul_halaman = 'Edit Mahasiswa';
$menu_aktif = 'mahasiswa';

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
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
            <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($data['foto'] ?? '') ?>">

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
                    <input type="text" id="jurusan" name="jurusan" value="<?= htmlspecialchars($data['jurusan']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
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
