<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
require_once 'config/upload.php';
wajib_role('admin');

$error = "";
$nim = $nama = $email = "";
$jurusan_id = 0;
// Proses simpan dijalankan SEBELUM layout dipasang, karena
// header("Location: ...") tidak boleh dipanggil setelah ada output HTML.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jurusan_id = (int) ($_POST['jurusan_id'] ?? 0);
    $email = trim($_POST['email'] ?? '');
    // Validasi field teks
    if ($nim === "" || $nama === "" || $email === "") {
        $error = "NIM, nama, dan email wajib diisi.";
    } elseif (!jurusan_ada($koneksi, $jurusan_id)) {
        $error = "Pilih jurusan yang tersedia.";
    }
    // Proses upload foto dan dokumen secara berurutan.
    $foto_nama = null;
    $dokumen_nama = null;
    if ($error === "") {
        $foto_nama = upload_foto($_FILES['foto'] ?? ['error' => UPLOAD_ERR_NO_FILE], $error);
    }
    if ($error === "") {
        $dokumen_nama = upload_dokumen($_FILES['dokumen'] ?? ['error' => UPLOAD_ERR_NO_FILE], $error);
    }
    // Jika upload salah satu file gagal, bersihkan file yang sempat tersimpan.
    if ($error !== "") {
        hapus_foto($foto_nama);
        hapus_dokumen($dokumen_nama);
    }
    // Simpan ke database hanya jika semua langkah di atas lolos
    if ($error === "") {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO mahasiswa (nim, nama, jurusan_id, email, dokumen, foto) VALUES (?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ssisss", $nim, $nama, $jurusan_id, $email, $dokumen_nama, $foto_nama);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?pesan=tambah");
            exit;
        } else {
            hapus_foto($foto_nama); // data gagal tersimpan -> jangan sisakan file yatim
            hapus_dokumen($dokumen_nama);
            $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
        }
    }
}


$judul_halaman = 'Tambah Mahasiswa';
$menu_aktif = 'tambah';
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
            <span>➕</span> Form Tambah Mahasiswa
        </h3>
    </div>
    <div class="card-body">
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nim">NIM</label>
                    <input type="text" id="nim" name="nim" placeholder="Contoh: 2201001" value="<?= htmlspecialchars($nim) ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" placeholder="Masukkan nama mahasiswa" value="<?= htmlspecialchars($nama) ?>" required>
                </div>
                <div class="form-group">
                    <label for="jurusan">Jurusan</label>
                    <select id="jurusan_id" name="jurusan_id" required>
                        <option value="">Pilih jurusan</option>
                        <?php while ($jurusan = mysqli_fetch_assoc($daftar_jurusan)) : ?>
                            <option value="<?= (int) $jurusan['id'] ?>" <?= $jurusan_id === (int) $jurusan['id'] ? 'selected' : '' ?>><?= htmlspecialchars($jurusan['nama_jurusan']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Contoh: nama@example.com" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <div class="form-group">
                    <label for="dokumen">Dokumen (opsional: PDF, Word, Excel, TXT; maksimal 5 MB)</label>
                    <input type="file" id="dokumen" name="dokumen" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt">
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
