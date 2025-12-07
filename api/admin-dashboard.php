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

if ($action === 'get_dashboard_stats') {
    $stats = [];
    
    $stmt_users = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
    $stats['total_customers'] = $stmt_users->fetch_assoc()['total'];
    
    $stmt_orders = $conn->query("SELECT COUNT(*) as total, SUM(price * quantity) as revenue FROM orders WHERE status != 'cancelled'");
    $order_data = $stmt_orders->fetch_assoc();
    $stats['total_orders'] = $order_data['total'];
    $stats['total_revenue'] = $order_data['revenue'] ?? 0;
    
    $stmt_pending = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $stmt_pending->fetch_assoc()['total'];
    
    $stmt_rooms = $conn->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available,
        SUM(CASE WHEN status = 'booked' THEN 1 ELSE 0 END) as booked
        FROM rooms");
    $stats['rooms'] = $stmt_rooms->fetch_assoc();
    
    $stmt_tables = $conn->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN booking_status = 'available' THEN 1 ELSE 0 END) as available,
        SUM(CASE WHEN booking_status = 'booked' THEN 1 ELSE 0 END) as booked
        FROM tables");
    $stats['tables'] = $stmt_tables->fetch_assoc();
    
    $stmt_menu = $conn->query("SELECT COUNT(*) as total FROM food_items");
    $stats['total_menu_items'] = $stmt_menu->fetch_assoc()['total'];
    
    echo json_encode(['success' => true, 'data' => $stats]);
    exit();
}

if ($action === 'get_recent_orders') {
    $limit = intval($_GET['limit'] ?? 10);
    
    $stmt = $conn->prepare("SELECT o.*, u.first_name, u.last_name 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           ORDER BY o.order_date DESC 
                           LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $orders]);
    exit();
}

if ($action === 'get_revenue_chart') {
    $period = $_GET['period'] ?? 'week';
    
    $date_format = $period === 'month' ? '%Y-%m' : '%Y-%m-%d';
    $days_back = $period === 'month' ? 180 : 30;
    
    $stmt = $conn->prepare("SELECT 
        DATE_FORMAT(order_date, ?) as period,
        SUM(price * quantity) as revenue,
        COUNT(*) as order_count
        FROM orders 
        WHERE status != 'cancelled' 
        AND order_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY DATE_FORMAT(order_date, ?)
        ORDER BY period ASC");
    $stmt->bind_param("sis", $date_format, $days_back, $date_format);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $chart_data = [];
    while ($row = $result->fetch_assoc()) {
        $chart_data[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $chart_data]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
