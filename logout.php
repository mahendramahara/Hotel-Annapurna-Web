<?php
session_start();
require_once 'config/db.php';
require_once 'includes/activity-logger.php';

// Log logout before destroying session
if(isset($_SESSION['user_id'])) {
    logActivity($conn, $_SESSION['user_id'], 'logout', 'logged out from the system');
}

$_SESSION = array();

session_destroy();

if (isset($_COOKIE['user_auth'])) {
    setcookie('user_auth', '', time() - 3600, '/');
}

header("Location: index.php");
exit();
