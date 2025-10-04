<?php
/**
 * Installation Script for e-Desa Application
 * This script will create necessary database tables and default admin user
 */

// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$dbname = "e_desa";

try {
    // Create database connection
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select database
    $conn->select_db($dbname);
    
    // Create tables
    $tables = [
        // Users table
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nik VARCHAR(16) UNIQUE NOT NULL,
            nama VARCHAR(100) NOT NULL,
            email VARCHAR(100) NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('warga', 'admin') NOT NULL DEFAULT 'warga',
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        // Surat table
        "CREATE TABLE IF NOT EXISTS surat (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            jenis_surat VARCHAR(50) NOT NULL,
            keperluan TEXT NULL,
            status ENUM('menunggu', 'disetujui', 'ditolak') NOT NULL DEFAULT 'menunggu',
            catatan TEXT NULL,
            file_path VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        // Pengaduan table
        "CREATE TABLE IF NOT EXISTS pengaduan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            judul VARCHAR(255) NOT NULL,
            isi_pengaduan TEXT NOT NULL,
            kategori VARCHAR(50) NOT NULL,
            status ENUM('menunggu', 'diproses', 'selesai') NOT NULL DEFAULT 'menunggu',
            tanggapan TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        // Informasi desa table
        "CREATE TABLE IF NOT EXISTS informasi_desa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            judul VARCHAR(255) NOT NULL,
            isi TEXT NOT NULL,
            kategori VARCHAR(50) NOT NULL DEFAULT 'umum',
            is_published BOOLEAN DEFAULT TRUE,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        // Activity log table
        "CREATE TABLE IF NOT EXISTS activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            action VARCHAR(100) NOT NULL,
            details TEXT NULL,
            ip_address VARCHAR(45) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        // Settings table
        "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT NOT NULL,
            description TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )"
    ];
    
    // Execute table creation
    foreach ($tables as $table_sql) {
        if (!$conn->query($table_sql)) {
            throw new Exception("Error creating table: " . $conn->error);
        }
    }
    
    // Create default admin user
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $admin_sql = "INSERT IGNORE INTO users (nik, nama, email, password, role) VALUES ('1234567890123456', 'Administrator', 'admin@e-desa.com', '$admin_password', 'admin')";
    
    if (!$conn->query($admin_sql)) {
        throw new Exception("Error creating admin user: " . $conn->error);
    }
    
    // Insert default settings
    $settings = [
        ['desa_name', 'Desa Digital', 'Nama desa'],
        ['desa_address', 'Jl. Raya Desa No. 123', 'Alamat desa'],
        ['desa_phone', '(0123) 456-7890', 'Nomor telepon desa'],
        ['desa_email', 'info@e-desa.com', 'Email desa'],
        ['app_version', '1.0.0', 'Versi aplikasi'],
        ['maintenance_mode', '0', 'Mode maintenance']
    ];
    
    foreach ($settings as $setting) {
        $setting_sql = "INSERT IGNORE INTO settings (setting_key, setting_value, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($setting_sql);
        $stmt->bind_param("sss", $setting[0], $setting[1], $setting[2]);
        $stmt->execute();
    }
    
    // Sample data for testing
    $sample_info = "INSERT IGNORE INTO informasi_desa (judul, isi, kategori, created_by) VALUES 
        ('Selamat Datang di e-Desa', 'Aplikasi layanan desa digital telah resmi diluncurkan. Warga dapat mengakses berbagai layanan administratif secara online.', 'pengumuman', 1),
        ('Jadwal Posyandu Bulan Ini', 'Posyandu akan dilaksanakan setiap hari Selasa minggu kedua di Balai Desa. Harap membawa buku KIA dan KMS.', 'kesehatan', 1),
        ('Program Bantuan Sosial', 'Pendaftaran program bantuan sosial untuk keluarga kurang mampu akan dibuka mulai tanggal 1 setiap bulan.', 'bantuan', 1)";
    
    $conn->query($sample_info);
    
    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Instalasi e-Desa</title>
        <link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>
    </head>
    <body class='bg-green-50 flex items-center justify-center min-h-screen'>
        <div class='bg-white p-8 rounded-lg shadow-lg max-w-md w-full'>
            <div class='text-center mb-6'>
                <div class='text-6xl mb-4'>✅</div>
                <h1 class='text-2xl font-bold text-green-600'>Instalasi Berhasil!</h1>
            </div>
            
            <div class='space-y-4'>
                <div class='bg-green-100 p-4 rounded-lg'>
                    <h3 class='font-semibold text-green-800 mb-2'>Database berhasil dibuat</h3>
                    <p class='text-green-700 text-sm'>Semua tabel telah dibuat dengan sukses.</p>
                </div>
                
                <div class='bg-blue-100 p-4 rounded-lg'>
                    <h3 class='font-semibold text-blue-800 mb-2'>Admin Default</h3>
                    <p class='text-blue-700 text-sm'>
                        <strong>NIK:</strong> 1234567890123456<br>
                        <strong>Password:</strong> admin123
                    </p>
                </div>
                
                <div class='bg-yellow-100 p-4 rounded-lg'>
                    <h3 class='font-semibold text-yellow-800 mb-2'>Keamanan</h3>
                    <p class='text-yellow-700 text-sm'>Segera ganti password default dan hapus file install.php setelah instalasi.</p>
                </div>
            </div>
            
            <div class='mt-6 text-center'>
                <a href='index.php' class='bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors'>
                    Mulai Menggunakan e-Desa
                </a>
            </div>
        </div>
    </body>
    </html>
    ";
    
} catch (Exception $e) {
    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Error Instalasi</title>
        <link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>
    </head>
    <body class='bg-red-50 flex items-center justify-center min-h-screen'>
        <div class='bg-white p-8 rounded-lg shadow-lg max-w-md w-full'>
            <div class='text-center mb-6'>
                <div class='text-6xl mb-4'>❌</div>
                <h1 class='text-2xl font-bold text-red-600'>Error Instalasi</h1>
            </div>
            
            <div class='bg-red-100 p-4 rounded-lg mb-4'>
                <p class='text-red-700'>" . $e->getMessage() . "</p>
            </div>
            
            <div class='text-center'>
                <button onclick='window.location.reload()' class='bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors'>
                    Coba Lagi
                </button>
            </div>
        </div>
    </body>
    </html>
    ";
}

$conn->close();
?>
