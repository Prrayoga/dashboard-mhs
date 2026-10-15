<?php
require 'config/koneksi.php';
require 'config/auth.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

$judul_halaman = 'Data Mahasiswa';
$menu_aktif = 'mahasiswa';

$query = "SELECT * FROM mahasiswa ORDER BY id DESC";
$hasil = mysqli_query($koneksi, $query);

require 'layout/header.php';
?>

<!-- Notifikasi Alert -->
<?php if (isset($_GET['pesan'])) : ?>
    <?php if ($_GET['pesan'] == 'tambah') : ?>
        <div class="alert alert-success">
            <span>✅</span> Data mahasiswa berhasil ditambahkan!
        </div>
    <?php elseif ($_GET['pesan'] == 'edit') : ?>
        <div class="alert alert-success">
            <span>✅</span> Data mahasiswa berhasil diperbarui!
        </div>
    <?php elseif ($_GET['pesan'] == 'hapus') : ?>
        <div class="alert alert-success">
            <span>🗑️</span> Data mahasiswa berhasil dihapus!
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Tabel Data Mahasiswa -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <span>📋</span> Daftar Mahasiswa
        </h3>
        <a href="create.php" class="btn btn-primary">➕ Tambah Mahasiswa</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Email</th>
                        <th style="width: 140px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($hasil) > 0) : ?>
                        <?php $no = 1; while ($baris = mysqli_fetch_assoc($hasil)) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($baris['nim']) ?></strong></td>
                                <td><?= htmlspecialchars($baris['nama']) ?></td>
                                <td><span class="badge badge-jurusan"><?= htmlspecialchars($baris['jurusan']) ?></span></td>
                                <td><?= htmlspecialchars($baris['email']) ?></td>
                                <td style="text-align: center;">
                                    <div class="action-buttons">
                                        <a href="edit.php?id=<?= $baris['id'] ?>" class="btn btn-sm btn-edit" title="Edit">✏️ Edit</a>
                                        <a href="delete.php?id=<?= $baris['id'] ?>" class="btn btn-sm btn-hapus" onclick="return confirm('Yakin ingin menghapus data ini?')" title="Hapus">🗑️ Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">📂</div>
                                    <div class="empty-state-text">Belum ada data mahasiswa.</div>
                                    <a href="create.php" class="btn btn-sm btn-primary">➕ Tambah Sekarang</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Form Tambah Cepat -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <span>➕</span> Tambah Mahasiswa Cepat
        </h3>
    </div>
    <div class="card-body">
        <form action="create.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nim">NIM</label>
                    <input type="text" id="nim" name="nim" placeholder="Contoh: 2201001" required>
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
            </div>
        </form>
    </div>
</div>

<?php
require 'layout/footer.php';
?>