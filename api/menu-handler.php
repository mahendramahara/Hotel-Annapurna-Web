<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Check for admin authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access - Please login as admin']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'get_all') {
    $category = $_GET['category'] ?? '';
    
    if ($category) {
        $stmt = $conn->prepare("SELECT * FROM food_items WHERE category = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $category);
    } else {
        $stmt = $conn->prepare("SELECT * FROM food_items ORDER BY category, created_at DESC");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];
    
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $items]);
    exit();
}

if ($action === 'get_by_id') {
    $id = intval($_GET['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT * FROM food_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit();
    }
    
    echo json_encode(['success' => true, 'data' => $result->fetch_assoc()]);
    exit();
}

if ($action === 'add') {
    $category = $_POST['category'] ?? '';
    $food_name = trim($_POST['food_name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $discount_price = isset($_POST['discount_price']) && $_POST['discount_price'] !== '' ? floatval($_POST['discount_price']) : null;
    $available_days = $_POST['available_days'] ?? 'All Days';
    $short_description = trim($_POST['short_description'] ?? '');
    
    if (!$category || !$food_name || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/menu/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('menu_') . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_path = 'images/menu/' . $filename;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO food_items (category, food_name, price, discount_price, image_path, available_days, short_description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddsss", $category, $food_name, $price, $discount_price, $image_path, $available_days, $short_description);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Menu item added successfully', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add menu item']);
    }
    exit();
}

if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $category = $_POST['category'] ?? '';
    $food_name = trim($_POST['food_name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $discount_price = isset($_POST['discount_price']) && $_POST['discount_price'] !== '' ? floatval($_POST['discount_price']) : null;
    $available_days = $_POST['available_days'] ?? 'All Days';
    $short_description = trim($_POST['short_description'] ?? '');
    
    if (!$id || !$category || !$food_name || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT image_path FROM food_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit();
    }
    
    $current_item = $result->fetch_assoc();
    $image_path = $current_item['image_path'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/menu/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        if ($image_path && file_exists(__DIR__ . '/../' . $image_path)) {
            unlink(__DIR__ . '/../' . $image_path);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('menu_') . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_path = 'images/menu/' . $filename;
        }
    }
    
    $stmt = $conn->prepare("UPDATE food_items SET category = ?, food_name = ?, price = ?, discount_price = ?, image_path = ?, available_days = ?, short_description = ? WHERE id = ?");
    $stmt->bind_param("ssddsssi", $category, $food_name, $price, $discount_price, $image_path, $available_days, $short_description, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Menu item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update menu item']);
    }
    exit();
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT image_path FROM food_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit();
    }
    
    $item = $result->fetch_assoc();
    
    $stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($item['image_path'] && file_exists(__DIR__ . '/../' . $item['image_path'])) {
            unlink(__DIR__ . '/../' . $item['image_path']);
        }
        echo json_encode(['success' => true, 'message' => 'Menu item deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete menu item']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
