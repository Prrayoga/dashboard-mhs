CREATE TABLE IF NOT EXISTS users (
 id INT(11) NOT NULL AUTO_INCREMENT,
 username VARCHAR(50) NOT NULL,
 nama_lengkap VARCHAR(100) NOT NULL,
 password VARCHAR(255) NOT NULL, -- 255 karakter: muat untuk hash password_hash()
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (id),
 UNIQUE KEY uq_username (username) -- username tidak boleh kembar
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;