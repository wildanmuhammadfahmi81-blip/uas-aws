CREATE DATABASE IF NOT EXISTS uas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE uas_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash CHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_notes_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO users (name, username, password_hash)
VALUES ('Wildan Fahmi', 'admin', SHA2('admin123', 256))
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO notes (title, description, created_by)
SELECT 'Data awal UAS', 'Data ini dibuat otomatis dari database/init.sql saat MariaDB pertama kali berjalan.', id
FROM users
WHERE username = 'admin'
ON DUPLICATE KEY UPDATE title = title;
