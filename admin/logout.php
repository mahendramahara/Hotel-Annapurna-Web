<?php
session_start();
require_once('../config/db.php');
require_once('../includes/activity-logger.php');

// Log logout before destroying session
if(isset($_SESSION['admin_id'])) {
    logActivity($conn, $_SESSION['admin_id'], 'logout', 'logged out from admin panel');
}

session_destroy();
header("Location: login.php");
exit();
?>
