<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warga') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajukan_surat'])) {
    $jenis_surat = $_POST['jenis_surat'];
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO surat (user_id, jenis_surat, status) VALUES (?, ?, 'menunggu')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $jenis_surat);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajukan_pengaduan'])) {
    $isi_pengaduan = $_POST['isi_pengaduan'];
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO pengaduan (user_id, isi_pengaduan, status) VALUES (?, ?, 'menunggu')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $isi_pengaduan);
    $stmt->execute();
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
    <link href="styles.css" rel="stylesheet">
</head>
<body class="dashboard-container">
    <header class="dashboard-header">
        <div class="container mx-auto px-4">
            <div class="dashboard-nav">
                <h1 class="dashboard-title">👤 Dashboard Warga</h1>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="nav-link">Beranda</a>
                    <a href="logout.php" class="nav-link bg-red-500 hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="dashboard-content container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <section class="fade-in">
                <h2 class="section-title">📄 Ajukan Surat Pengantar</h2>
                <div class="card">
                    <div class="card-body">
                        <form method="POST" class="space-y-4">
                            <div class="form-group">
                                <label class="form-label">Jenis Surat</label>
                                <select name="jenis_surat" class="form-input" required>
                                    <option value="">Pilih jenis surat</option>
                                    <option value="KTP">🆔 Surat Pengantar KTP</option>
                                    <option value="KK">👨‍👩‍👧‍👦 Surat Pengantar KK</option>
                                    <option value="SKTM">💰 Surat Keterangan Tidak Mampu</option>
                                </select>
                            </div>
                            <button type="submit" name="ajukan_surat" class="btn-primary w-full">
                                📤 Ajukan Surat
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <section class="fade-in">
                <h2 class="section-title">📝 Ajukan Pengaduan</h2>
                <div class="card">
                    <div class="card-body">
                        <form method="POST" class="space-y-4">
                            <div class="form-group">
                                <label class="form-label">Isi Pengaduan</label>
                                <textarea name="isi_pengaduan" class="form-input h-32" placeholder="Jelaskan pengaduan Anda..." required></textarea>
                            </div>
                            <button type="submit" name="ajukan_pengaduan" class="btn-primary w-full">
                                📨 Kirim Pengaduan
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        <section class="mt-8 fade-in">
            <h2 class="section-title">📋 Riwayat Permohonan Surat</h2>
            <div class="space-y-4">
                <?php
                $user_id = $_SESSION['user_id'];
                $sql = "SELECT * FROM surat WHERE user_id = ? ORDER BY created_at DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800 mb-2">
                                        <?php 
                                        $icons = ['KTP' => '🆔', 'KK' => '👨‍👩‍👧‍👦', 'SKTM' => '💰'];
                                        echo $icons[$row['jenis_surat']] . ' ' . htmlspecialchars($row['jenis_surat']); 
                                        ?>
                                    </h3>
                                    <p class="text-gray-600 mb-2">📅 <?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></p>
                                </div>
                                <div>
                                    <span class="status-badge status-<?php echo $row['status']; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php if ($row['status'] === 'disetujui'): ?>
                                <div class="mt-4">
                                    <a href="download_surat.php?id=<?php echo $row['id']; ?>" class="btn-success">
                                        📥 Unduh Surat
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <div class="text-center py-8">
                        <div class="text-6xl mb-4">📄</div>
                        <p class="text-gray-500">Belum ada riwayat permohonan surat</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>