-- Migrasi sekali jalan untuk mengubah jurusan teks menjadi relasi.
-- Cadangkan db_belajar sebelum menjalankan file ini.
USE db_belajar;

CREATE TABLE IF NOT EXISTS jurusan (
    id INT NOT NULL AUTO_INCREMENT,
    kode_jurusan VARCHAR(10) NOT NULL,
    nama_jurusan VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_kode_jurusan (kode_jurusan),
    UNIQUE KEY uq_nama_jurusan (nama_jurusan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS dosen (
    id INT NOT NULL AUTO_INCREMENT,
    nidn VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nidn (nidn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS mata_kuliah (
    id INT NOT NULL AUTO_INCREMENT,
    kode_mk VARCHAR(10) NOT NULL,
    nama_mk VARCHAR(100) NOT NULL,
    sks TINYINT NOT NULL,
    dosen_id INT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_kode_mk (kode_mk),
    CONSTRAINT fk_mk_dosen FOREIGN KEY (dosen_id)
        REFERENCES dosen (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS nilai (
    id INT NOT NULL AUTO_INCREMENT,
    mahasiswa_id INT NOT NULL,
    mata_kuliah_id INT NOT NULL,
    nilai_angka DECIMAL(5,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nilai (mahasiswa_id, mata_kuliah_id),
    CONSTRAINT fk_nilai_mahasiswa FOREIGN KEY (mahasiswa_id)
        REFERENCES mahasiswa (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_nilai_mk FOREIGN KEY (mata_kuliah_id)
        REFERENCES mata_kuliah (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO jurusan (kode_jurusan, nama_jurusan) VALUES
    ('IF', 'Informatika'),
    ('MN', 'Manajemen'),
    ('SI', 'Sistem Informasi'),
    ('TI', 'Teknik Informatika'),
    ('TK', 'Teknik Kebidanan'),
    ('AK', 'Akuntansi');

INSERT IGNORE INTO dosen (nidn, nama) VALUES
    ('0401018801', 'Dr. Rina Kartika, M.Kom'),
    ('0415058502', 'Ahmad Fauzi, M.T.'),
    ('0422079003', 'Siti Marlina, M.M.'),
    ('0430118204', 'Bambang Hartono, M.Kom');

INSERT IGNORE INTO mata_kuliah (kode_mk, nama_mk, sks, dosen_id)
SELECT 'IF101', 'Algoritma dan Pemrograman', 3, d.id FROM dosen d WHERE d.nidn = '0415058502'
UNION ALL SELECT 'IF201', 'Basis Data', 3, d.id FROM dosen d WHERE d.nidn = '0401018801'
UNION ALL SELECT 'IF301', 'Pemrograman Web', 3, d.id FROM dosen d WHERE d.nidn = '0401018801'
UNION ALL SELECT 'SI201', 'Analisis Sistem Informasi', 3, d.id FROM dosen d WHERE d.nidn = '0415058502'
UNION ALL SELECT 'MN101', 'Pengantar Manajemen', 2, d.id FROM dosen d WHERE d.nidn = '0422079003'
UNION ALL SELECT 'UM101', 'Bahasa Inggris', 2, NULL FROM DUAL;

ALTER TABLE mahasiswa ADD COLUMN jurusan_id INT NULL AFTER jurusan;

UPDATE mahasiswa m
INNER JOIN jurusan j ON j.nama_jurusan = m.jurusan
SET m.jurusan_id = j.id;

-- Pastikan hasil query ini 0 sebelum melanjutkan.
SELECT COUNT(*) AS mahasiswa_belum_terpetakan FROM mahasiswa WHERE jurusan_id IS NULL;

ALTER TABLE mahasiswa MODIFY jurusan_id INT NOT NULL;
ALTER TABLE mahasiswa
    ADD CONSTRAINT fk_mahasiswa_jurusan FOREIGN KEY (jurusan_id)
    REFERENCES jurusan (id) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Isi nilai contoh berdasarkan NIM dan nama yang ada pada database proyek ini.
-- Pasangan NIM dan nama dipakai agar NIM ganda tidak menggandakan nilai.
INSERT IGNORE INTO nilai (mahasiswa_id, mata_kuliah_id, nilai_angka)
SELECT m.id, mk.id, v.nilai_angka
FROM (
    SELECT '2201003' AS nim, 'Edelweis' AS nama, 'IF101' AS kode_mk, 85.00 AS nilai_angka
    UNION ALL SELECT '2201003', 'Edelweis', 'IF201', 78.00
    UNION ALL SELECT '2201003', 'Edelweis', 'IF301', 90.00
    UNION ALL SELECT '2201004', 'Prayoga', 'IF101', 70.00
    UNION ALL SELECT '2201004', 'Prayoga', 'IF201', 65.00
    UNION ALL SELECT '2201004', 'Prayoga', 'SI201', 88.00
    UNION ALL SELECT '220012', 'Muhamad', 'IF101', 92.00
    UNION ALL SELECT '220012', 'Muhamad', 'IF301', 81.00
    UNION ALL SELECT '2201003', 'Citra Ayu', 'IF201', 80.00
    UNION ALL SELECT '2201003', 'Citra Ayu', 'SI201', 75.00
    UNION ALL SELECT '2201001', 'Ujang Kopling', 'IF101', 60.00
    UNION ALL SELECT '2201001', 'Ujang Kopling', 'IF301', 72.00
    UNION ALL SELECT '2201032', 'Budiono', 'MN101', 85.00
    UNION ALL SELECT '2201032', 'Budiono', 'UM101', 90.00
) AS v
INNER JOIN mahasiswa m ON m.nim = v.nim AND m.nama = v.nama
INNER JOIN mata_kuliah mk ON mk.kode_mk = v.kode_mk;

-- Hapus kolom lama setelah semua halaman PHP memakai jurusan_id.
ALTER TABLE mahasiswa DROP COLUMN jurusan;
