<?php
session_start();
require_once('../config/db.php');
require_once('../includes/activity-logger.php');

if(isset($_SESSION['admin_id'])) {
    try {
        logActivity($conn, $_SESSION['admin_id'], 'logout', 'logged out from admin panel');
    } catch (Exception $e) {
        error_log('Admin logout activity log failed: ' . $e->getMessage());
    }
}

$_SESSION = array();
session_destroy();

if (isset($_COOKIE['admin_auth'])) {
    setcookie('admin_auth', '', time() - 3600, '/');
}

header("Location: login.php");
exit();
?>
