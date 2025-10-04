<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verifikasi_surat'])) {
    $surat_id = $_POST['surat_id'];
    $status = $_POST['status'];
    $sql = "UPDATE surat SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $surat_id);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_informasi'])) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $sql = "INSERT INTO informasi_desa (judul, isi) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $judul, $isi);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - e-Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body class="dashboard-container">
    <header class="dashboard-header">
        <div class="container mx-auto px-4">
            <div class="dashboard-nav">
                <h1 class="dashboard-title">⚙️ Dashboard Admin</h1>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="nav-link">Beranda</a>
                    <a href="logout.php" class="nav-link bg-red-500 hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="dashboard-content container mx-auto px-4">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            $stats = [
                ['label' => 'Total Warga', 'query' => "SELECT COUNT(*) as count FROM users WHERE role = 'warga'", 'icon' => '👥'],
                ['label' => 'Surat Menunggu', 'query' => "SELECT COUNT(*) as count FROM surat WHERE status = 'menunggu'", 'icon' => '📄'],
                ['label' => 'Pengaduan Aktif', 'query' => "SELECT COUNT(*) as count FROM pengaduan WHERE status != 'selesai'", 'icon' => '📝'],
                ['label' => 'Informasi Published', 'query' => "SELECT COUNT(*) as count FROM informasi_desa", 'icon' => 'ℹ️']
            ];
            
            foreach ($stats as $stat):
                $result = $conn->query($stat['query']);
                $count = $result->fetch_assoc()['count'];
            ?>
                <div class="stats-card fade-in">
                    <div class="text-4xl mb-2"><?php echo $stat['icon']; ?></div>
                    <div class="stats-number"><?php echo $count; ?></div>
                    <div class="stats-label"><?php echo $stat['label']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <section class="fade-in">
                <h2 class="section-title">✅ Verifikasi Permohonan Surat</h2>
                <div class="space-y-4">
                    <?php
                    $sql = "SELECT s.id, s.jenis_surat, s.status, s.created_at, u.nama FROM surat s JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                        <div class="card">
                            <div class="card-body">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-800">
                                            <?php 
                                            $icons = ['KTP' => '🆔', 'KK' => '👨‍👩‍👧‍👦', 'SKTM' => '💰'];
                                            echo $icons[$row['jenis_surat']] . ' ' . htmlspecialchars($row['jenis_surat']); 
                                            ?>
                                        </h3>
                                        <p class="text-gray-600">👤 <?php echo htmlspecialchars($row['nama']); ?></p>
                                        <p class="text-sm text-gray-500">📅 <?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></p>
                                    </div>
                                    <span class="status-badge status-<?php echo $row['status']; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </div>
                                <form method="POST" class="flex gap-2">
                                    <input type="hidden" name="surat_id" value="<?php echo $row['id']; ?>">
                                    <select name="status" class="form-input flex-1">
                                        <option value="menunggu" <?php echo $row['status'] === 'menunggu' ? 'selected' : ''; ?>>⏳ Menunggu</option>
                                        <option value="disetujui" <?php echo $row['status'] === 'disetujui' ? 'selected' : ''; ?>>✅ Disetujui</option>
                                        <option value="ditolak" <?php echo $row['status'] === 'ditolak' ? 'selected' : ''; ?>>❌ Ditolak</option>
                                    </select>
                                    <button type="submit" name="verifikasi_surat" class="btn-primary">💾 Simpan</button>
                                </form>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <div class="text-center py-8">
                            <div class="text-6xl mb-4">📄</div>
                            <p class="text-gray-500">Tidak ada permohonan surat</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="fade-in">
                <h2 class="section-title">📢 Tambah Informasi Publik</h2>
                <div class="card">
                    <div class="card-body">
                        <form method="POST" class="space-y-4">
                            <div class="form-group">
                                <label class="form-label">📝 Judul Informasi</label>
                                <input type="text" name="judul" class="form-input" placeholder="Masukkan judul informasi" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">📄 Isi Informasi</label>
                                <textarea name="isi" class="form-input h-32" placeholder="Masukkan isi informasi lengkap..." required></textarea>
                            </div>
                            <button type="submit" name="tambah_informasi" class="btn-primary w-full">
                                📤 Publikasikan
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        <section class="mt-8 fade-in">
            <h2 class="section-title">📋 Pengaduan Warga</h2>
            <div class="space-y-4">
                <?php
                $sql = "SELECT p.id, p.isi_pengaduan, p.status, p.created_at, u.nama FROM pengaduan p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg text-gray-800 mb-2">👤 <?php echo htmlspecialchars($row['nama']); ?></h3>
                                    <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($row['isi_pengaduan']); ?></p>
                                    <p class="text-sm text-gray-500">📅 <?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <div class="text-center py-8">
                        <div class="text-6xl mb-4">📝</div>
                        <p class="text-gray-500">Tidak ada pengaduan</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>