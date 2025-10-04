CREATE DATABASE e_desa;

USE e_desa;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(16) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('warga', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    jenis_surat VARCHAR(50) NOT NULL,
    status ENUM('menunggu', 'disetujui', 'ditolak') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE pengaduan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    isi_pengaduan TEXT NOT NULL,
    status ENUM('menunggu', 'diproses', 'selesai') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE informasi_desa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);