<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warga') {
    header("Location: login.php");
    exit;
}

// Get user info
$user_id = $_SESSION['user_id'];
$user_info = null;
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_info = $result->fetch_assoc();
} catch (Exception $e) {
    error_log("Error fetching user info: " . $e->getMessage());
}

// Initialize messages
$success_message = '';
$error_message = '';

// Handle surat submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajukan_surat'])) {
    try {
        $jenis_surat = trim($_POST['jenis_surat']);
        $keperluan = trim($_POST['keperluan'] ?? '');
        
        if (empty($jenis_surat)) {
            throw new Exception("Jenis surat harus dipilih");
        }
        
        $sql = "INSERT INTO surat (user_id, jenis_surat, keperluan, status, created_at) VALUES (?, ?, ?, 'menunggu', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $jenis_surat, $keperluan);
        
        if ($stmt->execute()) {
            $success_message = "Permohonan surat berhasil diajukan!";
        } else {
            throw new Exception("Gagal mengajukan surat: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Handle pengaduan submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajukan_pengaduan'])) {
    try {
        $judul = trim($_POST['judul_pengaduan']);
        $isi_pengaduan = trim($_POST['isi_pengaduan']);
        $kategori = trim($_POST['kategori'] ?? 'umum');
        
        if (empty($judul) || empty($isi_pengaduan)) {
            throw new Exception("Judul dan isi pengaduan harus diisi");
        }
        
        $sql = "INSERT INTO pengaduan (user_id, judul, isi_pengaduan, kategori, status, created_at) VALUES (?, ?, ?, ?, 'menunggu', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $judul, $isi_pengaduan, $kategori);
        
        if ($stmt->execute()) {
            $success_message = "Pengaduan berhasil dikirim!";
        } else {
            throw new Exception("Gagal mengirim pengaduan: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Warga - e-Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Success/Error Messages -->
    <?php if (!empty($success_message)): ?>
        <div class="fixed top-4 right-4 z-50 alert alert-success max-w-md">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="fixed top-4 right-4 z-50 alert alert-error max-w-md">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="sidebar fixed inset-y-0 left-0 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 z-50">
            <div class="flex items-center justify-center h-20 shadow-md bg-gradient-to-r from-blue-600 to-purple-600">
                <h1 class="text-xl font-bold text-white">🏛️ e-Desa</h1>
            </div>
            
            <nav class="mt-6">
                <div class="px-4 py-2">
                    <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                            <?php echo strtoupper(substr($user_info['nama'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($user_info['nama'] ?? 'User'); ?></p>
                            <p class="text-xs text-gray-600">Warga Desa</p>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 mt-6">
                    <a href="#dashboard" class="sidebar-link active" data-target="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="#surat" class="sidebar-link" data-target="surat">
                        <i class="fas fa-file-alt"></i>
                        <span>Ajukan Surat</span>
                    </a>
                    <a href="#pengaduan" class="sidebar-link" data-target="pengaduan">
                        <i class="fas fa-comments"></i>
                        <span>Pengaduan</span>
                    </a>
                    <a href="#riwayat" class="sidebar-link" data-target="riwayat">
                        <i class="fas fa-history"></i>
                        <span>Riwayat</span>
                    </a>
                    <a href="#profil" class="sidebar-link" data-target="profil">
                        <i class="fas fa-user"></i>
                        <span>Profil</span>
                    </a>
                </div>
                
                <div class="px-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="index.php" class="sidebar-link">
                        <i class="fas fa-home"></i>
                        <span>Beranda</span>
                    </a>
                    <a href="logout.php" class="sidebar-link text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-0">
            <!-- Header -->
            <header class="bg-white shadow-sm h-20 flex items-center justify-between px-6">
                <div class="flex items-center">
                    <button class="sidebar-toggle lg:hidden mr-4 p-2 rounded-md hover:bg-gray-100">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="text-2xl font-bold text-gray-800">Dashboard Warga</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <i class="fas fa-bell text-gray-600 cursor-pointer hover:text-blue-600"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs">3</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <?php echo date('d M Y, H:i'); ?>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 p-6 overflow-auto">
                <!-- Dashboard Section -->
                <section id="dashboard" class="content-section">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Selamat Datang, <?php echo htmlspecialchars($user_info['nama'] ?? 'User'); ?>!</h3>
                        <p class="text-gray-600">Kelola keperluan administrasi Anda dengan mudah</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <?php
                        $stats = [
                            [
                                'title' => 'Surat Diajukan',
                                'value' => $conn->query("SELECT COUNT(*) as count FROM surat WHERE user_id = $user_id")->fetch_assoc()['count'],
                                'icon' => 'fas fa-file-alt',
                                'color' => 'bg-blue-500'
                            ],
                            [
                                'title' => 'Pengaduan Aktif',
                                'value' => $conn->query("SELECT COUNT(*) as count FROM pengaduan WHERE user_id = $user_id AND status != 'selesai'")->fetch_assoc()['count'],
                                'icon' => 'fas fa-comments',
                                'color' => 'bg-yellow-500'
                            ],
                            [
                                'title' => 'Surat Disetujui',
                                'value' => $conn->query("SELECT COUNT(*) as count FROM surat WHERE user_id = $user_id AND status = 'disetujui'")->fetch_assoc()['count'],
                                'icon' => 'fas fa-check-circle',
                                'color' => 'bg-green-500'
                            ]
                        ];
                        
                        foreach ($stats as $stat):
                        ?>
                            <div class="bg-white rounded-lg shadow-md p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600"><?php echo $stat['title']; ?></p>
                                        <p class="text-2xl font-bold text-gray-900"><?php echo $stat['value']; ?></p>
                                    </div>
                                    <div class="<?php echo $stat['color']; ?> rounded-full p-3">
                                        <i class="<?php echo $stat['icon']; ?> text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h4 class="text-lg font-semibold mb-4">Aksi Cepat</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button class="quick-action-btn" data-target="surat">
                                <i class="fas fa-file-plus text-blue-600"></i>
                                <span>Ajukan Surat Baru</span>
                            </button>
                            <button class="quick-action-btn" data-target="pengaduan">
                                <i class="fas fa-comment-plus text-green-600"></i>
                                <span>Buat Pengaduan</span>
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Surat Section -->
                <section id="surat" class="content-section hidden">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-6">📄 Ajukan Surat Pengantar</h3>
                        <form method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label class="form-label">Jenis Surat</label>
                                    <select name="jenis_surat" class="form-input" required>
                                        <option value="">Pilih jenis surat</option>
                                        <option value="KTP">🆔 Surat Pengantar KTP</option>
                                        <option value="KK">👨‍👩‍👧‍👦 Surat Pengantar KK</option>
                                        <option value="SKTM">💰 Surat Keterangan Tidak Mampu</option>
                                        <option value="Usaha">🏪 Surat Keterangan Usaha</option>
                                        <option value="Domisili">🏠 Surat Keterangan Domisili</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Keperluan</label>
                                    <input type="text" name="keperluan" class="form-input" placeholder="Untuk keperluan apa?" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Keterangan Tambahan (Opsional)</label>
                                <textarea name="keterangan" class="form-input h-24" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" name="ajukan_surat" class="btn-primary">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Ajukan Surat
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Pengaduan Section -->
                <section id="pengaduan" class="content-section hidden">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-6">📝 Ajukan Pengaduan</h3>
                        <form method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label class="form-label">Judul Pengaduan</label>
                                    <input type="text" name="judul_pengaduan" class="form-input" placeholder="Ringkasan singkat pengaduan" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-input" required>
                                        <option value="infrastruktur">🏗️ Infrastruktur</option>
                                        <option value="pelayanan">🏢 Pelayanan</option>
                                        <option value="kebersihan">🧹 Kebersihan</option>
                                        <option value="keamanan">🛡️ Keamanan</option>
                                        <option value="umum">📝 Umum</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Isi Pengaduan</label>
                                <textarea name="isi_pengaduan" class="form-input h-32" placeholder="Jelaskan pengaduan Anda secara detail..." required></textarea>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" name="ajukan_pengaduan" class="btn-primary">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Kirim Pengaduan
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Riwayat Section -->
                <section id="riwayat" class="content-section hidden">
                    <div class="space-y-6">
                        <!-- Riwayat Surat -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-semibold mb-6">📋 Riwayat Permohonan Surat</h3>
                            <div class="space-y-4">
                                <?php
                                $sql = "SELECT * FROM surat WHERE user_id = ? ORDER BY created_at DESC";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $user_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                if ($result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                ?>
                                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-semibold text-gray-800">
                                                    <?php 
                                                    $icons = ['KTP' => '🆔', 'KK' => '👨‍👩‍👧‍👦', 'SKTM' => '💰', 'Usaha' => '🏪', 'Domisili' => '🏠'];
                                                    echo ($icons[$row['jenis_surat']] ?? '📄') . ' ' . htmlspecialchars($row['jenis_surat']); 
                                                    ?>
                                                </h4>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    Keperluan: <?php echo htmlspecialchars($row['keperluan'] ?? 'Tidak disebutkan'); ?>
                                                </p>
                                                <p class="text-sm text-gray-500 mt-2">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?>
                                                </p>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                                <?php if ($row['status'] === 'disetujui'): ?>
                                                    <a href="download_surat.php?id=<?php echo $row['id']; ?>" class="btn-success mt-2 text-sm">
                                                        <i class="fas fa-download mr-1"></i>
                                                        Unduh
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <div class="text-center py-8">
                                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500">Belum ada riwayat permohonan surat</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Riwayat Pengaduan -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-semibold mb-6">📝 Riwayat Pengaduan</h3>
                            <div class="space-y-4">
                                <?php
                                $sql = "SELECT * FROM pengaduan WHERE user_id = ? ORDER BY created_at DESC";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $user_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                if ($result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                ?>
                                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($row['judul'] ?? 'Pengaduan'); ?></h4>
                                                <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars(substr($row['isi_pengaduan'], 0, 100)) . '...'; ?></p>
                                                <p class="text-sm text-gray-500 mt-2">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?>
                                                </p>
                                            </div>
                                            <span class="status-badge status-<?php echo $row['status']; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <div class="text-center py-8">
                                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500">Belum ada riwayat pengaduan</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Profil Section -->
                <section id="profil" class="content-section hidden">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-6">👤 Profil Pengguna</h3>
                        <div class="space-y-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    <?php echo strtoupper(substr($user_info['nama'] ?? 'U', 0, 1)); ?>
                                </div>
                                <div>
                                    <h4 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($user_info['nama'] ?? 'User'); ?></h4>
                                    <p class="text-gray-600">Warga Desa</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="form-label">NIK</label>
                                    <input type="text" value="<?php echo htmlspecialchars($user_info['nik'] ?? ''); ?>" class="form-input" readonly>
                                </div>
                                <div>
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" value="<?php echo htmlspecialchars($user_info['nama'] ?? ''); ?>" class="form-input" readonly>
                                </div>
                                <div>
                                    <label class="form-label">Email</label>
                                    <input type="email" value="<?php echo htmlspecialchars($user_info['email'] ?? 'Belum diisi'); ?>" class="form-input" readonly>
                                </div>
                                <div>
                                    <label class="form-label">Terdaftar Sejak</label>
                                    <input type="text" value="<?php echo date('d M Y', strtotime($user_info['created_at'])); ?>" class="form-input" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

    <script>
        // Sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebarOverlay = document.querySelector('.sidebar-overlay');
            const sidebarLinks = document.querySelectorAll('.sidebar-link[data-target]');
            const quickActionBtns = document.querySelectorAll('.quick-action-btn[data-target]');
            const contentSections = document.querySelectorAll('.content-section');

            // Toggle sidebar on mobile
            sidebarToggle?.addEventListener('click', function() {
                sidebar.classList.toggle('-translate-x-full');
                sidebarOverlay.classList.toggle('hidden');
            });

            // Close sidebar when clicking overlay
            sidebarOverlay?.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            });

            // Handle sidebar navigation
            function switchSection(targetId) {
                // Hide all sections
                contentSections.forEach(section => {
                    section.classList.add('hidden');
                });
                
                // Show target section
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }
                
                // Update sidebar active state
                sidebarLinks.forEach(link => {
                    link.classList.remove('active');
                });
                
                const activeLink = document.querySelector(`[data-target="${targetId}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
                
                // Close sidebar on mobile
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                }
            }

            // Sidebar links
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = this.getAttribute('data-target');
                    switchSection(target);
                });
            });

            // Quick action buttons
            quickActionBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    switchSection(target);
                });
            });

            // Auto-hide messages
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.animation = 'slideOut 0.5s ease-out forwards';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>

    <style>
        .sidebar-link {
            @apply flex items-center space-x-3 px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200 mb-2;
        }
        
        .sidebar-link.active {
            @apply bg-blue-50 text-blue-700 border-r-4 border-blue-700;
        }
        
        .quick-action-btn {
            @apply flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200 cursor-pointer;
        }
        
        @keyframes slideOut {
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
    </style>
</body>
</html>