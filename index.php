<?php
require 'config/koneksi.php';
require 'config/auth.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

$query = "SELECT * FROM mahasiswa ORDER BY id DESC";
$hasil = mysqli_query($koneksi, $query);
?>

<div class="container">
    <div class="topbar">
        <span>Halo, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong></span>
        <a href="logout.php" class="btn btn-hapus">Logout</a>
    </div>
    <h1>Data Mahasiswa</h1>
    <table>
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama</th>
            <th>Jurusan</th>
            <th>Email</th>
            <th>Aksi</th>
        </tr>
        <?php $no = 1;
        while ($baris = mysqli_fetch_assoc($hasil)) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($baris['nim']) ?></td>
                <td><?= htmlspecialchars($baris['nama']) ?></td>
                <td><?= htmlspecialchars($baris['jurusan']) ?></td>
                <td><?= htmlspecialchars($baris['email']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $baris['id'] ?>">Edit</a>
                    <a href="delete.php?id=<?= $baris['id'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <form action="create.php" method="POST">
        <label for="nim">NIM</label>
        <input type="text" id="nim" name="nim" required>
        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" required>
        <label for="jurusan">Jurusan</label>
        <input type="text" id="jurusan" name="jurusan" required>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Simpan</button>
    </form>