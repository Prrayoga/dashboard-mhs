<?php
require 'config/koneksi.php';
require 'config/auth.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

$judul_halaman = 'Data Mahasiswa';
$menu_aktif = 'mahasiswa';

// Fitur Pencarian
$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$where = "";
if ($cari !== '') {
    $cari_escape = mysqli_real_escape_string($koneksi, $cari);
    $where = "WHERE nim LIKE '%$cari_escape%' OR nama LIKE '%$cari_escape%' OR jurusan LIKE '%$cari_escape%' OR email LIKE '%$cari_escape%'";
}

// Fitur Pagination
$batas = 5; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($halaman < 1) $halaman = 1;
$awal = ($halaman - 1) * $batas;

// Hitung total data
$query_count = "SELECT COUNT(*) AS total FROM mahasiswa $where";
$hasil_count = mysqli_query($koneksi, $query_count);
$total_data = 0;
if ($hasil_count) {
    $baris_count = mysqli_fetch_assoc($hasil_count);
    $total_data = (int) $baris_count['total'];
}
$total_halaman = ceil($total_data / $batas);
if ($total_halaman < 1) $total_halaman = 1;

// Query data mahasiswa dengan LIMIT
$query = "SELECT * FROM mahasiswa $where ORDER BY id DESC LIMIT $awal, $batas";
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
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="cari" class="search-input" value="<?= htmlspecialchars($cari) ?>" placeholder="Cari data mahasiswa...">
                <button type="submit" class="btn btn-sm btn-primary">🔍 Cari</button>
                <?php if ($cari !== '') : ?>
                    <a href="index.php" class="btn btn-sm btn-secondary">Reset</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 60px; text-align: center;">Foto</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Email</th>
                        <th style="width: 140px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($hasil) > 0) : ?>
                        <?php $no = $awal + 1;
                        while ($baris = mysqli_fetch_assoc($hasil)) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td style="text-align: center;">
                                    <?php if (!empty($baris['foto']) && file_exists('uploads/' . $baris['foto'])) : ?>
                                        <img src="uploads/<?= htmlspecialchars($baris['foto']) ?>" alt="Foto" class="img-avatar">
                                    <?php else : ?>
                                        <div class="avatar-placeholder"><?= strtoupper(mb_substr($baris['nama'], 0, 1)) ?></div>
                                    <?php endif; ?>
                                </td>
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
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">📂</div>
                                    <div class="empty-state-text">
                                        <?= $cari !== '' ? 'Tidak ditemukan data mahasiswa dengan kata kunci "' . htmlspecialchars($cari) . '".' : 'Belum ada data mahasiswa.' ?>
                                    </div>
                                    <?php if ($cari !== '') : ?>
                                        <a href="index.php" class="btn btn-sm btn-secondary">Tampilkan Semua Data</a>
                                    <?php else : ?>
                                        <a href="create.php" class="btn btn-sm btn-primary">➕ Tambah Sekarang</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($total_data > 0) : ?>
        <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div class="text-muted" style="font-size: 13px;">
                Menampilkan <strong><?= min($awal + 1, $total_data) ?></strong> - <strong><?= min($awal + $batas, $total_data) ?></strong> dari <strong><?= $total_data ?></strong> data
            </div>
            <?php if ($total_halaman > 1) : ?>
                <div class="pagination">
                    <?php $param_cari = ($cari !== '') ? '&cari=' . urlencode($cari) : ''; ?>
                    <?php if ($halaman > 1) : ?>
                        <a href="index.php?halaman=<?= $halaman - 1 ?><?= $param_cari ?>" class="pagination-link">&laquo; Sebelumnya</a>
                    <?php else : ?>
                        <span class="pagination-link disabled">&laquo; Sebelumnya</span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_halaman; $i++) : ?>
                        <a href="index.php?halaman=<?= $i ?><?= $param_cari ?>" class="pagination-link <?= ($i == $halaman) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($halaman < $total_halaman) : ?>
                        <a href="index.php?halaman=<?= $halaman + 1 ?><?= $param_cari ?>" class="pagination-link">Selanjutnya &raquo;</a>
                    <?php else : ?>
                        <span class="pagination-link disabled">Selanjutnya &raquo;</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require 'layout/footer.php';
?>