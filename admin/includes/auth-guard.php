<?php
/**
 * Admin Authorization Guard
 * Ensures only authenticated admin users can access admin section pages
 * Include this file at the top of every admin page that requires authentication
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Not logged in - redirect to login page
    header("Location: " . getLoginRedirectPath());
    exit();
}

// Check if admin role is set and is actually admin
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
    // Invalid role - destroy session and redirect to login
    session_unset();
    session_destroy();
    header("Location: " . getLoginRedirectPath());
    exit();
}

// Optional: Check session timeout (30 minutes of inactivity)
$timeout_duration = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session expired
    session_unset();
    session_destroy();
    header("Location: " . getLoginRedirectPath() . "?timeout=1");
    exit();
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

/**
 * Helper function to determine correct path to login page
 * Handles different directory levels in admin section
 */
function getLoginRedirectPath() {
    $current_path = $_SERVER['PHP_SELF'];
    
    // If we're in admin root (index.php, etc.)
    if (strpos($current_path, '/admin/') !== false && strpos($current_path, '/admin/sections/') === false && strpos($current_path, '/admin/blogs/') === false) {
        return 'login.php';
    }
    // If we're in sections/ or blogs/ subfolder
    else {
        return '../login.php';
    }
}

// Optional: Log admin access for security audit
if (function_exists('logActivity')) {
    $page = basename($_SERVER['PHP_SELF']);
    // Provide a fourth argument, e.g., current timestamp or additional info
    logActivity($_SESSION['admin_id'], 'admin_access', "Accessed admin page: {$page}", time());
}
?>
