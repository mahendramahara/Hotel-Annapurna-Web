<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/activity-logger.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$admin_id = $_SESSION['admin_id'];

if ($action === 'update_profile') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $errors = [];
        if (empty($first_name)) $errors[] = 'first name';
        if (empty($last_name)) $errors[] = 'last name';
        if (empty($email)) $errors[] = 'email';
        echo json_encode(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $errors)]);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $admin_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, contact = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $contact, $address, $admin_id);
    
    if ($stmt->execute()) {
        $_SESSION['admin_name'] = $first_name . ' ' . $last_name;
        logActivity($conn, $admin_id, 'update', 'updated profile information');
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }
    exit();
}

if ($action === 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    if (!$current_password || !$new_password) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if (!password_verify($current_password, $result['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit();
    }
    
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed, $admin_id);
    
    if ($stmt->execute()) {
        logActivity($conn, $admin_id, 'update', 'changed account password');
        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to change password']);
    }
    exit();
}

if ($action === 'upload_profile_image') {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No image uploaded']);
        exit();
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['image']['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image type. Only JPG, PNG, and GIF allowed']);
        exit();
    }
    
    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Image size too large. Maximum 5MB allowed']);
        exit();
    }
    
    $upload_dir = __DIR__ . '/../images/profiles/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $old_image = $stmt->get_result()->fetch_assoc()['profile_pic'];
    
    if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
        unlink(__DIR__ . '/../' . $old_image);
    }
    
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $filename = uniqid('profile_') . '.' . $ext;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
        $image_path = 'images/profiles/' . $filename;
        
        $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $image_path, $admin_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Profile image updated successfully', 'image_path' => $image_path]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
