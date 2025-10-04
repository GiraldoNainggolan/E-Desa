<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];

    $sql = "INSERT INTO users (nik, nama, password, role) VALUES (?, ?, ?, 'warga')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nik, $nama, $password);

    if ($stmt->execute()) {
        $success = "Pendaftaran berhasil! Silakan login.";
    } else {
        $error = "Gagal mendaftar, NIK mungkin sudah terdaftar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - e-Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="form-container w-full max-w-md mx-4 fade-in">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">🏛️ e-Desa</h1>
            <h2 class="text-xl font-semibold text-gray-600">Daftar Akun Baru</h2>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error mb-4">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success mb-4">
                <strong>Sukses:</strong> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4">
            <div class="form-group">
                <label class="form-label">📋 NIK</label>
                <input type="text" name="nik" class="form-input" placeholder="Masukkan NIK (16 digit)" maxlength="16" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">👤 Nama Lengkap</label>
                <input type="text" name="nama" class="form-input" placeholder="Masukkan nama lengkap Anda" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">🔒 Password</label>
                <input type="password" name="password" class="form-input" placeholder="Buat password yang kuat" required>
            </div>
            
            <button type="submit" class="btn-primary w-full text-lg py-3">
                Daftar Sekarang
            </button>
        </form>
        
        <div class="text-center mt-6">
            <p class="text-gray-600">Sudah punya akun? 
                <a href="login.php" class="text-blue-600 hover:text-blue-800 font-medium">Login di sini</a>
            </p>
            <a href="index.php" class="text-gray-500 hover:text-gray-700 text-sm mt-2 inline-block">← Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>