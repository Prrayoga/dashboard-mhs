<?php
require 'config/koneksi.php';
require 'config/auth.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);

    $query = "INSERT INTO mahasiswa (nim, nama, jurusan, email) VALUES ('$nim', '$nama', '$jurusan', '$email')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php?pesan=tambah");
        exit;
    } else {
        $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}

$judul_halaman = 'Tambah Mahasiswa';
$menu_aktif = 'tambah';

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
            <span>➕</span> Form Tambah Mahasiswa
        </h3>
    </div>
    <div class="card-body">
        <form action="create.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nim">NIM</label>
                    <input type="text" id="nim" name="nim" placeholder="Contoh: 2201001" required autofocus>
                </div>
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" placeholder="Masukkan nama mahasiswa" required>
                </div>
                <div class="form-group">
                    <label for="jurusan">Jurusan</label>
                    <input type="text" id="jurusan" name="jurusan" placeholder="Contoh: Teknik Informatika" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Contoh: nama@example.com" required>
                </div>
            </div>
            <div class="form-footer">
                <button type="submit" class="btn btn-simpan">💾 Simpan Data</button>
                <a href="index.php" class="btn btn-batal">Kembali</a>
            </div>
        </form>
    </div>
</div>

<?php
require 'layout/footer.php';
?>
