# Modul Praktikum 10 --- Web Programming

## Panduan Implementasi untuk AI Agent: RBAC Admin dan Mahasiswa

> **Sumber utama:** `MODUL PRAKTIKUM 10 — WEB PROGRAMMING.pdf` (53
> halaman), terutama Bab 1--11.\
> **Batas dokumen ini:** fokus pada progres aplikasi yang harus
> diubah/ditambahkan dan kode referensi dari modul. Bagian latihan/tugas
> mandiri pada halaman akhir tidak dimasukkan.

------------------------------------------------------------------------

## 1. Instruksi utama untuk AI agent

Dokumen ini adalah **spesifikasi perubahan + bank kode referensi**,
bukan instruksi untuk membangun ulang aplikasi.

### Aturan wajib

1.  **Periksa project yang sudah ada terlebih dahulu.** Identifikasi
    struktur folder, file, database, layout, CSS, komponen, dan pola
    coding yang sudah digunakan.
2.  **Jangan membuat ulang fitur yang sudah tersedia.** Modul 3--9 sudah
    membangun koneksi database, CRUD, login, dashboard, pencarian,
    pagination, upload file, JOIN, detail mahasiswa, dan rekap nilai.
3.  **Kode pada bagian "Kode referensi" adalah salinan kode dari
    modul.** Jika diminta menerapkan bagian tersebut, ambil dan gunakan
    kode referensinya (copy/adaptasi seperlunya hanya untuk path/nama
    variabel yang memang berbeda di project). Jangan merancang
    implementasi alternatif sendiri.
4.  **Jangan menulis ulang kode yang sudah ada hanya karena ingin
    merapikannya.** Lakukan perubahan sekecil mungkin sesuai daftar
    progres.
5.  **Pertahankan styling dan desain yang sudah berjalan.** Gunakan
    class CSS, komponen, spacing, warna, typography, struktur layout,
    navbar, sidebar, tabel, tombol, alert, dan responsive behavior yang
    sudah ada. Jangan membuat desain baru atau mengganti tema.
6.  Saat menambahkan halaman baru, **ikuti pola halaman existing**:
    `layout/header.php`, konten utama dengan class yang sudah digunakan,
    lalu `layout/footer.php`.
7.  Jangan mengganti stack, library, database driver, atau arsitektur
    project. Modul menggunakan PHP Native + MySQLi dan prepared
    statements.
8.  Jangan menghapus atau mengubah data database secara destruktif tanpa
    memeriksa kondisi database dan meminta persetujuan bila data
    berpotensi hilang.
9.  Jangan mengarang struktur tabel, nama kolom, fungsi helper, atau isi
    file. Cocokkan dengan project. Bila ada perbedaan, laporkan secara
    spesifik sebelum mengadaptasi.
10. Jangan mengerjakan bagian "Latihan dan Tugas Mandiri" atau
    menambahkan fitur di luar ruang lingkup Bab 1--11.
11. Setelah perubahan, jalankan pemeriksaan syntax/tes yang tersedia.
    Laporkan file yang diubah, kode yang digunakan, dan hasil tes.

### Cara bekerja

-   Tahap A --- Audit: baca struktur project dan file terkait, termasuk
    `config/koneksi.php`, `config/auth.php`, `config/master.php`,
    `login.php`, layout, `style.css`, halaman admin, dan skema database.
-   Tahap B --- Mapping: cocokkan kondisi project dengan daftar
    perubahan pada dokumen ini. Tandai yang sudah ada, belum ada, atau
    berbeda.
-   Tahap C --- Implementasi: kerjakan satu kelompok perubahan dalam
    satu waktu. Gunakan kode referensi modul sebagai sumber
    implementasi.
-   Tahap D --- Verifikasi: cek alur admin, alur mahasiswa, pembatasan
    akses, data milik sendiri, dan tampilan.
-   Jangan lanjut ke perubahan besar jika ada ketidakcocokan schema atau
    struktur yang belum jelas.

------------------------------------------------------------------------

## 2. Tujuan progres Modul 10

Menerapkan **RBAC (Role-Based Access Control)** agar aplikasi memiliki
dua jenis akun:

-   **Admin:** dapat mengelola seluruh data mahasiswa, nilai, rekap, dan
    akun.
-   **Mahasiswa:** hanya dapat melihat data akademik dan profil miliknya
    sendiri, serta mengubah password sendiri.

Login tetap satu pintu. Setelah login, pengguna diarahkan berdasarkan
role.

### Prinsip keamanan yang harus dipertahankan

-   **Deny by default:** role yang tidak diizinkan harus ditolak.
-   **Least privilege:** mahasiswa hanya memperoleh akses minimum untuk
    melihat data sendiri.
-   **Server-side enforcement:** setiap halaman PHP memeriksa role di
    server. Menyembunyikan menu saja bukan pengaman.
-   **Jangan percaya klien:** identitas mahasiswa diambil dari session,
    bukan URL atau hidden input.
-   **IDOR prevention:** halaman mahasiswa tidak boleh menggunakan
    `$_GET['id']` untuk menentukan pemilik data.
-   Belum login → redirect ke `login.php`.
-   Sudah login tetapi role tidak sesuai → HTTP 403 dan halaman
    `403.php`.
-   Data tidak ditemukan → pesan data tidak ditemukan / 404 sesuai
    konteks.

### Matriks akses

  Fitur                                      Belum login                        Mahasiswa                  Admin
  ---------------------------------------- ------------- -------------------------------- ----------------------
  Login                                               Ya             Dialihkan ke beranda   Dialihkan ke beranda
  Dashboard admin                                  Tidak                            Tidak                     Ya
  Data mahasiswa dan CRUD                          Tidak                            Tidak                     Ya
  Detail mahasiswa dan pengelolaan nilai           Tidak                            Tidak                     Ya
  Rekap nilai                                      Tidak                            Tidak                     Ya
  Kelola akun                                      Tidak                            Tidak                     Ya
  Dashboard mahasiswa                              Tidak                     Data sendiri                  Tidak
  Nilai Saya                                       Tidak                     Data sendiri                  Tidak
  Mata Kuliah                                      Tidak   Katalog + status nilai sendiri                  Tidak
  Profil Saya                                      Tidak                   Profil sendiri                  Tidak
  Ubah password sendiri                            Tidak                               Ya                     Ya
  Logout                                           Tidak                               Ya                     Ya

------------------------------------------------------------------------

## 3. Kondisi project yang diasumsikan modul

Modul mengasumsikan project PHP Native berada di
`C:\laragon\www\php_crud_native`, database `db_belajar`, dan sudah
menyelesaikan Modul 3--9.

Fitur existing yang **harus dipertahankan**: - Koneksi database. - CRUD
data mahasiswa. - Login existing. - Dashboard admin, layout
header/navbar/sidebar. - Pencarian dan pagination. - Upload foto dan
dokumen. - Tabel jurusan, dosen, mata_kuliah, dan nilai. - Detail
mahasiswa, input/hapus nilai, rekap nilai. - Helper dan CSS existing
yang masih digunakan.

Jangan berasumsi project pengguna persis sama. Cocokkan nama folder,
tabel, kolom, dan fungsi yang benar-benar ada.

------------------------------------------------------------------------

## 4. Daftar file dan progres perubahan

### File baru

  File                   Fungsi
  ---------------------- --------------------------------------------------
  `403.php`              Halaman akses ditolak
  `mhs_dashboard.php`    Dashboard mahasiswa
  `mhs_nilai.php`        Nilai mahasiswa yang login
  `mhs_matkul.php`       Katalog mata kuliah dan status nilai mahasiswa
  `mhs_profil.php`       Biodata mahasiswa yang login
  `ubah_password.php`    Ubah password untuk semua role
  `akun.php`             Daftar dan pengelolaan akun mahasiswa oleh admin
  `akun_tambah.php`      Membuat akun untuk mahasiswa
  `db_pertemuan10.sql`   Perubahan struktur database dan akun contoh

### File yang diubah

  -----------------------------------------------------------------------
  File                                Perubahan
  ----------------------------------- -----------------------------------
  `config/auth.php`                   Menambah helper role dan penjaga
                                      `wajib_role()`

  `login.php`                         Mengambil role/status/mahasiswa_id,
                                      validasi akun aktif, session role,
                                      redirect sesuai role

  `layout/header.php`                 Link Home/breadcrumb mengikuti role

  `layout/navbar.php`                 Dropdown menampilkan role, beranda,
                                      ubah password, logout

  `layout/sidebar.php`                Menu admin dan mahasiswa berbeda

  `dashboard.php`                     Proteksi `wajib_role('admin')`

  `index.php`                         Proteksi `wajib_role('admin')`

  `create.php`                        Proteksi `wajib_role('admin')`

  `edit.php`                          Proteksi `wajib_role('admin')`

  `delete.php`                        Proteksi `wajib_role('admin')`

  `detail.php`                        Proteksi `wajib_role('admin')`

  `rekap.php`                         Proteksi `wajib_role('admin')`

  `style.css`                         Tambahan badge role/status,
                                      password baru, halaman 403, form
                                      aksi
  -----------------------------------------------------------------------

### File yang dihapus / tidak berubah

-   `register.php`: dihapus; registrasi publik tidak lagi digunakan.
-   `logout.php`: tidak berubah.
-   `config/koneksi.php`, `config/upload.php`, `config/master.php`:
    tidak mengubah bagian existing kecuali penambahan helper yang
    dijelaskan di bawah.
-   Folder upload dan file fitur existing tidak dipindahkan.

### Struktur target

``` text
php_crud_native/
├── config/
│   ├── koneksi.php
│   ├── upload.php
│   ├── master.php       # ditambah helper query dan password acak
│   └── auth.php         # diubah
├── layout/
│   ├── header.php       # diubah
│   ├── navbar.php       # diubah
│   ├── sidebar.php      # diubah
│   └── footer.php
├── dashboard.php       # admin
├── index.php            # admin
├── create.php           # admin
├── edit.php             # admin
├── delete.php           # admin
├── detail.php           # admin
├── rekap.php            # admin
├── akun.php             # baru, admin
├── akun_tambah.php      # baru, admin
├── mhs_dashboard.php    # baru, mahasiswa
├── mhs_nilai.php        # baru, mahasiswa
├── mhs_matkul.php       # baru, mahasiswa
├── mhs_profil.php       # baru, mahasiswa
├── login.php            # diubah
├── logout.php           # tetap
├── ubah_password.php    # baru, semua role
├── 403.php              # baru
├── style.css            # ditambah
└── db_pertemuan10.sql   # baru
```

------------------------------------------------------------------------

## 5. Database

### Perubahan tabel `users`

Tambahkan tiga kolom:

  ----------------------------------------------------------------------------------------------------------
  Kolom                   Tipe                                                       Ketentuan
  ----------------------- ---------------------------------------------------------- -----------------------
  `role`                  `ENUM('admin','mahasiswa') NOT NULL DEFAULT 'mahasiswa'`   Role akun

  `mahasiswa_id`          `INT NULL`, UNIQUE, FK ke `mahasiswa(id)`                  NULL untuk admin; satu
                                                                                     mahasiswa maksimal satu
                                                                                     akun

  `aktif`                 `TINYINT(1) NOT NULL DEFAULT 1`                            1 aktif, 0 nonaktif
  ----------------------------------------------------------------------------------------------------------

Foreign key menggunakan `ON DELETE CASCADE ON UPDATE CASCADE`. Jika data
mahasiswa dihapus, akun yang terhubung ikut terhapus.

### SQL referensi --- db_pertemuan10.sql

> **Penting:** script ini mengubah schema dan menghapus akun mahasiswa
> lama yang tidak terhubung ke data mahasiswa. Backup database terlebih
> dahulu. Jalankan hanya setelah memeriksa kondisi data dan memastikan
> tindakan penghapusan memang diinginkan. Jangan jalankan ulang bagian
> ALTER TABLE.

#### Bagian 1 --- Tambah kolom dan relasi

``` sql
USE db_belajar;

ALTER TABLE users
    ADD COLUMN role ENUM('admin','mahasiswa') NOT NULL DEFAULT 'mahasiswa'
        AFTER nama_lengkap,
    ADD COLUMN mahasiswa_id INT NULL AFTER role,
    ADD COLUMN aktif TINYINT(1) NOT NULL DEFAULT 1 AFTER mahasiswa_id,
    ADD UNIQUE KEY uq_users_mahasiswa (mahasiswa_id),
    ADD CONSTRAINT fk_users_mahasiswa FOREIGN KEY (mahasiswa_id)
        REFERENCES mahasiswa (id) ON DELETE CASCADE ON UPDATE CASCADE;
```

#### Bagian 2 --- Tetapkan admin dan bersihkan akun registrasi lama

``` sql
-- Akun 'admin' dari Modul 5 menjadi admin
UPDATE users SET role = 'admin' WHERE username = 'admin';

-- Akun hasil registrasi bebas yang tidak terhubung ke data mahasiswa
DELETE FROM users
WHERE role = 'mahasiswa' AND mahasiswa_id IS NULL;
```

Jangan jalankan DELETE sebelum memastikan tidak ada akun yang perlu
dipertahankan. Akun yang ingin dipertahankan harus ditangani/ditautkan
secara manual terlebih dahulu.

#### Bagian 3 --- Akun mahasiswa contoh (khusus data contoh modul)

``` sql
-- Username = NIM. Password sementara praktikum: admin123
INSERT INTO users (username, nama_lengkap, password, role, mahasiswa_id)
SELECT m.nim, m.nama,
       '$2y$10$3i6nd6pcL35bp9t4xOq4j.E7GglJTj6ZUSMu6t8Y.V/kK7OOOwiLK',
       'mahasiswa', m.id
FROM mahasiswa m
WHERE m.nim IN ('2201001', '2201009');
```

Hash di atas merupakan hash password sementara `admin123` pada contoh
modul. Jangan mengubah atau membuat hash baru jika memang sedang
mereplikasi data contoh modul.

#### Query verifikasi

``` sql
SELECT u.username, u.role, u.aktif, m.nim, m.nama
FROM users u
LEFT JOIN mahasiswa m ON m.id = u.mahasiswa_id
ORDER BY u.role, u.username;
```

Hasil contoh yang diharapkan: - `admin` --- role admin, aktif 1,
mahasiswa_id NULL. - `2201001` --- role mahasiswa, aktif 1, terhubung ke
Andi Saputra. - `2201009` --- role mahasiswa, aktif 1, terhubung ke
Indra Wijaya.

`LEFT JOIN` diperlukan agar akun admin yang `mahasiswa_id`-nya NULL
tetap muncul.

------------------------------------------------------------------------

## 6. Helper otorisasi --- `config/auth.php`

Ganti isi `config/auth.php` dengan implementasi modul berikut. Fungsi
`sudah_login()` dan `wajib_login()` tetap tersedia.

``` php
<?php
/**
 * =====================================================
 * HELPER AUTENTIKASI DAN OTORISASI (SESSION + ROLE)
 * =====================================================
 * Pemakaian di awal halaman:
 *     require 'config/auth.php';
 *     wajib_role('admin');         // hanya admin
 *     wajib_role('mahasiswa');     // hanya mahasiswa
 *     wajib_login();               // semua yang sudah login
 * =====================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------- Membaca identitas dari session ----------

function sudah_login()
{
    return isset($_SESSION['user_id']);
}

function role_user(): string
{
    return $_SESSION['role'] ?? '';
}

function adalah_admin(): bool
{
    return role_user() === 'admin';
}

function adalah_mahasiswa(): bool
{
    return role_user() === 'mahasiswa';
}

// ID mahasiswa milik akun yang sedang login (0 jika bukan akun mahasiswa)
function id_mahasiswa_login(): int
{
    return (int) ($_SESSION['mahasiswa_id'] ?? 0);
}

// Halaman beranda sesuai role
function halaman_beranda(): string
{
    return adalah_admin() ? 'dashboard.php' : 'mhs_dashboard.php';
}

// ---------- Penjaga halaman ----------

// Belum login -> dilempar ke halaman login
function wajib_login()
{
    if (!sudah_login()) {
        header("Location: login.php?pesan=belum_login");
        exit;
    }
}

// Harus login DAN rolenya termasuk yang diizinkan, selain itu 403
function wajib_role(string ...$role_diizinkan): void
{
    wajib_login();

    if (!in_array(role_user(), $role_diizinkan, true)) {
        http_response_code(403);
        require __DIR__ . '/../403.php';
        exit;
    }
}
```

Ketentuan penting: - `wajib_role()` memanggil `wajib_login()` terlebih
dahulu. - Role dibandingkan ketat menggunakan `in_array(..., true)`. -
`http_response_code(403)` harus dipasang. - `exit` wajib setelah halaman
403 dipanggil agar kode halaman terlarang tidak dilanjutkan. - Role
kosong atau role yang tidak disebut harus ditolak.

------------------------------------------------------------------------

## 7. Halaman 403 --- `403.php`

``` php
<?php
// Halaman 'akses ditolak'. Dipanggil oleh wajib_role(), tetapi aman
// juga jika dibuka langsung lewat address bar.
require_once 'config/auth.php';
wajib_login();
http_response_code(403);

$judul_halaman = 'Akses Ditolak';
$menu_aktif    = '';
require 'layout/header.php';
?>

<div class="panel error-403">
    <div class="kode">403</div>
    <h2>Akses Ditolak</h2>
    <p>
        Akun Anda (<strong><?= htmlspecialchars(role_user()) ?></strong>)
        tidak memiliki hak untuk membuka halaman ini.
    </p>
    <a href="<?= halaman_beranda() ?>" class="btn btn-edit">Kembali ke Beranda</a>
</div>

<?php require 'layout/footer.php'; ?>
```

Gunakan `require_once` untuk `config/auth.php` karena file ini bisa
dipanggil dari `wajib_role()` ketika auth sudah dimuat.

------------------------------------------------------------------------

## 8. Login berbasis role --- `login.php`

Ganti bagian PHP di atas halaman login. Pertahankan form HTML dan
styling login existing. Hapus tautan registrasi publik.

``` php
<?php
require 'config/koneksi.php';
require 'config/auth.php';

// Sudah login? Arahkan ke beranda sesuai role
if (sudah_login()) {
    header("Location: " . halaman_beranda());
    exit;
}

$error = "";
$info = "";

if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'belum_login') {
        $info = "Silakan login terlebih dahulu.";
    } elseif ($_GET['pesan'] == 'logout') {
        $info = "Anda berhasil logout.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($koneksi,
        "SELECT id, username, nama_lengkap, password, role, mahasiswa_id, aktif
         FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$user || !password_verify($password, $user['password'])) {
        // Pesan sengaja umum
        $error = "Username atau password salah.";
    } elseif ((int) $user['aktif'] !== 1) {
        // Status akun baru diberitahukan setelah password terbukti benar
        $error = "Akun Anda dinonaktifkan. Hubungi admin.";
    } elseif ($user['role'] === 'mahasiswa' && empty($user['mahasiswa_id'])) {
        $error = "Akun Anda belum terhubung ke data mahasiswa. Hubungi admin.";
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id']      = $user['id'];
        $_SESSION['username']     = $user['username'];
        $_SESSION['nama']         = $user['nama_lengkap'];
        $_SESSION['role']         = $user['role'];
        $_SESSION['mahasiswa_id'] = $user['mahasiswa_id'];

        header("Location: " . halaman_beranda());
        exit;
    }
}
?>
```

Perilaku: - Admin → `dashboard.php`. - Mahasiswa →
`mhs_dashboard.php`. - Akun nonaktif ditolak setelah password benar. -
Akun mahasiswa tanpa relasi `mahasiswa_id` ditolak. - Jika
username/password salah, pesan tetap umum:
`Username atau password salah.` - `session_regenerate_id(true)`
dilakukan saat login berhasil.

### Hapus registrasi publik

1.  Hapus `register.php`.
2.  Hapus tautan "Belum punya akun? Daftar" dari form login.
3.  Jangan membuat pengganti registrasi publik. Akun mahasiswa dibuat
    oleh admin melalui Kelola Akun.

------------------------------------------------------------------------

## 9. Perubahan layout

### `layout/header.php`

Breadcrumb Home harus mengikuti role:

``` php
<a href="<?= halaman_beranda() ?>">Home</a> /
<?= htmlspecialchars($judul_halaman) ?>
```

### `layout/navbar.php`

Dropdown pengguna:

``` php
<div class="dropdown-menu" id="dropdown-user-menu">
    <div class="dropdown-info">
        Login sebagai <strong><?= htmlspecialchars($_SESSION['username']) ?></strong><br>
        <span class="badge badge-<?= htmlspecialchars(role_user()) ?>">
            <?= htmlspecialchars(role_user()) ?>
        </span>
    </div>
    <a href="<?= halaman_beranda() ?>">Beranda</a>
    <a href="ubah_password.php">Ubah Password</a>
    <a href="logout.php">Logout</a>
</div>
```

### `layout/sidebar.php`

Sidebar memiliki menu berbeda. Pertahankan struktur dan class existing,
termasuk `kelas_aktif()`.

``` php
<?php
function kelas_aktif($nama_menu, $menu_aktif)
{
    return $nama_menu === $menu_aktif ? 'aktif' : '';
}
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">🎓 SIMAHASISWA</div>

    <ul class="sidebar-menu">

        <?php if (adalah_admin()) : ?>
            <!-- ================= MENU ADMIN ================= -->
            <li class="sidebar-title">Menu Utama</li>
            <li>
                <a href="dashboard.php" class="<?= kelas_aktif('dashboard', $menu_aktif) ?>">
                    <span class="menu-ikon">🏠</span> Dashboard
                </a>
            </li>

            <li class="sidebar-title">Data</li>
            <li>
                <a href="index.php" class="<?= kelas_aktif('mahasiswa', $menu_aktif) ?>">
                    <span class="menu-ikon">📋</span> Data Mahasiswa
                </a>
            </li>
            <li>
                <a href="create.php" class="<?= kelas_aktif('tambah', $menu_aktif) ?>">
                    <span class="menu-ikon">➕</span> Tambah Mahasiswa
                </a>
            </li>
            <li>
                <a href="rekap.php" class="<?= kelas_aktif('rekap', $menu_aktif) ?>">
                    <span class="menu-ikon">📊</span> Rekap Nilai
                </a>
            </li>

            <li class="sidebar-title">Pengguna</li>
            <li>
                <a href="akun.php" class="<?= kelas_aktif('akun', $menu_aktif) ?>">
                    <span class="menu-ikon">🔑</span> Kelola Akun
                </a>
            </li>

        <?php else : ?>
            <!-- =============== MENU MAHASISWA =============== -->
            <li class="sidebar-title">Menu Utama</li>
            <li>
                <a href="mhs_dashboard.php" class="<?= kelas_aktif('dashboard', $menu_aktif) ?>">
                    <span class="menu-ikon">🏠</span> Dashboard
                </a>
            </li>

            <li class="sidebar-title">Akademik</li>
            <li>
                <a href="mhs_nilai.php" class="<?= kelas_aktif('nilai', $menu_aktif) ?>">
                    <span class="menu-ikon">📝</span> Nilai Saya
                </a>
            </li>
            <li>
                <a href="mhs_matkul.php" class="<?= kelas_aktif('matkul', $menu_aktif) ?>">
                    <span class="menu-ikon">📚</span> Mata Kuliah
                </a>
            </li>

            <li class="sidebar-title">Profil</li>
            <li>
                <a href="mhs_profil.php" class="<?= kelas_aktif('profil', $menu_aktif) ?>">
                    <span class="menu-ikon">👤</span> Profil Saya
                </a>
            </li>
        <?php endif; ?>

        <!-- ============ MENU UNTUK SEMUA ROLE ============ -->
        <li class="sidebar-title">Akun</li>
        <li>
            <a href="ubah_password.php" class="<?= kelas_aktif('password', $menu_aktif) ?>">
                <span class="menu-ikon">🔒</span> Ubah Password
            </a>
        </li>
        <li>
            <a href="logout.php">
                <span class="menu-ikon">🚪</span> Logout
            </a>
        </li>
    </ul>
</aside>
```

> Catatan: cuplikan sidebar pada PDF memiliki pemenggalan baris/urutan
> elemen HTML akibat tata letak halaman. Kode di atas mempertahankan isi
> dan tujuan menu modul dalam markup yang utuh. Tetap sesuaikan
> wrapper/class dengan sidebar project existing; jangan mengganti
> desain.

### Tambahan `style.css`

Tambahkan CSS berikut di bagian akhir stylesheet. Jangan menghapus CSS
existing.

``` css
/* ===== MODUL 10: RBAC ===== */
.badge-admin     { background: #8e44ad; }
.badge-mahasiswa { background: #2980b9; }
.badge-nonaktif  { background: #7f8c8d; }
.badge-aktif     { background: #27ae60; }

/* Kotak password baru yang ditampilkan sekali kepada admin */
.pw-baru {
    display: inline-block;
    font-family: Consolas, monospace;
    font-size: 18px;
    letter-spacing: 1px;
    padding: 4px 10px;
    background: #fff8e1;
    border: 1px dashed #e0a800;
    border-radius: 4px;
}

/* Halaman 403 */
.error-403 {
    text-align: center;
    padding: 40px 20px;
}
.error-403 .kode {
    font-size: 64px;
    font-weight: bold;
    color: #c0392b;
    line-height: 1;
}

/* Form kecil di dalam sel tabel */
.form-aksi { display: inline; }
```

------------------------------------------------------------------------

## 10. Proteksi halaman admin lama

Pada tujuh file berikut, ganti pemanggilan penjaga akses di bagian
paling atas:

``` php
// SEBELUM
wajib_login();

// SESUDAH
wajib_role('admin');
```

File: - `dashboard.php` - `index.php` - `create.php` - `edit.php` -
`delete.php` - `detail.php` - `rekap.php`

**Penting:** `delete.php` juga wajib diproteksi walaupun tidak
menampilkan halaman. Jangan hanya melindungi menu/tombol. Setiap file
yang bisa dipanggil melalui URL atau menerima POST harus melakukan
pemeriksaan akses di server sebelum memproses query atau aksi.

Pencarian global `wajib_login()` pada project seharusnya hanya
menyisakan penggunaan wajar: - `config/auth.php` (definisi dan
pemanggilan di dalam `wajib_role()`). - `403.php` (halaman ini
membutuhkan status login). - `ubah_password.php` (semua role boleh).

------------------------------------------------------------------------

## 11. Helper query --- `config/master.php`

Tambahkan fungsi berikut di bagian bawah `config/master.php`. Jangan
menghapus fungsi existing.

``` php
// Menjalankan query prepared statement, mengembalikan SATU baris (atau null)
// ambil_baris($koneksi, "SELECT ... WHERE id = ?", "i", $id);
function ambil_baris(mysqli $koneksi, string $sql, string $tipe = '', ...$param): ?array
{
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($tipe !== '') {
        mysqli_stmt_bind_param($stmt, $tipe, ...$param);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

// Sama, tetapi mengembalikan SEMUA baris sebagai array
function ambil_semua(mysqli $koneksi, string $sql, string $tipe = '', ...$param): array
{
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($tipe !== '') {
        mysqli_stmt_bind_param($stmt, $tipe, ...$param);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}
```

Fungsi menggunakan prepared statement. `$tipe` mengikuti format
`bind_param` (`i` untuk integer, `s` untuk string).

------------------------------------------------------------------------

## 12. Halaman mahasiswa

### Aturan umum seluruh halaman mahasiswa

Awali masing-masing halaman dengan pola:

``` php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('mahasiswa');
$mid = id_mahasiswa_login();
```

-   Tidak boleh membaca `$_GET['id']` untuk menentukan mahasiswa.
-   Semua query data mahasiswa harus menggunakan `$mid` dari session.
-   Mahasiswa hanya membaca data; tidak ada tombol edit/hapus nilai.
-   Gunakan layout dan class existing.

### 12.1 `mhs_dashboard.php`

``` php
<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('mahasiswa');
$mid = id_mahasiswa_login();

// 1. Biodata + jurusan (INNER JOIN)
$bio = ambil_baris($koneksi,
    "SELECT m.nim, m.nama, j.nama_jurusan
     FROM mahasiswa m
     INNER JOIN jurusan j ON j.id = m.jurusan_id
     WHERE m.id = ?", "i", $mid);
if (!$bio) { echo "Data mahasiswa tidak ditemukan."; exit; }

// 2. Ringkasan akademik
$ringkas = ambil_baris($koneksi,
    "SELECT COUNT(n.id) AS jml_mk,
            COALESCE(SUM(mk.sks), 0) AS total_sks,
            ROUND(SUM(n.nilai_angka * mk.sks) / SUM(mk.sks), 2) AS rata_berbobot
     FROM nilai n
     INNER JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
     WHERE n.mahasiswa_id = ?", "i", $mid);

// 3. Tiga nilai terbaru
$terbaru = ambil_semua($koneksi,
    "SELECT mk.nama_mk, n.nilai_angka
     FROM nilai n
     INNER JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
     WHERE n.mahasiswa_id = ?
     ORDER BY n.id DESC
     LIMIT 3", "i", $mid);

$judul_halaman = 'Dashboard Mahasiswa';
$menu_aktif    = 'dashboard';
require 'layout/header.php';
?>

<p>
    Selamat datang, <strong><?= htmlspecialchars($bio['nama']) ?></strong>
    (<?= htmlspecialchars($bio['nim']) ?> &middot;
    <?= htmlspecialchars($bio['nama_jurusan']) ?>).
</p>

<div class="cards">
    <div class="stat-card">
        <div class="stat-ikon">📚</div>
        <div>
            <div class="stat-angka"><?= $ringkas['jml_mk'] ?></div>
            <div class="stat-label">Mata Kuliah Bernilai</div>
        </div>
    </div>
    <div class="stat-card hijau">
        <div class="stat-ikon">🎯</div>
        <div>
            <div class="stat-angka"><?= $ringkas['total_sks'] ?></div>
            <div class="stat-label">Total SKS</div>
        </div>
    </div>
    <div class="stat-card oranye">
        <div class="stat-ikon">⭐</div>
        <div>
            <div class="stat-angka">
                <?= $ringkas['rata_berbobot'] === null ? '-' : $ringkas['rata_berbobot'] ?>
            </div>
            <div class="stat-label">Rata-rata Berbobot</div>
        </div>
    </div>
</div>

<div class="panel">
    <h2>Nilai Terbaru</h2>
    <?php if (count($terbaru) == 0) : ?>
        <p>Belum ada nilai.</p>
    <?php else : ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Mata Kuliah</th><th>Nilai</th><th>Mutu</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($terbaru as $t) : $huruf = huruf_mutu($t['nilai_angka']); ?>
                        <tr>
                            <td><?= htmlspecialchars($t['nama_mk']) ?></td>
                            <td class="angka"><?= number_format($t['nilai_angka'], 2) ?></td>
                            <td><span class="badge badge-<?= $huruf ?>"><?= $huruf ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="ringkas"><a href="mhs_nilai.php">Lihat semua nilai &raquo;</a></p>
    <?php endif; ?>
</div>

<?php require 'layout/footer.php'; ?>
```

### 12.2 `mhs_nilai.php`

``` php
<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('mahasiswa');
$mid = id_mahasiswa_login();

$daftar_nilai = ambil_semua($koneksi,
    "SELECT mk.kode_mk, mk.nama_mk, mk.sks, d.nama AS dosen, n.nilai_angka
     FROM nilai n
     INNER JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
     LEFT JOIN dosen d ON d.id = mk.dosen_id
     WHERE n.mahasiswa_id = ?
     ORDER BY mk.kode_mk", "i", $mid);

$total_sks = 0;
$total_bobot = 0;
foreach ($daftar_nilai as $r) {
    $total_sks   += $r['sks'];
    $total_bobot += $r['nilai_angka'] * $r['sks'];
}
$rata_berbobot = $total_sks > 0 ? $total_bobot / $total_sks : null;

$judul_halaman = 'Nilai Saya';
$menu_aktif    = 'nilai';
require 'layout/header.php';
?>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th><th>Kode</th><th>Mata Kuliah</th><th>SKS</th>
                    <th>Dosen</th><th>Nilai</th><th>Mutu</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($daftar_nilai as $r) : $huruf = huruf_mutu($r['nilai_angka']); ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($r['kode_mk']) ?></td>
                        <td><?= htmlspecialchars($r['nama_mk']) ?></td>
                        <td><?= $r['sks'] ?></td>
                        <td>
                            <?php if ($r['dosen'] !== null) : ?>
                                <?= htmlspecialchars($r['dosen']) ?>
                            <?php else : ?>
                                <span class="teks-kosong">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="angka"><?= number_format($r['nilai_angka'], 2) ?></td>
                        <td><span class="badge badge-<?= $huruf ?>"><?= $huruf ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($daftar_nilai) == 0) : ?>
                    <tr><td colspan="7" style="text-align:center;">Belum ada nilai.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($rata_berbobot !== null) : ?>
        <p class="ringkas">
            Total SKS: <strong><?= $total_sks ?></strong> &middot;
            Rata-rata berbobot: <strong><?= number_format($rata_berbobot, 2) ?></strong>
            (<?= huruf_mutu($rata_berbobot) ?>)
        </p>
    <?php endif; ?>
</div>

<?php require 'layout/footer.php'; ?>
```

### 12.3 `mhs_matkul.php`

``` php
<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('mahasiswa');
$mid = id_mahasiswa_login();

$katalog = ambil_semua($koneksi,
    "SELECT mk.kode_mk, mk.nama_mk, mk.sks, d.nama AS dosen, n.nilai_angka
     FROM mata_kuliah mk
     LEFT JOIN dosen d ON d.id = mk.dosen_id
     LEFT JOIN nilai n ON n.mata_kuliah_id = mk.id AND n.mahasiswa_id = ?
     ORDER BY mk.kode_mk", "i", $mid);

$judul_halaman = 'Mata Kuliah';
$menu_aktif    = 'matkul';
require 'layout/header.php';
?>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Dosen</th><th>Status Saya</th></tr>
            </thead>
            <tbody>
                <?php foreach ($katalog as $k) : ?>
                    <tr>
                        <td><?= htmlspecialchars($k['kode_mk']) ?></td>
                        <td><?= htmlspecialchars($k['nama_mk']) ?></td>
                        <td><?= $k['sks'] ?></td>
                        <td>
                            <?php if ($k['dosen'] !== null) : ?>
                                <?= htmlspecialchars($k['dosen']) ?>
                            <?php else : ?>
                                <span class="teks-kosong">Belum ada dosen</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($k['nilai_angka'] !== null) :
                                $huruf = huruf_mutu($k['nilai_angka']); ?>
                                <span class="badge badge-<?= $huruf ?>"><?= $huruf ?></span>
                                <?= number_format($k['nilai_angka'], 2) ?>
                            <?php else : ?>
                                <span class="teks-kosong">Belum bernilai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'layout/footer.php'; ?>
```

**Penting:** kondisi `n.mahasiswa_id = ?` harus berada pada klausa `ON`
dari `LEFT JOIN`, bukan `WHERE`, agar mata kuliah yang belum memiliki
nilai tetap tampil.

### 12.4 `mhs_profil.php`

``` php
<?php
require 'config/koneksi.php';
require 'config/auth.php';
require 'config/master.php';
wajib_role('mahasiswa');
$mid = id_mahasiswa_login();

$p = ambil_baris($koneksi,
    "SELECT m.nim, m.nama, m.email, m.foto, m.dokumen, j.nama_jurusan
     FROM mahasiswa m
     INNER JOIN jurusan j ON j.id = m.jurusan_id
     WHERE m.id = ?", "i", $mid);
if (!$p) { echo "Data mahasiswa tidak ditemukan."; exit; }

$judul_halaman = 'Profil Saya';
$menu_aktif    = 'profil';
require 'layout/header.php';
?>

<div class="panel">
    <?php if (!empty($p['foto']) && file_exists('uploads/' . $p['foto'])) : ?>
        <img src="uploads/<?= htmlspecialchars($p['foto']) ?>"
             alt="Foto <?= htmlspecialchars($p['nama']) ?>" class="foto-detail">
    <?php endif; ?>

    <dl class="info-grid">
        <dt>NIM</dt>     <dd><?= htmlspecialchars($p['nim']) ?></dd>
        <dt>Nama</dt>    <dd><?= htmlspecialchars($p['nama']) ?></dd>
        <dt>Jurusan</dt> <dd><?= htmlspecialchars($p['nama_jurusan']) ?></dd>
        <dt>Email</dt>   <dd><?= htmlspecialchars($p['email']) ?></dd>
        <dt>Dokumen</dt>
        <dd>
            <?php if (!empty($p['dokumen']) && file_exists('uploads/dokumen/' . $p['dokumen'])) : ?>
                <a href="uploads/dokumen/<?= htmlspecialchars($p['dokumen']) ?>"
                   class="dok-link" download>
                    <?= strtoupper(pathinfo($p['dokumen'], PATHINFO_EXTENSION)) ?>
                </a>
            <?php else : ?>
                <span class="teks-kosong">Belum ada dokumen</span>
            <?php endif; ?>
        </dd>
    </dl>

    <p class="ringkas">Untuk mengubah data pribadi, hubungi admin.</p>
</div>

<?php require 'layout/footer.php'; ?>
```

Batasan yang dinyatakan modul: file statis di `uploads/` masih dapat
diakses siapa pun yang mengetahui alamat file. Proteksi RBAC pada
halaman PHP tidak otomatis melindungi file statis. Jangan mengklaim file
upload sudah privat.

------------------------------------------------------------------------

## 13. Ubah password --- `ubah_password.php`

Halaman ini digunakan semua role, sehingga memakai `wajib_login()`. ID
akun yang diubah wajib berasal dari session, bukan dari form atau URL.

``` php
<?php
require 'config/koneksi.php';
require 'config/auth.php';
wajib_login(); // semua role boleh membuka

$uid    = (int) $_SESSION['user_id']; // akun sendiri, dari session
$error  = "";
$sukses = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lama    = $_POST['password_lama'] ?? '';
    $baru    = $_POST['password_baru'] ?? '';
    $konfirm = $_POST['konfirmasi'] ?? '';

    $stmt = mysqli_prepare($koneksi, "SELECT password FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $uid);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$user || !password_verify($lama, $user['password'])) {
        $error = "Password lama salah.";
    } elseif (strlen($baru) < 6) {
        $error = "Password baru minimal 6 karakter.";
    } elseif ($baru !== $konfirm) {
        $error = "Konfirmasi password baru tidak sama.";
    } elseif ($baru === $lama) {
        $error = "Password baru tidak boleh sama dengan password lama.";
    } else {
        $hash = password_hash($baru, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($koneksi, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hash, $uid);
        mysqli_stmt_execute($stmt);
        session_regenerate_id(true);
        $sukses = "Password berhasil diubah.";
    }
}

$judul_halaman = 'Ubah Password';
$menu_aktif    = 'password';
require 'layout/header.php';
?>

<div class="panel panel-form">
    <?php if ($sukses != "") : ?>
        <div class="alert"><?= htmlspecialchars($sukses) ?></div>
    <?php endif; ?>
    <?php if ($error != "") : ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="ubah_password.php" method="POST">
        <label for="password_lama">Password Lama</label>
        <input type="password" id="password_lama" name="password_lama" required>

        <label for="password_baru">Password Baru (minimal 6 karakter)</label>
        <input type="password" id="password_baru" name="password_baru" required>

        <label for="konfirmasi">Konfirmasi Password Baru</label>
        <input type="password" id="konfirmasi" name="konfirmasi" required>

        <div style="margin-top:20px;">
            <button type="submit" class="btn btn-simpan">Simpan Password</button>
        </div>
    </form>
</div>

<?php require 'layout/footer.php'; ?>
```

Validasi: - Password lama harus benar. - Password baru minimal 6
karakter. - Konfirmasi harus sama. - Password baru tidak boleh sama
dengan password lama. - Simpan menggunakan `password_hash()`. - Perbarui
session ID dengan `session_regenerate_id(true)`.

------------------------------------------------------------------------

## 14. Kelola akun admin

### 14.1 Helper password acak di `config/master.php`

Tambahkan fungsi ini tanpa menghapus helper existing:

``` php
// Password acak 8 karakter (huruf dan angka heksadesimal) yang aman secara kriptografis
function buat_password_acak(int $panjang = 8): string
{
    return substr(bin2hex(random_bytes($panjang)), 0, $panjang);
}
```

Gunakan `random_bytes()`, bukan `rand()` atau `mt_rand()`.

### 14.2 `akun.php` --- logic

``` php
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
```

### 14.3 `akun.php` --- tampilan

``` php
<?php if ($flash) : ?>
    <div class="alert">
        <?= htmlspecialchars($flash['pesan']) ?>
        <?php if (!empty($flash['password'])) : ?>
            <br>Password baru:
            <span class="pw-baru"><?= htmlspecialchars($flash['password']) ?></span><br>
            <small>Catat dan berikan kepada mahasiswa. Password ini tidak akan ditampilkan lagi.</small>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($error != "") : ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="toolbar">
        <a href="akun_tambah.php" class="btn btn-tambah">+ Buat Akun Mahasiswa</a>
    </div>

    <div class="table-wrap">
        <table>
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
                                <span class="teks-kosong">-</span>
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
                                    <button type="submit" class="btn btn-edit">Reset Password</button>
                                </form>

                                <form action="akun.php" method="POST" class="form-aksi">
                                    <input type="hidden" name="aksi" value="toggle">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-batal">
                                        <?= $u['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>

                                <form action="akun.php" method="POST" class="form-aksi"
                                      onsubmit="return confirm('Hapus akun ini? Data mahasiswanya tidak ikut terhapus.')">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-hapus">Hapus</button>
                                </form>
                            <?php else : ?>
                                <span class="teks-kosong">Dikelola lewat database</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'layout/footer.php'; ?>
```

Keamanan: - Query target selalu memakai `AND role = 'mahasiswa'`. Jangan
hanya mengandalkan tombol yang disembunyikan. - Semua aksi perubahan
memakai POST. - Redirect setelah POST menerapkan pola
POST-Redirect-GET. - `LEFT JOIN` membuat akun admin tetap tampil. -
Menghapus akun mahasiswa tidak menghapus data mahasiswa. - Menghapus
data mahasiswa dari halaman admin dapat menghapus akun terkait karena FK
`ON DELETE CASCADE`.

### 14.4 `akun_tambah.php`

``` php
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

<div class="panel panel-form">
    <?php if ($error != "") : ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (count($calon) == 0) : ?>
        <p>Semua mahasiswa sudah memiliki akun.</p>
        <a href="akun.php" class="btn btn-batal">Kembali</a>
    <?php else : ?>
        <form action="akun_tambah.php" method="POST">
            <label for="mahasiswa_id">Mahasiswa (yang belum punya akun)</label>
            <select id="mahasiswa_id" name="mahasiswa_id" required>
                <option value="">-- Pilih mahasiswa --</option>
                <?php foreach ($calon as $c) : ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['nim'] . ' - ' . $c['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <p class="ringkas">
                Username akun adalah NIM. Password awal dibuat acak dan ditampilkan sekali setelah akun dibuat.
            </p>

            <div style="margin-top:20px;">
                <button type="submit" class="btn btn-simpan">Buat Akun</button>
                <a href="akun.php" class="btn btn-batal">Batal</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require 'layout/footer.php'; ?>
```

Poin penting: - Admin memilih mahasiswa yang belum memiliki akun. -
Username otomatis NIM. - Password awal acak dan hanya ditampilkan satu
kali. - Role ditulis tetap `'mahasiswa'` di query; jangan menerima role
dari form. - NIM/nama diambil ulang dari database berdasarkan
`mahasiswa_id`, bukan dipercaya dari input. - Anti-join menampilkan
mahasiswa tanpa akun. - UNIQUE `mahasiswa_id` menjadi perlindungan
terakhir dari akun ganda. - Error duplicate key 1062 ditangani dengan
pesan yang ramah.

------------------------------------------------------------------------

## 15. Checklist pengujian dan hasil yang diharapkan

Gunakan sebagai checklist verifikasi implementasi (bukan bagian
tugas/latihan tambahan).

  -------------------------------------------------------------------------------------
                           No. Skenario               Hasil yang diharapkan
  ---------------------------- ---------------------- ---------------------------------
                             1 Login admin            Masuk `dashboard.php`; sidebar
                                                      admin dan Kelola Akun tampil

                             2 Login mahasiswa        Masuk `mhs_dashboard.php`;
                                                      sidebar khusus mahasiswa

                             3 Password salah         `Username atau password salah.`

                             4 Akun nonaktif +        Pesan akun dinonaktifkan
                               password benar         

                             5 Akun nonaktif +        Tetap pesan username/password
                               password salah         salah

                             6 Belum login membuka    Redirect ke login dengan pesan
                               `index.php`            belum login

                             7 Mahasiswa membuka      HTTP 403
                               halaman admin          

                             8 Mahasiswa membuka      HTTP 403; query hapus tidak
                               `delete.php`           dijalankan

                             9 Mahasiswa membuka      HTTP 403
                               `akun.php` /           
                               `akun_tambah.php`      

                            10 Admin membuka halaman  HTTP 403
                               mahasiswa              

                            11 Mahasiswa membuka      Tetap hanya nilai miliknya;
                               `mhs_nilai.php?id=2`   parameter ID tidak dipakai

                            12 Sudah login membuka    Redirect ke beranda sesuai role
                               `login.php`            

                            13 Network tab saat akses HTTP status 403, bukan 200
                               terlarang              

                            14 Ubah password dengan   Pesan validasi sesuai kasus
                               input salah            

                            15 Buat akun mahasiswa    Password acak tampil sekali,
                                                      hilang setelah refresh

                            16 Kirim input            Akun tetap dibuat sebagai
                               tersembunyi            mahasiswa
                               `role=admin` pada form 
                               buat akun              

                            17 Ubah `user_id` aksi    Ditolak oleh query target
                               Kelola Akun ke ID      
                               admin                  

                            18 Hapus mahasiswa        Akun terkait ikut terhapus
                                                      melalui ON DELETE CASCADE

                            19 Mahasiswa tanpa nilai  Empty state tampil tanpa error
                               membuka halaman        
  -------------------------------------------------------------------------------------

------------------------------------------------------------------------

## 16. Troubleshooting yang tercantum di modul

  --------------------------------------------------------------------------------------------------------------------
  Masalah                                                     Penyebab yang mungkin   Penanganan
  ----------------------------------------------------------- ----------------------- --------------------------------
  `Unknown column 'role' in 'field list'`                     Bagian 1 SQL belum      Jalankan perubahan schema yang
                                                              dijalankan              sesuai

  `Duplicate column name 'role'`                              ALTER TABLE dijalankan  Jangan ulangi ALTER TABLE
                                                              dua kali                

  Foreign key gagal dibuat                                    Engine bukan InnoDB     Pastikan `users` dan `mahasiswa`
                                                              atau tipe kolom tidak   InnoDB; tipe ID cocok
                                                              sama                    

  Admin mendapat 403 setelah login                            Session lama belum      Logout lalu login ulang
                                                              memiliki role           

  Mahasiswa diarahkan ke dashboard admin                      Redirect login masih    Gunakan `halaman_beranda()`
                                                              versi lama              

  `Call to undefined function wajib_role()`                   `auth.php` belum        Periksa file dan require
                                                              diperbarui / path salah 

  `Call to undefined function ambil_baris()` /                Helper belum            Tambahkan helper dan require
  `buat_password_acak()`                                      ditambahkan atau        file
                                                              master.php belum        
                                                              di-require              

  `Cannot redeclare ...`                                      File helper dimuat dua  Gunakan `require_once` pada 403
                                                              kali                    untuk auth

  `Cannot modify header information - headers already sent`   Ada output sebelum      Pastikan penjaga di awal file,
                                                              header/redirect         tanpa output sebelum PHP

  Redirect loop                                               Penjaga dipasang di     Jangan pasang
                                                              `login.php`             `wajib_login()`/`wajib_role()`
                                                                                      di login

  `Undefined array key "role"`                                Session lama / belum    Logout-login ulang dan cek
                                                              login                   posisi penjaga

  Akun mahasiswa belum terhubung                              Akun dibuat manual      Hapus/tautkan dengan benar dan
                                                              tanpa `mahasiswa_id`    buat akun melalui Kelola Akun

  Duplicate username saat buat akun                           NIM sudah digunakan     Periksa akun yang bentrok
  --------------------------------------------------------------------------------------------------------------------

------------------------------------------------------------------------

## 17. Catatan batasan dan hal yang jangan diasumsikan

1.  Role disimpan di session. Jika role/status akun diubah saat pengguna
    sedang login, perubahan baru efektif setelah login ulang. Ini
    merupakan batasan yang disebut modul.
2.  File foto/dokumen dalam folder `uploads/` masih dapat diakses
    langsung melalui URL jika server menyajikannya sebagai file statis.
    RBAC halaman tidak otomatis mengamankan file tersebut.
3.  Admin tidak dibuat melalui form Kelola Akun. Akun admin dikelola
    melalui database.
4.  Maksimal satu akun untuk setiap mahasiswa karena `mahasiswa_id`
    UNIQUE.
5.  Menghapus akun melalui `akun.php` tidak menghapus data mahasiswa.
    Menghapus mahasiswa dapat menghapus akun terkait melalui FK cascade.
6.  Password database harus berupa hash, bukan plaintext.
7.  Jangan mengubah desain existing demi meniru tampilan baru. Tujuan
    modul adalah menambah role dan halaman khusus dengan tetap
    memanfaatkan layout aplikasi sebelumnya.
8.  Modul tidak meminta fitur edit profil mahasiswa. Profil mahasiswa
    hanya ditampilkan; perubahan data pribadi melalui admin.

------------------------------------------------------------------------

## 18. Output yang harus dilaporkan AI agent

Setelah implementasi, laporkan:

-   File yang sudah diaudit.
-   File yang dibuat, diubah, atau dihapus.
-   Bagian yang menggunakan kode referensi modul.
-   Perbedaan antara struktur project aktual dan asumsi modul.
-   Perubahan database yang dilakukan atau yang masih perlu dijalankan
    manual.
-   Hasil pemeriksaan syntax dan pengujian role.
-   Masalah yang belum terselesaikan.

**Jangan menyatakan selesai** jika proteksi server-side, kepemilikan
data mahasiswa dari session, atau pengujian akses langsung URL belum
diverifikasi.
