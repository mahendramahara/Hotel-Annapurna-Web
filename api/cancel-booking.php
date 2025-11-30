<?php
session_start();
require_once('../config/db.php');
require_once('../includes/activity-logger.php');

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
$user_id = $_SESSION['user_id'];

if ($booking_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit();
}

$stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    // Log activity
    logActivity($conn, $user_id, 'other', "cancelled order #$booking_id");
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking cancelled successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Booking not found or already cancelled'
    ]);
}

$stmt->close();
$conn->close();
