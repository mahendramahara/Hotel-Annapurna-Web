<?php
session_start();
require_once 'config/db.php';
require_once 'includes/activity-logger.php';

// Log logout before destroying session
if(isset($_SESSION['user_id'])) {
    try {
        logActivity($conn, $_SESSION['user_id'], 'logout', 'logged out from the system');
    } catch (Exception $e) {
        error_log('Logout activity log failed: ' . $e->getMessage());
    }
}

$_SESSION = array();

session_destroy();

if (isset($_COOKIE['user_auth'])) {
    setcookie('user_auth', '', time() - 3600, '/');
}

header("Location: index.php");
exit();
