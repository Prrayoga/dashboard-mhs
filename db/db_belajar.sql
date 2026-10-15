CREATE DATABASE IF NOT EXISTS db_belajar;
USE db_belajar;
CREATE TABLE IF NOT EXISTS mahasiswa (
 id INT(11) NOT NULL AUTO_INCREMENT,
 nim VARCHAR(15) NOT NULL,
 nama VARCHAR(100) NOT NULL,
 jurusan VARCHAR(50) NOT NULL,
 email VARCHAR(100) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO mahasiswa (nim, nama, jurusan, email) VALUES
('2201001', 'Andi Saputra', 'Teknik Informatika', 'andi@example.com'),
('2201002', 'Budi Santoso', 'Sistem Informasi', 'budi@example.com'),
('2201003', 'Citra Ayu', 'Teknik Informatika', 'citra@example.com');
