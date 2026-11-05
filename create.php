<?php
require 'config/koneksi.php';
require 'config/auth.php';
require_once 'config/upload.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

$error = "";
$nim = $nama = $jurusan = $email = ""; // nilai awal form (kosong)
// Proses simpan dijalankan SEBELUM layout dipasang, karena
// header("Location: ...") tidak boleh dipanggil setelah ada output HTML.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $email = trim($_POST['email'] ?? '');
    // Validasi field teks
    if ($nim === "" || $nama === "" || $jurusan === "" || $email === "") {
        $error = "NIM, nama, jurusan, dan email wajib diisi.";
    }
    // Proses upload foto (opsional). Jika ada masalah, $error terisi.
    $foto_nama = null;
    if ($error === "") {
        $foto_nama = upload_foto($_FILES['foto'] ?? ['error' => UPLOAD_ERR_NO_FILE], $error);
    }
    // Simpan ke database hanya jika semua langkah di atas lolos
    if ($error === "") {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO mahasiswa (nim, nama, jurusan, email, foto) VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "sssss", $nim, $nama, $jurusan, $email, $foto_nama);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?pesan=tambah");
            exit;
        } else {
            hapus_foto($foto_nama); // data gagal tersimpan -> jangan sisakan file yatim
            $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
        }
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
        <form action="create.php" method="POST" enctype="multipart/form-data">
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
                <div class="form-group">
                    <label for="foto">Foto Profil (Opsional)</label>
                    <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png">
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