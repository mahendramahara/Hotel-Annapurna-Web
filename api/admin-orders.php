<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Admin authorization check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['admin_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin login required.']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'get_all_orders') {
    $filter_status = $_GET['status'] ?? '';
    $filter_type = $_GET['type'] ?? '';
    
    $query = "SELECT o.*, u.first_name, u.last_name, u.email, u.contact 
              FROM orders o 
              JOIN users u ON o.user_id = u.id 
              WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if ($filter_status) {
        $query .= " AND o.status = ?";
        $params[] = $filter_status;
        $types .= "s";
    }
    
    if ($filter_type) {
        $query .= " AND o.order_type = ?";
        $params[] = $filter_type;
        $types .= "s";
    }
    
    $query .= " ORDER BY o.created_at DESC";
    
    $stmt = $conn->prepare($query);
    
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $orders]);
    exit();
}

if ($action === 'update_order_status') {
    $order_id = intval($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    
    if (!$order_id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        if ($status === 'cancelled' || $status === 'completed') {
            $stmt_order = $conn->prepare("SELECT order_type, item_id FROM orders WHERE id = ?");
            $stmt_order->bind_param("i", $order_id);
            $stmt_order->execute();
            $order = $stmt_order->get_result()->fetch_assoc();
            
            if ($order['order_type'] === 'room') {
                $stmt_update = $conn->prepare("UPDATE rooms SET status = 'available' WHERE id = ?");
                $stmt_update->bind_param("i", $order['item_id']);
                $stmt_update->execute();
            } elseif ($order['order_type'] === 'table') {
                $stmt_update = $conn->prepare("UPDATE tables SET booking_status = 'available' WHERE id = ?");
                $stmt_update->bind_param("i", $order['item_id']);
                $stmt_update->execute();
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
    }
    exit();
}

if ($action === 'get_order_stats') {
    $stmt = $conn->prepare("SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_orders,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
        SUM(price * quantity) as total_revenue
        FROM orders");
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo json_encode(['success' => true, 'data' => $result->fetch_assoc()]);
    exit();
}

if ($action === 'get') {
    $order_id = intval($_GET['id'] ?? 0);
    
    if (!$order_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.email, u.contact FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        echo json_encode(['success' => true, 'order' => $order]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
    exit();
}

if ($action === 'update_status') {
    $order_id = intval($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    
    if (!$order_id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status value']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        // Update room/table availability if cancelled or completed
        if ($status === 'cancelled' || $status === 'completed') {
            $stmt_order = $conn->prepare("SELECT order_type, item_id FROM orders WHERE id = ?");
            $stmt_order->bind_param("i", $order_id);
            $stmt_order->execute();
            $order = $stmt_order->get_result()->fetch_assoc();
            
            if ($order['order_type'] === 'room') {
                $stmt_update = $conn->prepare("UPDATE rooms SET status = 'available' WHERE id = ?");
                $stmt_update->bind_param("i", $order['item_id']);
                $stmt_update->execute();
            } elseif ($order['order_type'] === 'table') {
                $stmt_update = $conn->prepare("UPDATE tables SET booking_status = 'available' WHERE id = ?");
                $stmt_update->bind_param("i", $order['item_id']);
                $stmt_update->execute();
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Order status updated to ' . $status]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status or no changes made']);
    }
    exit();
}

if ($action === 'delete') {
    $order_id = intval($_POST['id'] ?? 0);
    
    if (!$order_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
        exit();
    }
    
    // Get order details before deleting
    $stmt_check = $conn->prepare("SELECT order_type, item_id FROM orders WHERE id = ?");
    $stmt_check->bind_param("i", $order_id);
    $stmt_check->execute();
    $order = $stmt_check->get_result()->fetch_assoc();
    
    if ($order) {
        // Free up room/table if applicable
        if ($order['order_type'] === 'room') {
            $stmt_update = $conn->prepare("UPDATE rooms SET status = 'available' WHERE id = ?");
            $stmt_update->bind_param("i", $order['item_id']);
            $stmt_update->execute();
        } elseif ($order['order_type'] === 'table') {
            $stmt_update = $conn->prepare("UPDATE tables SET booking_status = 'available' WHERE id = ?");
            $stmt_update->bind_param("i", $order['item_id']);
            $stmt_update->execute();
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete order']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
