<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();

ob_start();
require_once __DIR__ . '/../config/db.php';
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

// Test response
echo json_encode([
    'success' => true,
    'message' => 'Test response working',
    'session_check' => isset($_SESSION['admin_logged_in']) ? 'logged in' : 'not logged in',
    'db_check' => isset($conn) ? 'connected' : 'not connected',
    'post_data' => $_POST,
    'files_data' => isset($_FILES) ? array_keys($_FILES) : []
]);
