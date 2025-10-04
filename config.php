<?php
// Database Configuration
$host = "localhost";
$username = "root";
$password = "";
$dbname = "e_desa";

// Create connection with error handling
try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF-8
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    // Log error and show user-friendly message
    error_log("Database connection error: " . $e->getMessage());
    die("Maaf, terjadi kesalahan koneksi database. Silakan coba lagi nanti.");
}

// Database helper functions
function sanitize_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

function execute_query($sql, $params = null) {
    global $conn;
    
    if ($params) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param(...$params);
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        return $stmt;
    } else {
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception("Query failed: " . $conn->error);
        }
        return $result;
    }
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security settings
ini_set('display_errors', 0);
ini_set('log_errors', 1);
?>