<?php
session_start();
require_once('config/db.php');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

$data = $_GET['data'] ?? '';
$booking_id = $_GET['booking_id'] ?? 0;
$order_id = $_GET['order_id'] ?? 0;

if (!empty($data)) {
    $decoded = base64_decode($data);
    $response = json_decode($decoded, true);
    $error_message = $response['status'] ?? 'Payment failed';
} else {
    $error_message = 'Payment was cancelled or failed';
}

// Check if this is a cart order or booking
if ($order_id > 0) {
    // Cart order failure - redirect to cart
    header("Location: cart.php?error=" . urlencode($error_message));
} else {
    // Booking failure - redirect to payment page
    header("Location: payment.php?booking_id={$booking_id}&error=" . urlencode($error_message));
}
exit();
?>
