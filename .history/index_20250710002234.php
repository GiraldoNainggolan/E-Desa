<?php
session_start();
require_once 'config.php';

// Check for logout success message
$logout_message = isset($_GET['logout']) && $_GET['logout'] == 'success';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Desa: Aplikasi Layanan Desa Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <!-- Particle Background -->
    <div class="particles" id="particles"></div>

    <!-- Fixed Header -->
    <header class="header-custom text-white p-4" id="header">
        <div class="container mx-auto">
            <div class="dashboard-nav">
                <h1 class="dashboard-title">🏛️ e-Desa</h1>
                <nav class="flex items-center space-x-2">
                    <a href="#home" class="nav-link">Beranda</a>
                    <a href="#services" class="nav-link">Layanan</a>
                    <a href="#about" class="nav-link">Tentang</a>
                    <a href="#contact" class="nav-link">Kontak</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard_<?php echo $_SESSION['role']; ?>.php" class="nav-link bg-green-500 hover:bg-green-600">Dashboard</a>
                        <a href="logout.php" class="nav-link bg-red-500 hover:bg-red-600">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link bg-blue-500 hover:bg-blue-600">Login</a>
                        <a href="register.php" class="nav-link bg-green-500 hover:bg-green-600">Daftar</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- Logout Success Alert -->
    <?php if ($logout_message): ?>
        <div class="fixed top-20 right-4 z-50 alert alert-success max-w-md">
            <i class="fas fa-check-circle mr-2"></i>
            <strong>Berhasil logout!</strong> Terima kasih telah menggunakan layanan kami.
        </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container mx-auto px-4">
            <h1 class="hero-title">Revolusi Digital untuk Desa Modern</h1>
            <p class="hero-subtitle">Transformasi layanan administrasi desa dengan teknologi terdepan untuk kemudahan warga</p>
            <div class="hero-buttons space-x-4">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="hero-button inline-block text-white no-underline">
                        <i class="fas fa-rocket mr-2"></i>Mulai Sekarang
                    </a>
                    <a href="#services" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-xl font-semibold hover:bg-white hover:text-blue-600 transition-all duration-300 inline-block">
                        <i class="fas fa-info-circle mr-2"></i>Pelajari Lebih Lanjut
                    </a>
                <?php else: ?>
                    <a href="dashboard_<?php echo $_SESSION['role']; ?>.php" class="hero-button inline-block text-white no-underline">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <?php
                $stats = [
                    ['icon' => 'fas fa-users', 'number' => $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'warga'")->fetch_assoc()['count'], 'label' => 'Warga Terdaftar'],
                    ['icon' => 'fas fa-file-alt', 'number' => $conn->query("SELECT COUNT(*) as count FROM surat")->fetch_assoc()['count'], 'label' => 'Surat Diproses'],
                    ['icon' => 'fas fa-comments', 'number' => $conn->query("SELECT COUNT(*) as count FROM pengaduan")->fetch_assoc()['count'], 'label' => 'Pengaduan Ditangani'],
                    ['icon' => 'fas fa-info-circle', 'number' => $conn->query("SELECT COUNT(*) as count FROM informasi_desa")->fetch_assoc()['count'], 'label' => 'Informasi Publik']
                ];
                
                foreach ($stats as $index => $stat):
                ?>
                    <div class="stats-item scroll-animate" style="animation-delay: <?php echo $index * 0.2; ?>s">
                        <i class="<?php echo $stat['icon']; ?> text-4xl mb-4 text-green-400"></i>
                        <div class="stats-number"><?php echo $stat['number']; ?>+</div>
                        <div class="stats-label"><?php echo $stat['label']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-16 bg-gray-50" id="services">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-4 scroll-animate">🚀 Layanan Unggulan</h2>
            <p class="text-center text-gray-600 mb-12 scroll-animate">Nikmati berbagai layanan digital yang memudahkan kehidupan Anda</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $services = [
                    ['icon' => '📄', 'title' => 'Pengajuan Surat Online', 'desc' => 'Ajukan surat pengantar KTP, KK, dan SKTM secara online tanpa perlu antre', 'color' => 'from-blue-500 to-purple-600'],
                    ['icon' => '📝', 'title' => 'Sistem Pengaduan', 'desc' => 'Sampaikan keluhan dan saran untuk pembangunan desa yang lebih baik', 'color' => 'from-green-500 to-teal-600'],
                    ['icon' => 'ℹ️', 'title' => 'Informasi Publik', 'desc' => 'Akses informasi terbaru tentang kegiatan dan program desa', 'color' => 'from-orange-500 to-red-600']
                ];
                
                foreach ($services as $index => $service):
                ?>
                    <div class="feature-card scroll-animate" style="animation-delay: <?php echo $index * 0.2; ?>s">
                        <div class="feature-icon"><?php echo $service['icon']; ?></div>
                        <h3 class="text-xl font-bold mb-4 text-gray-800"><?php echo $service['title']; ?></h3>
                        <p class="text-gray-600 mb-6"><?php echo $service['desc']; ?></p>
                        <div class="h-1 bg-gradient-to-r <?php echo $service['color']; ?> rounded-full"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Information Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-4 scroll-animate">📢 Informasi Terbaru</h2>
            <p class="text-center text-gray-600 mb-12 scroll-animate">Dapatkan update terbaru tentang kegiatan dan program desa</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $sql = "SELECT * FROM informasi_desa ORDER BY created_at DESC LIMIT 6";
                $result = $conn->query($sql);
                $index = 0;
                while ($row = $result->fetch_assoc()):
                ?>
                    <div class="card scroll-animate" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <div class="card-header">
                            <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($row['judul']); ?></h3>
                        </div>
                        <div class="card-body">
                            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars(substr($row['isi'], 0, 120)) . '...'; ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                </span>
                                <button class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                    <i class="fas fa-arrow-right mr-1"></i>Baca Selengkapnya
                                </button>
                            </div>
                        </div>
                    </div>
                <?php 
                    $index++;
                endwhile; 
                ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gradient-to-br from-blue-50 to-purple-50">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-12 scroll-animate">✨ Mengapa Memilih e-Desa?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php
                $features = [
                    ['icon' => 'fas fa-clock', 'title' => '24/7 Tersedia', 'desc' => 'Layanan online yang dapat diakses kapan saja'],
                    ['icon' => 'fas fa-shield-alt', 'title' => 'Aman & Terpercaya', 'desc' => 'Data pribadi Anda dijamin keamanannya'],
                    ['icon' => 'fas fa-mobile-alt', 'title' => 'Mobile Friendly', 'desc' => 'Akses mudah dari smartphone Anda'],
                    ['icon' => 'fas fa-bolt', 'title' => 'Proses Cepat', 'desc' => 'Pengajuan surat dalam hitungan menit']
                ];
                
                foreach ($features as $index => $feature):
                ?>
                    <div class="text-center p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow scroll-animate" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <i class="<?php echo $feature['icon']; ?> text-3xl text-blue-600 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo $feature['title']; ?></h3>
                        <p class="text-gray-600"><?php echo $feature['desc']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 bg-white" id="testimonials">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-12 scroll-animate">💬 Testimoni Warga</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $testimonials = [
                    ['name' => 'Budi Santoso', 'role' => 'Warga Desa', 'text' => 'Sangat membantu! Sekarang saya bisa mengurus surat-surat tanpa harus antre panjang di kantor desa.'],
                    ['name' => 'Siti Nurhaliza', 'role' => 'Ibu Rumah Tangga', 'text' => 'Aplikasi yang sangat user-friendly. Informasi desa juga selalu update dan mudah diakses.'],
                    ['name' => 'Ahmad Wijaya', 'role' => 'Kepala Keluarga', 'text' => 'Pelayanan yang cepat dan efisien. Terima kasih untuk inovasi yang memudahkan warga.']
                ];
                
                foreach ($testimonials as $index => $testimonial):
                ?>
                    <div class="testimonial-card scroll-animate" style="animation-delay: <?php echo $index * 0.2; ?>s">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                <?php echo substr($testimonial['name'], 0, 1); ?>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800"><?php echo $testimonial['name']; ?></h4>
                                <p class="text-sm text-gray-600"><?php echo $testimonial['role']; ?></p>
                            </div>
                        </div>
                        <p class="text-gray-700 italic">"<?php echo $testimonial['text']; ?>"</p>
                        <div class="flex text-yellow-400 mt-4">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-16 bg-gray-900 text-white" id="contact">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12 scroll-animate">📞 Hubungi Kami</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="scroll-animate">
                    <h3 class="text-2xl font-semibold mb-6">Informasi Kontak</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-blue-400 mr-4"></i>
                            <span>Jl. Raya Desa No. 123, Kecamatan ABC, Kabupaten XYZ</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-blue-400 mr-4"></i>
                            <span>(0123) 456-7890</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-blue-400 mr-4"></i>
                            <span>info@e-desa.com</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock text-blue-400 mr-4"></i>
                            <span>Senin - Jumat: 08:00 - 16:00</span>
                        </div>
                    </div>
                </div>
                
                <div class="scroll-animate">
                    <h3 class="text-2xl font-semibold mb-6">Jam Operasional</h3>
                    <div class="bg-gray-800 rounded-lg p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span>Senin - Kamis</span>
                                <span>08:00 - 16:00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Jumat</span>
                                <span>08:00 - 11:30</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sabtu - Minggu</span>
                                <span class="text-red-400">Tutup</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">🏛️ e-Desa</h3>
                    <p class="text-gray-400">Sistem informasi desa digital untuk pelayanan yang lebih baik dan efisien.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Layanan</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Pengajuan Surat</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pengaduan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Informasi Publik</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Tentang</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Profil Desa</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Visi & Misi</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Struktur Organisasi</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Ikuti Kami</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 e-Desa. Semua hak dilindungi undang-undang. Dibuat dengan ❤️ untuk kemajuan desa.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Animations -->
    <script>
        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.scroll-animate').forEach(el => {
            observer.observe(el);
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Particle background
        function createParticles() {
            const particles = document.getElementById('particles');
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 10 + 's';
                particle.style.animationDuration = (Math.random() * 5 + 5) + 's';
                particles.appendChild(particle);
            }
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Initialize particles
        createParticles();

        // Auto-hide logout message
        setTimeout(() => {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.style.animation = 'slideOut 0.5s ease-out forwards';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    </script>

    <style>
        @keyframes slideOut {
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
    </style>
</body>
</html>