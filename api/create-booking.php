<?php
session_start();
require_once('../config/db.php');
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'message' => 'Authentication required. Please login to continue.',
        'require_login' => true
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    
    $item_type = trim($_POST['item_type'] ?? '');
    $item_id = intval($_POST['item_id'] ?? 0);
    $item_data = $_POST['item_data'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    
    $check_in = $_POST['check_in'] ?? date('Y-m-d');
    $check_out = $_POST['check_out'] ?? date('Y-m-d', strtotime('+1 day'));
    $guests = intval($_POST['guests'] ?? 1);
    $special_requests = trim($_POST['special_requests'] ?? '');


    if (empty($item_type) || $item_id <= 0 || $price <= 0) {
        throw new Exception('Missing required booking information');
    }

    if (!in_array($item_type, ['room', 'table'])) {
        throw new Exception('Invalid booking type');
    }

    $decoded_data = json_decode($item_data, true);
    if (!$decoded_data) {
        throw new Exception('Invalid item data format');
    }

    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $duration = max(1, $check_in_date->diff($check_out_date)->days);
    
    $subtotal = $price * $duration;
    $tax_rate = 0.13;
    $tax_amount = round($subtotal * $tax_rate, 2);
    $total_amount = round($subtotal + $tax_amount, 2);

    $booking_ref = strtoupper(substr($item_type, 0, 1)) . date('Ymd') . str_pad($user_id, 4, '0', STR_PAD_LEFT) . rand(1000, 9999);


    if ($item_type === 'room') {
        $item_name = $decoded_data['room_type'] . ' - Room ' . $decoded_data['room_no'];
    } else {
        $item_name = $decoded_data['location'] . ' - Table ' . $decoded_data['table_no'];
    }

    $conn->begin_transaction();


    $stmt = $conn->prepare("
        INSERT INTO orders (
            user_id, order_type, item_id, item_name, 
            price, quantity, subtotal, tax_amount, total_amount,
            booking_reference, check_in_date, check_out_date, guests,
            status, payment_status, notes, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid', ?, NOW())
    ");
    
    $stmt->bind_param(
        "isisdiiddssiis",
        $user_id, $item_type, $item_id, $item_name,
        $price, $duration, $subtotal, $tax_amount, $total_amount,
        $booking_ref, $check_in, $check_out, $guests, $special_requests
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to create booking');
    }

    $booking_id = $conn->insert_id;

    if ($item_type === 'room') {
        $update_stmt = $conn->prepare("UPDATE rooms SET status = 'reserved' WHERE id = ?");
    } else {
        $update_stmt = $conn->prepare("UPDATE tables SET booking_status = 'reserved' WHERE id = ?");
    }
    $update_stmt->bind_param("i", $item_id);
    $update_stmt->execute();

    $conn->commit();


    echo json_encode([
        'success' => true,
        'message' => 'Booking created successfully!',
        'booking_id' => $booking_id,
        'booking_reference' => $booking_ref,
        'total_amount' => $total_amount
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    
    error_log('Booking Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($stmt)) $stmt->close();
if (isset($conn)) $conn->close();
