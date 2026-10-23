<?php
require 'config/koneksi.php';
require 'config/auth.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $id_post = (int) $_POST['id'];

    $query = "UPDATE mahasiswa SET
        nim = '$nim',
        nama = '$nama',
        jurusan = '$jurusan',
        email = '$email'
        WHERE id = $id_post";

    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php?pesan=edit");
        exit;
    } else {
        $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
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
        <form action="edit.php?id=<?= $data['id'] ?>" method="POST">
            <input type="hidden" name="id" value="<?= $data['id'] ?>">

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