<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('admin');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mid = (int) ($_POST['mahasiswa_id'] ?? 0);
    $mhs = ambil_baris($koneksi,
        "SELECT id, nim, nama FROM mahasiswa WHERE id = ?", "i", $mid);

    if (!$mhs) {
        $error = "Pilih mahasiswa yang valid.";
    } else {
        $pw   = buat_password_acak();
        $hash = password_hash($pw, PASSWORD_DEFAULT);

        // Username = NIM. Role selalu 'mahasiswa', bukan dari form.
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO users (username, nama_lengkap, password, role, mahasiswa_id)
             VALUES (?, ?, ?, 'mahasiswa', ?)");
        mysqli_stmt_bind_param($stmt, "sssi", $mhs['nim'], $mhs['nama'], $hash, $mhs['id']);

        try {
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash_akun'] = [
                    'pesan'    => "Akun untuk {$mhs['nama']} berhasil dibuat. Username: {$mhs['nim']}.",
                    'password' => $pw,
                ];
                header("Location: akun.php");
                exit;
            }
            $error = "Gagal membuat akun.";
        } catch (mysqli_sql_exception $e) {
            // 1062 = Duplicate entry
            $error = ($e->getCode() == 1062)
                ? "Mahasiswa ini sudah punya akun, atau username (NIM) sudah dipakai akun lain."
                : "Gagal membuat akun: " . $e->getMessage();
        }
    }
}

// Mahasiswa yang BELUM punya akun (anti-join)
$calon = ambil_semua($koneksi,
    "SELECT m.id, m.nim, m.nama
     FROM mahasiswa m
     LEFT JOIN users u ON u.mahasiswa_id = m.id
     WHERE u.id IS NULL
     ORDER BY m.nim");

$judul_halaman = 'Buat Akun Mahasiswa';
$menu_aktif    = 'akun';
require 'layout/header.php';
?>

<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h3 class="card-title"><span>🔑</span> Form Buat Akun Mahasiswa</h3>
    </div>
    <div class="card-body">
        <?php if ($error != "") : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (count($calon) == 0) : ?>
            <p>Semua mahasiswa sudah memiliki akun.</p>
            <a href="akun.php" class="btn btn-batal">Kembali</a>
        <?php else : ?>
            <form action="akun_tambah.php" method="POST">
                <div class="form-group">
                    <label for="mahasiswa_id">Mahasiswa (yang belum punya akun)</label>
                    <select id="mahasiswa_id" name="mahasiswa_id" required>
                        <option value="">-- Pilih mahasiswa --</option>
                        <?php foreach ($calon as $c) : ?>
                            <option value="<?= $c['id'] ?>">
                                <?= htmlspecialchars($c['nim'] . ' - ' . $c['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <p class="text-muted">
                    Username akun adalah NIM. Password awal dibuat acak dan ditampilkan sekali setelah akun dibuat.
                </p>

                <div class="form-footer">
                    <button type="submit" class="btn btn-simpan">Buat Akun</button>
                    <a href="akun.php" class="btn btn-batal">Batal</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require 'layout/footer.php'; ?>