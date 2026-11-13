<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('admin');

// Baca lalu hapus flash message (hanya tampil sekali)
$flash = $_SESSION['flash_akun'] ?? null;
unset($_SESSION['flash_akun']);

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $uid  = (int) ($_POST['user_id'] ?? 0);

    // Hanya akun ber-role mahasiswa yang boleh dikelola dari halaman ini.
    $target = ambil_baris($koneksi,
        "SELECT id, username, aktif FROM users WHERE id = ? AND role = 'mahasiswa'",
        "i", $uid);

    if (!$target) {
        $error = "Akun tidak ditemukan atau tidak boleh dikelola dari halaman ini.";

    } elseif ($aksi === 'reset') {
        $pw   = buat_password_acak();
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($koneksi, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hash, $uid);
        mysqli_stmt_execute($stmt);

        $_SESSION['flash_akun'] = [
            'pesan'    => "Password akun {$target['username']} berhasil direset.",
            'password' => $pw,
        ];
        header("Location: akun.php");
        exit;

    } elseif ($aksi === 'toggle') {
        $aktif_baru = $target['aktif'] ? 0 : 1;
        $stmt = mysqli_prepare($koneksi, "UPDATE users SET aktif = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $aktif_baru, $uid);
        mysqli_stmt_execute($stmt);

        $_SESSION['flash_akun'] = [
            'pesan'    => "Akun {$target['username']} " . ($aktif_baru ? "diaktifkan." : "dinonaktifkan."),
            'password' => null,
        ];
        header("Location: akun.php");
        exit;

    } elseif ($aksi === 'hapus') {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $uid);
        mysqli_stmt_execute($stmt);

        $_SESSION['flash_akun'] = [
            'pesan'    => "Akun {$target['username']} berhasil dihapus.",
            'password' => null,
        ];
        header("Location: akun.php");
        exit;
    }
}

// Daftar akun + data mahasiswa yang terhubung (LEFT JOIN: akun admin tetap tampil)
$daftar = ambil_semua($koneksi,
    "SELECT u.id, u.username, u.nama_lengkap, u.role, u.aktif, m.nim, m.nama AS nama_mhs
     FROM users u
     LEFT JOIN mahasiswa m ON m.id = u.mahasiswa_id
     ORDER BY u.role, u.username");

$judul_halaman = 'Kelola Akun';
$menu_aktif    = 'akun';
require 'layout/header.php';
?>

<?php if ($flash) : ?>
    <div class="alert alert-success">
        <div>
            <?= htmlspecialchars($flash['pesan']) ?>
            <?php if (!empty($flash['password'])) : ?>
                <br>Password baru:
                <span class="pw-baru"><?= htmlspecialchars($flash['password']) ?></span><br>
                <small>Catat dan berikan kepada mahasiswa. Password ini tidak akan ditampilkan lagi.</small>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($error != "") : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><span>🔑</span> Daftar Akun</h3>
        <a href="akun_tambah.php" class="btn btn-sm btn-primary">+ Buat Akun Mahasiswa</a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Username</th><th>Nama</th><th>Role</th>
                        <th>Mahasiswa</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftar as $u) : ?>
                        <tr>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($u['role']) ?>">
                                    <?= htmlspecialchars($u['role']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($u['nim'] !== null) : ?>
                                    <?= htmlspecialchars($u['nim'] . ' - ' . $u['nama_mhs']) ?>
                                <?php else : ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $u['aktif'] ? 'badge-aktif' : 'badge-nonaktif' ?>">
                                    <?= $u['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($u['role'] === 'mahasiswa') : ?>
                                    <form action="akun.php" method="POST" class="form-aksi"
                                          onsubmit="return confirm('Reset password akun ini?')">
                                        <input type="hidden" name="aksi" value="reset">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-edit">Reset Password</button>
                                    </form>

                                    <form action="akun.php" method="POST" class="form-aksi">
                                        <input type="hidden" name="aksi" value="toggle">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-batal">
                                            <?= $u['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                        </button>
                                    </form>

                                    <form action="akun.php" method="POST" class="form-aksi"
                                          onsubmit="return confirm('Hapus akun ini? Data mahasiswanya tidak ikut terhapus.')">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-hapus">Hapus</button>
                                    </form>
                                <?php else : ?>
                                    <span class="text-muted">Dikelola lewat database</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($daftar) == 0) : ?>
                        <tr><td colspan="6" class="text-center">Belum ada akun.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'layout/footer.php'; ?>