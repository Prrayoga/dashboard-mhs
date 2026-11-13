-- =====================================================
-- MODUL 10 - RBAC ADMIN & MAHASISWA
-- File migrasi untuk database utama project (db_belajar).
-- Jalankan SEKALI melalui phpMyAdmin setelah backup database.
-- File ini TIDAK membuat database baru dan TIDAK menghapus data lama.
-- =====================================================

USE db_belajar;

-- -----------------------------------------------------
-- Pemeriksaan awal (opsional, aman dijalankan)
-- Pastikan kolom role / mahasiswa_id / aktif BELUM ada.
-- Jika sudah ada, lewati Bagian 1 agar tidak "Duplicate column".
-- -----------------------------------------------------
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'db_belajar'
  AND TABLE_NAME = 'users'
ORDER BY ORDINAL_POSITION;

-- -----------------------------------------------------
-- Bagian 1 - Tambah kolom role, mahasiswa_id, aktif
--          + UNIQUE + FOREIGN KEY ke tabel mahasiswa.
-- Catatan:
--   * mahasiswa_id NULL untuk akun admin.
--   * Satu mahasiswa maksimal memiliki satu akun (UNIQUE).
--   * ON DELETE CASCADE: hapus data mahasiswa ikut hapus akunnya.
--   * users dan mahasiswa sudah memakai engine InnoDB,
--     sehingga foreign key dapat dibuat.
-- -----------------------------------------------------
ALTER TABLE users
    ADD COLUMN role ENUM('admin','mahasiswa') NOT NULL DEFAULT 'mahasiswa'
        AFTER nama_lengkap,
    ADD COLUMN mahasiswa_id INT NULL AFTER role,
    ADD COLUMN aktif TINYINT(1) NOT NULL DEFAULT 1 AFTER mahasiswa_id,
    ADD UNIQUE KEY uq_users_mahasiswa (mahasiswa_id),
    ADD CONSTRAINT fk_users_mahasiswa FOREIGN KEY (mahasiswa_id)
        REFERENCES mahasiswa (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- -----------------------------------------------------
-- Bagian 2 - Tetapkan akun admin yang sudah ada.
-- Akun dengan username 'admin' (dari modul login sebelumnya)
-- dijadikan role admin. Jika username admin project berbeda,
-- sesuaikan nilai 'admin' pada WHERE di bawah ini.
-- -----------------------------------------------------
UPDATE users SET role = 'admin' WHERE username = 'admin';

-- Akun admin tidak boleh terhubung ke data mahasiswa.
UPDATE users SET mahasiswa_id = NULL WHERE role = 'admin';

-- -----------------------------------------------------
-- Bagian 3 - Penanganan akun lama hasil registrasi publik.
-- Modul contoh menghapus akun mahasiswa yang belum terhubung
-- ke data mahasiswa. Di project ini data TIDAK dihapus agar
-- tidak ada kehilangan data. Akun tersebut otomatis tidak bisa
-- login (lihat validasi di login.php) sampai ditautkan ke data
-- mahasiswa atau dihapus manual oleh admin lewat menu Kelola Akun.
--
-- Jika Anda memang ingin membersihkannya, periksa dulu hasil
-- query berikut, lalu jalankan DELETE secara sadar (tidak otomatis):
--
-- SELECT id, username, nama_lengkap, created_at
-- FROM users
-- WHERE role = 'mahasiswa' AND mahasiswa_id IS NULL;
--
-- DELETE FROM users
-- WHERE role = 'mahasiswa' AND mahasiswa_id IS NULL;
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Bagian 4 - (OPSIONAL) Akun mahasiswa contoh.
-- Diblok komentar secara default. Buka komentar hanya jika
-- ingin membuat akun contoh untuk NIM yang benar-benar ada.
-- Username = NIM, password sementara = admin123 (hash modul).
-- -----------------------------------------------------
-- INSERT INTO users (username, nama_lengkap, password, role, mahasiswa_id)
-- SELECT m.nim, m.nama,
--        '$2y$10$3i6nd6pcL35bp9t4xOq4j.E7GglJTj6ZUSMu6t8Y.V/kK7OOOwiLK',
--        'mahasiswa', m.id
-- FROM mahasiswa m
-- WHERE m.nim IN ('2201001', '2201009');

-- -----------------------------------------------------
-- Bagian 5 - Verifikasi hasil migrasi.
-- LEFT JOIN dipakai agar akun admin (mahasiswa_id NULL)
-- tetap muncul di hasil.
-- -----------------------------------------------------
SELECT u.id, u.username, u.nama_lengkap, u.role, u.aktif,
       u.mahasiswa_id, m.nim, m.nama
FROM users u
LEFT JOIN mahasiswa m ON m.id = u.mahasiswa_id
ORDER BY u.role, u.username;
