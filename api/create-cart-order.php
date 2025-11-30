<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';
require_once '../includes/activity-logger.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$cart = $data['cart'] ?? [];
$subtotal = floatval($data['subtotal'] ?? 0);
$discount = floatval($data['discount'] ?? 0);
$total = floatval($data['total'] ?? 0);
$coupon = $data['coupon'] ?? null;
$payment_method = $data['payment_method'] ?? 'cash';
$payment_status = $data['payment_status'] ?? 'pending';

$conn->begin_transaction();

try {
    $booking_reference = 'ORD' . date('Ymd') . $user_id . rand(1000, 9999);
    $order_ids = [];
    
    $foods = $cart['foods'] ?? [];
    $rooms = $cart['rooms'] ?? [];
    $tables = $cart['tables'] ?? [];
    
    foreach ($foods as $food) {
        $item_id = intval($food['id']);
        $quantity = intval($food['quantity'] ?? 1);
        $price = floatval($food['discount_price'] ?? $food['price'] ?? 0);
        $item_total = $price * $quantity;
        $order_type = 'food';
        $item_name = $food['food_name'] ?? $food['name'] ?? $food['item_name'] ?? 'Food Item';
        
        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, item_id, item_name, quantity, price, payment_method, payment_status, booking_reference, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("isiidssss", $user_id, $order_type, $item_id, $item_name, $quantity, $item_total, $payment_method, $payment_status, $booking_reference);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create food order: ' . $stmt->error);
        }
        $order_ids[] = $conn->insert_id;
    }
    
    foreach ($rooms as $room) {
        $item_id = intval($room['id']);
        $nights = intval($room['nights'] ?? 1);
        $price = floatval($room['price_today'] ?? $room['price'] ?? 0);
        $item_total = $price * $nights;
        $order_type = 'room';
        $item_name = $room['room_name'] ?? $room['name'] ?? $room['item_name'] ?? 'Room Booking';
        
        $check_in = $room['check_in'] ?? date('Y-m-d');
        $check_out = $room['check_out'] ?? date('Y-m-d', strtotime('+1 day'));
        
        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, item_id, item_name, quantity, price, payment_method, payment_status, booking_reference, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("isiidssss", $user_id, $order_type, $item_id, $item_name, $nights, $item_total, $payment_method, $payment_status, $booking_reference);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create room order: ' . $stmt->error);
        }
        $order_ids[] = $conn->insert_id;
    }
    
    foreach ($tables as $table) {
        $item_id = intval($table['id']);
        $quantity = intval($table['quantity'] ?? 1);
        $price = floatval($table['price_today'] ?? $table['price_main'] ?? 0);
        $item_total = $price * $quantity;
        $order_type = 'table';
        $item_name = $table['table_name'] ?? $table['name'] ?? $table['item_name'] ?? 'Table Booking';
        
        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, item_id, item_name, quantity, price, payment_method, payment_status, booking_reference, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("isiidssss", $user_id, $order_type, $item_id, $item_name, $quantity, $item_total, $payment_method, $payment_status, $booking_reference);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create table order: ' . $stmt->error);
        }
        $order_ids[] = $conn->insert_id;
    }
    
    if ($coupon) {
        $coupon_code = $coupon['code'];
        $stmt = $conn->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE code = ?");
        $stmt->bind_param("s", $coupon_code);
        $stmt->execute();
    }
    
    // Clear the user's cart after successful order
    $clear_stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $clear_stmt->bind_param("i", $user_id);
    $clear_stmt->execute();
    
    $conn->commit();
    
    // Log activity
    $total_items = count($foods) + count($rooms) + count($tables);
    logActivity($conn, $user_id, 'order', "placed an order with {$total_items} item(s) - Reference: {$booking_reference}");
    
    // Return the first order ID for eSewa payment reference
    $primary_order_id = !empty($order_ids) ? $order_ids[0] : 0;
    
    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'booking_reference' => $booking_reference,
        'order_id' => $primary_order_id,
        'order_ids' => $order_ids
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>