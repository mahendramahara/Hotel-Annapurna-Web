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

if ($action === 'get_all') {
    $stmt = $conn->prepare("SELECT * FROM tables ORDER BY table_no ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $tables = [];
    
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $tables]);
    exit();
}

if ($action === 'get_by_id') {
    $id = intval($_GET['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT * FROM tables WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Table not found']);
        exit();
    }
    
    echo json_encode(['success' => true, 'data' => $result->fetch_assoc()]);
    exit();
}

if ($action === 'add') {
    $table_no = trim($_POST['table_no'] ?? '');
    $total_chairs = intval($_POST['total_chairs'] ?? 2);
    $booking_status = $_POST['booking_status'] ?? 'available';
    $price_main = floatval($_POST['price_main'] ?? 0);
    $price_today = isset($_POST['price_today']) && $_POST['price_today'] !== '' ? floatval($_POST['price_today']) : null;
    $location = $_POST['location'] ?? 'ground floor';
    $short_description = trim($_POST['short_description'] ?? '');
    
    if (!$table_no || $price_main <= 0) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT id FROM tables WHERE table_no = ?");
    $stmt->bind_param("s", $table_no);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Table number already exists']);
        exit();
    }
    
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/tables/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('table_') . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_path = 'images/tables/' . $filename;
        }
    }
    
    // Use default demo image if no image uploaded
    if ($image_path === null) {
        $image_path = 'images/tables/demoTable.jpg';
    }
    
    $stmt = $conn->prepare("INSERT INTO tables (image_path, table_no, total_chairs, booking_status, price_main, price_today, location, short_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissdds", $image_path, $table_no, $total_chairs, $booking_status, $price_main, $price_today, $location, $short_description);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Table added successfully', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add table']);
    }
    exit();
}

if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $table_no = trim($_POST['table_no'] ?? '');
    $total_chairs = intval($_POST['total_chairs'] ?? 2);
    $booking_status = $_POST['booking_status'] ?? 'available';
    $price_main = floatval($_POST['price_main'] ?? 0);
    $price_today = isset($_POST['price_today']) && $_POST['price_today'] !== '' ? floatval($_POST['price_today']) : null;
    $location = $_POST['location'] ?? 'ground floor';
    $short_description = trim($_POST['short_description'] ?? '');
    
    if (!$id || !$table_no || $price_main <= 0) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT image_path FROM tables WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Table not found']);
        exit();
    }
    
    $current_table = $result->fetch_assoc();
    $image_path = $current_table['image_path'];
    
    $stmt = $conn->prepare("SELECT id FROM tables WHERE table_no = ? AND id != ?");
    $stmt->bind_param("si", $table_no, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Table number already exists']);
        exit();
    }
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/tables/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Only delete old image if it's not a demo image
        if ($image_path && file_exists(__DIR__ . '/../' . $image_path) && 
            strpos($image_path, 'demo') === false) {
            unlink(__DIR__ . '/../' . $image_path);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('table_') . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_path = 'images/tables/' . $filename;
        }
    }
    
    // Use default demo image if still NULL or empty
    if (!$image_path || empty($image_path)) {
        $image_path = 'images/tables/demoTable.jpg';
    }
    
    $stmt = $conn->prepare("UPDATE tables SET image_path = ?, table_no = ?, total_chairs = ?, booking_status = ?, price_main = ?, price_today = ?, location = ?, short_description = ? WHERE id = ?");
    $stmt->bind_param("ssissddsi", $image_path, $table_no, $total_chairs, $booking_status, $price_main, $price_today, $location, $short_description, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Table updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update table']);
    }
    exit();
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT image_path FROM tables WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Table not found']);
        exit();
    }
    
    $table = $result->fetch_assoc();
    
    $stmt = $conn->prepare("DELETE FROM tables WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($table['image_path'] && file_exists(__DIR__ . '/../' . $table['image_path'])) {
            unlink(__DIR__ . '/../' . $table['image_path']);
        }
        echo json_encode(['success' => true, 'message' => 'Table deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete table']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
