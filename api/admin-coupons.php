<?php
session_start();
header('Content-Type: application/json');

// Admin authorization check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['admin_role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin login required.']);
    exit;
}

require_once '../config/db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'getAll') {
    $sql = "SELECT * FROM coupons ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $coupons = [];
    while ($row = $result->fetch_assoc()) {
        $coupons[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'coupons' => $coupons
    ]);
    exit;
}

if ($action === 'get') {
    $id = intval($_GET['id'] ?? 0);
    
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => true,
            'coupon' => $result->fetch_assoc()
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Coupon not found'
        ]);
    }
    exit;
}

if ($action === 'create') {
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $discount_type = $_POST['discount_type'] ?? 'percentage';
    $discount_value = floatval($_POST['discount_value'] ?? 0);
    $min_purchase = floatval($_POST['min_purchase'] ?? 0);
    $max_discount = !empty($_POST['max_discount']) ? floatval($_POST['max_discount']) : null;
    $valid_from = $_POST['valid_from'] ?? date('Y-m-d H:i:s');
    $valid_until = $_POST['valid_until'] ?? date('Y-m-d H:i:s', strtotime('+30 days'));
    $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : null;
    $status = $_POST['status'] ?? 'active';
    
    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Coupon code is required']);
        exit;
    }
    
    if ($discount_value <= 0) {
        echo json_encode(['success' => false, 'message' => 'Discount value must be greater than 0']);
        exit;
    }
    
    if ($discount_type === 'percentage' && $discount_value > 100) {
        echo json_encode(['success' => false, 'message' => 'Percentage discount cannot exceed 100%']);
        exit;
    }
    
    $check_stmt = $conn->prepare("SELECT id FROM coupons WHERE code = ?");
    $check_stmt->bind_param("s", $code);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Coupon code already exists']);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO coupons (code, discount_type, discount_value, min_purchase, max_discount, valid_from, valid_until, usage_limit, used_count, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?)");
    $stmt->bind_param("ssdddssss", $code, $discount_type, $discount_value, $min_purchase, $max_discount, $valid_from, $valid_until, $usage_limit, $status);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Coupon created successfully',
            'coupon_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create coupon: ' . $stmt->error
        ]);
    }
    exit;
}

if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $discount_type = $_POST['discount_type'] ?? 'percentage';
    $discount_value = floatval($_POST['discount_value'] ?? 0);
    $min_purchase = floatval($_POST['min_purchase'] ?? 0);
    $max_discount = !empty($_POST['max_discount']) ? floatval($_POST['max_discount']) : null;
    $valid_from = $_POST['valid_from'] ?? date('Y-m-d H:i:s');
    $valid_until = $_POST['valid_until'] ?? date('Y-m-d H:i:s', strtotime('+30 days'));
    $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : null;
    $status = $_POST['status'] ?? 'active';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid coupon ID']);
        exit;
    }
    
    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Coupon code is required']);
        exit;
    }
    
    if ($discount_value <= 0) {
        echo json_encode(['success' => false, 'message' => 'Discount value must be greater than 0']);
        exit;
    }
    
    $check_stmt = $conn->prepare("SELECT id FROM coupons WHERE code = ? AND id != ?");
    $check_stmt->bind_param("si", $code, $id);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Coupon code already exists']);
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE coupons SET code = ?, discount_type = ?, discount_value = ?, min_purchase = ?, max_discount = ?, valid_from = ?, valid_until = ?, usage_limit = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssdddssssi", $code, $discount_type, $discount_value, $min_purchase, $max_discount, $valid_from, $valid_until, $usage_limit, $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Coupon updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update coupon: ' . $stmt->error
        ]);
    }
    exit;
}

if ($action === 'toggleStatus') {
    $id = intval($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    
    $stmt = $conn->prepare("UPDATE coupons SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update status'
        ]);
    }
    exit;
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Coupon deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete coupon'
        ]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
$conn->close();
?>
