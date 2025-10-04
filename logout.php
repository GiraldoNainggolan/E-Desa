<?php
// Start session to access session variables
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Store user info for logging (optional)
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['role'] ?? 'unknown';
    
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
    
    // Redirect with success message
    header("Location: index.php?logout=success");
    exit();
} else {
    // If user is not logged in, redirect to home
    header("Location: index.php");
    exit();
}
?>
