<?php
require 'config/koneksi.php';
require 'config/auth.php';
wajib_login(); // halaman ini hanya boleh diakses setelah login

$id = (int) $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $id_post = (int) $_POST['id'];
    $query = "UPDATE mahasiswa SET
 nim = '$nim', nama = '$nama',
 jurusan = '$jurusan', email = '$email'
 WHERE id = $id_post";
    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php?pesan=edit");
        exit;
    }
}
// Ambil data lama untuk ditampilkan di form
$query = "SELECT * FROM mahasiswa WHERE id = $id";
$hasil = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($hasil);
?>
<form action="edit.php?id=<?= $data['id'] ?>" method="POST">
    <input type="hidden" name="id" value="<?= $data['id'] ?>">

    <label>NIM</label>
    <input type="text" name="nim" value="<?= htmlspecialchars($data['nim']) ?>">

    <label>Nama</label>
    <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>">

    <!-- Tambahkan 2 baris ini -->
    <label>Jurusan</label>
    <input type="text" name="jurusan" value="<?= htmlspecialchars($data['jurusan']) ?>">

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>">

    <button type="submit">Update</button>
</form>