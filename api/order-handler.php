<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'create_order') {
    $order_type = $_POST['order_type'] ?? '';
    $item_id = intval($_POST['item_id'] ?? 0);
    $item_name = trim($_POST['item_name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    $delivery_date = $_POST['delivery_date'] ?? null;
    $notes = trim($_POST['notes'] ?? '');
    
    if (!$order_type || !$item_id || !$item_name || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, item_id, item_name, price, quantity, delivery_date, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iisidiss", $user_id, $order_type, $item_id, $item_name, $price, $quantity, $delivery_date, $notes);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Order created successfully', 'order_id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create order']);
    }
    exit();
}

if ($action === 'get_user_orders') {
    $stmt = $conn->prepare("SELECT o.*, 
        CASE 
            WHEN o.order_type = 'food' THEN f.image_url
            WHEN o.order_type = 'room' THEN r.image_url
            WHEN o.order_type = 'table' THEN t.image_url
        END as item_image
        FROM orders o
        LEFT JOIN food_items f ON o.order_type = 'food' AND o.item_id = f.id
        LEFT JOIN rooms r ON o.order_type = 'room' AND o.item_id = r.id
        LEFT JOIN tables t ON o.order_type = 'table' AND o.item_id = t.id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $orders]);
    exit();
}

if ($action === 'get_order_details') {
    $order_id = intval($_GET['order_id'] ?? 0);
    
    if (!$order_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit();
    }
    
    echo json_encode(['success' => true, 'data' => $result->fetch_assoc()]);
    exit();
}

if ($action === 'cancel_order') {
    $order_id = intval($_POST['order_id'] ?? 0);
    
    if (!$order_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT status FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit();
    }
    
    $order = $result->fetch_assoc();
    
    if ($order['status'] === 'completed' || $order['status'] === 'cancelled') {
        echo json_encode(['success' => false, 'message' => 'Cannot cancel this order']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel order']);
    }
    exit();
}

if ($action === 'checkout') {
    $items = json_decode($_POST['items'] ?? '[]', true);
    
    if (empty($items)) {
        echo json_encode(['success' => false, 'message' => 'No items to checkout']);
        exit();
    }
    
    $conn->begin_transaction();
    
    try {
        $order_ids = [];
        
        foreach ($items as $item) {
            $order_type = $item['type'] ?? '';
            $item_id = intval($item['id'] ?? 0);
            $item_name = $item['name'] ?? '';
            $price = floatval($item['price'] ?? 0);
            $quantity = intval($item['quantity'] ?? 1);
            $delivery_date = $item['delivery_date'] ?? null;
            
            $stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, item_id, item_name, price, quantity, delivery_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("iisidis", $user_id, $order_type, $item_id, $item_name, $price, $quantity, $delivery_date);
            $stmt->execute();
            
            $order_ids[] = $stmt->insert_id;
            
            if ($order_type === 'room') {
                $stmt_update = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
                $stmt_update->bind_param("i", $item_id);
                $stmt_update->execute();
            } elseif ($order_type === 'table') {
                $stmt_update = $conn->prepare("UPDATE tables SET booking_status = 'booked' WHERE id = ?");
                $stmt_update->bind_param("i", $item_id);
                $stmt_update->execute();
            }
        }
        
        $stmt_clear = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt_clear->bind_param("i", $user_id);
        $stmt_clear->execute();
        
        $conn->commit();
        
        echo json_encode(['success' => true, 'message' => 'Checkout completed successfully', 'order_ids' => $order_ids]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Checkout failed: ' . $e->getMessage()]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
