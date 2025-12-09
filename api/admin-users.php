<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Admin authorization check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['admin_role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin login required.']);
    exit();
}

ob_start();
require_once __DIR__ . '/../config/db.php';
ob_end_clean();

header('Content-Type: application/json');

// Handle JSON input
$json_input = file_get_contents('php://input');
if ($json_input) {
    $json_data = json_decode($json_input, true);
    if ($json_data) {
        $_POST = array_merge($_POST, $json_data);
    }
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access - Please login as admin']);
    exit();
}

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'get_all_users') {
    $role = $_GET['role'] ?? '';
    $status = $_GET['status'] ?? '';
    
    $query = "SELECT id, first_name, last_name, email, contact, role, status, created_at FROM users WHERE 1=1";
    $params = [];
    $types = "";
    
    if ($role) {
        $query .= " AND role = ?";
        $params[] = $role;
        $types .= "s";
    }
    
    if ($status) {
        $query .= " AND status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($query);
    
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $users]);
    exit();
}

if ($action === 'update_user_status') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    
    if (!$user_id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $valid_statuses = ['pending', 'verified', 'suspended'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
    }
    exit();
}

if ($action === 'update_user_role') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $role = $_POST['role'] ?? '';
    
    if (!$user_id || !$role) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $valid_roles = ['customer', 'staff', 'admin'];
    if (!in_array($role, $valid_roles)) {
        echo json_encode(['success' => false, 'message' => 'Invalid role']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User role updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user role']);
    }
    exit();
}

if ($action === 'delete_user') {
    $user_id = intval($_POST['user_id'] ?? 0);
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        exit();
    }
    
    if ($user_id === $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
    exit();
}

if ($action === 'get_user_details') {
    $user_id = intval($_GET['user_id'] ?? 0);
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, contact, profile_pic, address, role, status, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    $stmt_orders = $conn->prepare("SELECT COUNT(*) as total_orders, SUM(price * quantity) as total_spent FROM orders WHERE user_id = ? AND status != 'cancelled'");
    $stmt_orders->bind_param("i", $user_id);
    $stmt_orders->execute();
    $order_stats = $stmt_orders->get_result()->fetch_assoc();
    
    $user['order_stats'] = $order_stats;
    
    echo json_encode(['success' => true, 'data' => $user]);
    exit();
}

if ($action === 'get') {
    $id = intval($_GET['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count FROM users u WHERE u.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    $user = $result->fetch_assoc();
    echo json_encode(['success' => true, 'user' => $user]);
    exit();
}

if ($action === 'create') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'customer';
    $status = $_POST['status'] ?? 'verified';
    $salary = isset($_POST['salary']) && $_POST['salary'] !== '' ? floatval($_POST['salary']) : null;
    
    if (!$first_name || !$last_name || !$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit();
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $profile_pic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('profile_') . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $filepath)) {
            $profile_pic = 'images/profiles/' . $filename;
        }
    }
    
    // Use default demo image if no image uploaded
    if ($profile_pic === null) {
        if ($role === 'staff') {
            $profile_pic = 'images/profiles/demoStaff.jpg';
        } elseif ($role === 'admin') {
            $profile_pic = 'images/profiles/demoAdmin.jpg';
        } else {
            $profile_pic = 'images/profiles/demoUser.jpg';
        }
    }
    
    if ($salary === null) {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, contact, address, password, profile_pic, role, status, salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)");
        $stmt->bind_param("sssssssss", $first_name, $last_name, $email, $contact, $address, $hashed_password, $profile_pic, $role, $status);
    } else {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, contact, address, password, profile_pic, role, status, salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssd", $first_name, $last_name, $email, $contact, $address, $hashed_password, $profile_pic, $role, $status, $salary);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User created successfully', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create user']);
    }
    exit();
}

if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $status = $_POST['status'] ?? 'verified';
    $salary = isset($_POST['salary']) && $_POST['salary'] !== '' ? floatval($_POST['salary']) : null;
    
    if (!$id || !$first_name || !$last_name || !$email) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    $current_user = $result->fetch_assoc();
    $profile_pic = $current_user['profile_pic'];
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit();
    }
    
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Only delete old image if it's not a demo image
        if ($profile_pic && file_exists(__DIR__ . '/../' . $profile_pic) && 
            strpos($profile_pic, 'demo') === false) {
            unlink(__DIR__ . '/../' . $profile_pic);
        }
        
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('profile_') . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $filepath)) {
            $profile_pic = 'images/profiles/' . $filename;
        }
    }
    
    // If still NULL or empty, set default demo image based on role
    if (!$profile_pic || empty($profile_pic)) {
        // Get user's role to determine which demo image to use
        $stmt_role = $conn->prepare("SELECT role FROM users WHERE id = ?");
        $stmt_role->bind_param("i", $id);
        $stmt_role->execute();
        $role_result = $stmt_role->get_result()->fetch_assoc();
        $user_role = $role_result['role'] ?? 'customer';
        
        if ($user_role === 'staff') {
            $profile_pic = 'images/profiles/demoStaff.jpg';
        } elseif ($user_role === 'admin') {
            $profile_pic = 'images/profiles/demoAdmin.jpg';
        } else {
            $profile_pic = 'images/profiles/demoUser.jpg';
        }
    }
    
    if ($salary === null) {
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, contact = ?, address = ?, profile_pic = ?, status = ?, salary = NULL WHERE id = ?");
        $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $contact, $address, $profile_pic, $status, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, contact = ?, address = ?, profile_pic = ?, status = ?, salary = ? WHERE id = ?");
        $stmt->bind_param("sssssssdi", $first_name, $last_name, $email, $contact, $address, $profile_pic, $status, $salary, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user']);
    }
    exit();
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    if ($id === $_SESSION['admin_id']) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($user['profile_pic'] && file_exists(__DIR__ . '/../' . $user['profile_pic'])) {
            unlink(__DIR__ . '/../' . $user['profile_pic']);
        }
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>

