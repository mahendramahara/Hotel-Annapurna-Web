<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
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

    // Validate room/table availability BEFORE creating booking
    if ($item_type === 'room') {
        $check_stmt = $conn->prepare("SELECT id, status FROM rooms WHERE id = ?");
        $check_stmt->bind_param("i", $item_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception('Room not found');
        }
        
        $room = $check_result->fetch_assoc();
        if ($room['status'] !== 'available') {
            throw new Exception('This room is not available for booking. Current status: ' . ucfirst($room['status']));
        }
        
        $check_stmt->close();
    } else {
        $check_stmt = $conn->prepare("SELECT id, booking_status FROM tables WHERE id = ?");
        $check_stmt->bind_param("i", $item_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception('Table not found');
        }
        
        $table = $check_result->fetch_assoc();
        if ($table['booking_status'] !== 'available') {
            throw new Exception('This table is not available for booking. Current status: ' . ucfirst($table['booking_status']));
        }
        
        $check_stmt->close();
    }

    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $duration = max(1, $check_in_date->diff($check_out_date)->days);
    
    $total_price = $price * $duration;

    $booking_ref = strtoupper(substr($item_type, 0, 1)) . date('Ymd') . str_pad($user_id, 4, '0', STR_PAD_LEFT) . rand(1000, 9999);

    if ($item_type === 'room') {
        $item_name = $decoded_data['room_type'] . ' - Room ' . $decoded_data['room_no'];
    } else {
        $item_name = $decoded_data['location'] . ' - Table ' . $decoded_data['table_no'];
    }

    $conn->begin_transaction();

    $notes = json_encode([
        'check_in' => $check_in,
        'check_out' => $check_out,
        'guests' => $guests,
        'requests' => $special_requests
    ]);

    $stmt = $conn->prepare("
        INSERT INTO orders (
            user_id, order_type, item_id, item_name,
            quantity, price, payment_method, payment_status,
            booking_reference, status, notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $payment_method = 'pending';
    $payment_status = 'pending';
    $status = 'pending';

    $stmt->bind_param(
        "isissdsssss",
        $user_id, $item_type, $item_id, $item_name,
        $duration, $total_price, $payment_method, $payment_status, 
        $booking_ref, $status, $notes
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
        'total_amount' => $total_price
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
