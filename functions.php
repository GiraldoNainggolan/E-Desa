<?php
require_once 'config.php';

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user has specific role
 */
function has_role($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Redirect to login if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Redirect to login if not admin
 */
function require_admin() {
    require_login();
    if (!has_role('admin')) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Get user information
 */
function get_user_info($user_id) {
    global $conn;
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Format date for display
 */
function format_date($date) {
    return date('d M Y H:i', strtotime($date));
}

/**
 * Get status badge class
 */
function get_status_class($status) {
    switch ($status) {
        case 'menunggu':
            return 'status-menunggu';
        case 'disetujui':
            return 'status-disetujui';
        case 'ditolak':
            return 'status-ditolak';
        case 'diproses':
            return 'status-diproses';
        case 'selesai':
            return 'status-selesai';
        default:
            return 'status-menunggu';
    }
}

/**
 * Generate secure password hash
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate random string for tokens
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Log user activity
 */
function log_activity($user_id, $action, $details = null) {
    global $conn;
    $sql = "INSERT INTO activity_log (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
}

/**
 * Get statistics for dashboard
 */
function get_dashboard_stats() {
    global $conn;
    
    $stats = [];
    
    // Total warga
    $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'warga'");
    $stats['total_warga'] = $result->fetch_assoc()['count'];
    
    // Total surat
    $result = $conn->query("SELECT COUNT(*) as count FROM surat");
    $stats['total_surat'] = $result->fetch_assoc()['count'];
    
    // Surat menunggu
    $result = $conn->query("SELECT COUNT(*) as count FROM surat WHERE status = 'menunggu'");
    $stats['surat_menunggu'] = $result->fetch_assoc()['count'];
    
    // Total pengaduan
    $result = $conn->query("SELECT COUNT(*) as count FROM pengaduan");
    $stats['total_pengaduan'] = $result->fetch_assoc()['count'];
    
    // Pengaduan aktif
    $result = $conn->query("SELECT COUNT(*) as count FROM pengaduan WHERE status != 'selesai'");
    $stats['pengaduan_aktif'] = $result->fetch_assoc()['count'];
    
    // Total informasi
    $result = $conn->query("SELECT COUNT(*) as count FROM informasi_desa");
    $stats['total_informasi'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

/**
 * Send notification (placeholder for future implementation)
 */
function send_notification($user_id, $message, $type = 'info') {
    // TODO: Implement notification system
    // This could be email, SMS, or in-app notification
    return true;
}

/**
 * Validate NIK format
 */
function validate_nik($nik) {
    return preg_match('/^[0-9]{16}$/', $nik);
}

/**
 * Clean and validate input
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate breadcrumb navigation
 */
function generate_breadcrumb($current_page) {
    $breadcrumbs = [
        'index.php' => 'Beranda',
        'dashboard_warga.php' => 'Dashboard Warga',
        'dashboard_admin.php' => 'Dashboard Admin',
        'login.php' => 'Login',
        'register.php' => 'Registrasi'
    ];
    
    return isset($breadcrumbs[$current_page]) ? $breadcrumbs[$current_page] : 'Halaman';
}

/**
 * Check if file upload is valid
 */
function validate_file_upload($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    return in_array($file_extension, $allowed_types);
}
?>
