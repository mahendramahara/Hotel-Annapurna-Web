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
    $stmt = $conn->prepare("SELECT * FROM rooms ORDER BY room_no ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $rooms = [];
    
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $rooms]);
    exit();
}

if ($action === 'get_by_id') {
    $id = intval($_GET['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Room not found']);
        exit();
    }
    
    echo json_encode(['success' => true, 'data' => $result->fetch_assoc()]);
    exit();
}

if ($action === 'add') {
    $room_no = trim($_POST['room_no'] ?? '');
    $room_type = $_POST['room_type'] ?? 'single';
    $total_beds = intval($_POST['total_beds'] ?? 1);
    $bed_size = $_POST['bed_size'] ?? 'double';
    $status = $_POST['status'] ?? 'available';
    $price = floatval($_POST['price'] ?? 0);
    $price_today = isset($_POST['price_today']) && $_POST['price_today'] !== '' ? floatval($_POST['price_today']) : null;
    $amenities = trim($_POST['amenities'] ?? '');
    $short_description = trim($_POST['short_description'] ?? '');
    
    if (!$room_no || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT id FROM rooms WHERE room_no = ?");
    $stmt->bind_param("s", $room_no);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Room number already exists']);
        exit();
    }
    
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/rooms/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('room_') . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_path = 'images/rooms/' . $filename;
        }
    }
    
    // Use default demo image if no image uploaded
    if ($image_path === null) {
        $image_path = 'images/rooms/demoRoom.jpg';
    }
    
    $stmt = $conn->prepare("INSERT INTO rooms (image_path, room_no, room_type, total_beds, bed_size, status, price, price_today, amenities, short_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssddss", $image_path, $room_no, $room_type, $total_beds, $bed_size, $status, $price, $price_today, $amenities, $short_description);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Room added successfully', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add room']);
    }
    exit();
}

if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $room_no = trim($_POST['room_no'] ?? '');
    $room_type = $_POST['room_type'] ?? 'single';
    $total_beds = intval($_POST['total_beds'] ?? 1);
    $bed_size = $_POST['bed_size'] ?? 'double';
    $status = $_POST['status'] ?? 'available';
    $price = floatval($_POST['price'] ?? 0);
    $price_today = isset($_POST['price_today']) && $_POST['price_today'] !== '' ? floatval($_POST['price_today']) : null;
    $amenities = trim($_POST['amenities'] ?? '');
    $short_description = trim($_POST['short_description'] ?? '');
    
    if (!$id || !$room_no || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT image_path FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Room not found']);
        exit();
    }
    
    $current_room = $result->fetch_assoc();
    $image_path = $current_room['image_path'];
    
    $stmt = $conn->prepare("SELECT id FROM rooms WHERE room_no = ? AND id != ?");
    $stmt->bind_param("si", $room_no, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Room number already exists']);
        exit();
    }
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/rooms/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Only delete old image if it's not a demo image
        if ($image_path && file_exists(__DIR__ . '/../' . $image_path) && 
            strpos($image_path, 'demo') === false) {
            unlink(__DIR__ . '/../' . $image_path);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('room_') . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_path = 'images/rooms/' . $filename;
        }
    }
    
    // Use default demo image if still NULL or empty
    if (!$image_path || empty($image_path)) {
        $image_path = 'images/rooms/demoRoom.jpg';
    }
    
    $stmt = $conn->prepare("UPDATE rooms SET image_path = ?, room_no = ?, room_type = ?, total_beds = ?, bed_size = ?, status = ?, price = ?, price_today = ?, amenities = ?, short_description = ? WHERE id = ?");
    $stmt->bind_param("ssisssddssi", $image_path, $room_no, $room_type, $total_beds, $bed_size, $status, $price, $price_today, $amenities, $short_description, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Room updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update room']);
    }
    exit();
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT image_path FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Room not found']);
        exit();
    }
    
    $room = $result->fetch_assoc();
    
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($room['image_path'] && file_exists(__DIR__ . '/../' . $room['image_path'])) {
            unlink(__DIR__ . '/../' . $room['image_path']);
        }
        echo json_encode(['success' => true, 'message' => 'Room deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete room']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
