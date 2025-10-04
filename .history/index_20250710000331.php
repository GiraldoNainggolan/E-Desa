<?php
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Desa: Aplikasi Layanan Desa Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <header class="header-custom text-white p-4">
        <div class="container mx-auto">
            <div class="dashboard-nav">
                <h1 class="dashboard-title">🏛️ e-Desa</h1>
                <nav class="flex items-center space-x-2">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard_<?php echo $_SESSION['role']; ?>.php" class="nav-link">Dashboard</a>
                        <a href="logout.php" class="nav-link">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link">Login</a>
                        <a href="register.php" class="nav-link">Daftar</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <?php if (!isset($_SESSION['user_id'])): ?>
    <section class="hero-section">
        <div class="container mx-auto px-4">
            <h1 class="hero-title fade-in">Selamat Datang di e-Desa</h1>
            <p class="hero-subtitle fade-in">Layanan digital untuk memudahkan administrasi desa Anda</p>
            <div class="space-x-4 fade-in">
                <a href="register.php" class="hero-button inline-block text-white no-underline">Daftar Sekarang</a>
                <a href="login.php" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-xl font-semibold hover:bg-white hover:text-blue-600 transition-all duration-300 inline-block">Masuk</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <main class="container mx-auto mt-8 px-4">
        <section class="mb-12">
            <h2 class="section-title text-center mb-8">📢 Informasi Publik Desa</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $sql = "SELECT * FROM informasi_desa ORDER BY created_at DESC LIMIT 6";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()):
                ?>
                    <div class="card fade-in">
                        <div class="card-header">
                            <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($row['judul']); ?></h3>
                        </div>
                        <div class="card-body">
                            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars(substr($row['isi'], 0, 150)) . '...'; ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">📅 <?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
                                <button class="text-blue-600 hover:text-blue-800 font-medium">Baca Selengkapnya</button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <?php if (!isset($_SESSION['user_id'])): ?>
        <section class="mb-12">
            <h2 class="section-title text-center mb-8">🚀 Layanan Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="text-4xl mb-4">📄</div>
                        <h3 class="text-xl font-bold mb-2">Pengajuan Surat</h3>
                        <p class="text-gray-600">Ajukan surat pengantar online dengan mudah dan cepat</p>
                    </div>
                </div>
                <div class="card text-center">
                    <div class="card-body">
                        <div class="text-4xl mb-4">📝</div>
                        <h3 class="text-xl font-bold mb-2">Pengaduan</h3>
                        <p class="text-gray-600">Sampaikan keluhan dan saran untuk kemajuan desa</p>
                    </div>
                </div>
                <div class="card text-center">
                    <div class="card-body">
                        <div class="text-4xl mb-4">ℹ️</div>
                        <h3 class="text-xl font-bold mb-2">Informasi Publik</h3>
                        <p class="text-gray-600">Dapatkan informasi terbaru tentang kegiatan desa</p>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <footer class="footer mt-16">
        <div class="container mx-auto text-center px-4">
            <p class="text-lg font-medium">&copy; 2025 e-Desa. Semua hak dilindungi undang-undang.</p>
            <p class="text-sm opacity-75 mt-2">Dibuat dengan ❤️ untuk kemajuan desa</p>
        </div>
    </footer>
</body>
</html>