<?php
// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/blog_errors.log');

session_start();

// Admin authorization check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['admin_role'] !== 'admin') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin login required.']);
    exit();
}

// Start output buffering to capture any unwanted output
ob_start();

try {
    require_once __DIR__ . '/../config/db.php';
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Database connection error: ' . $e->getMessage()]);
    exit();
}

// Clear any output from included files
ob_end_clean();

// Set header
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Handle JSON input for delete action
$json_input = file_get_contents('php://input');
if (!empty($json_input)) {
    $json_data = json_decode($json_input, true);
    if ($json_data) {
        $_POST = array_merge($_POST, $json_data);
    }
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'get_all') {
    $stmt = $conn->prepare("SELECT * FROM blogs ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $blogs = [];
    
    while ($row = $result->fetch_assoc()) {
        $stmt_stats = $conn->prepare("SELECT 
            SUM(CASE WHEN interaction_type = 'like' THEN 1 ELSE 0 END) as likes,
            SUM(CASE WHEN interaction_type = 'comment' THEN 1 ELSE 0 END) as comments,
            SUM(CASE WHEN interaction_type = 'share' THEN 1 ELSE 0 END) as shares
            FROM blog_interactions WHERE blog_id = ?");
        $stmt_stats->bind_param("i", $row['id']);
        $stmt_stats->execute();
        $stats = $stmt_stats->get_result()->fetch_assoc();
        
        $row['stats'] = $stats;
        $blogs[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $blogs]);
    exit();
}

if ($action === 'get_by_id') {
    $id = intval($_GET['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Blog not found']);
        exit();
    }
    
    echo json_encode(['success' => true, 'data' => $result->fetch_assoc()]);
    exit();
}

if ($action === 'add') {
    try {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $content = $_POST['content'] ?? ''; // Don't trim HTML content
        $status = trim($_POST['status'] ?? 'draft');
        $author_id = $_SESSION['admin_id'] ?? null;
        
        // Validate required fields
        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Title is required']);
            exit();
        }
        
        if (empty($category)) {
            echo json_encode(['success' => false, 'message' => 'Category is required']);
            exit();
        }
        
        if (empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Content is required']);
            exit();
        }
        
        // Handle image upload
        $featured_image = null;
        
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../images/blogs/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('blog_') . '.' . $ext;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $filepath)) {
                $featured_image = 'images/blogs/' . $filename;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit();
            }
        }
        
        // Use default demo image if no image uploaded
        if ($featured_image === null) {
            $featured_image = 'images/blogs/demoBlog.jpg';
        }
        
        // Prepare and execute insert query
        if ($author_id === null) {
            $stmt = $conn->prepare("INSERT INTO blogs (title, category, tags, content, featured_image, status) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }
            $stmt->bind_param("ssssss", $title, $category, $tags, $content, $featured_image, $status);
        } else {
            $stmt = $conn->prepare("INSERT INTO blogs (title, category, tags, content, featured_image, author_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }
            $stmt->bind_param("sssssis", $title, $category, $tags, $content, $featured_image, $author_id, $status);
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Blog added successfully', 
            'id' => $stmt->insert_id
        ]);
        exit();
        
    } catch (Exception $e) {
        error_log('Blog add error: ' . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error adding blog: ' . $e->getMessage()
        ]);
        exit();
    }
}

if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = trim($_POST['status'] ?? 'draft');
    
    if (!$id || !$title || !$category || !$content) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT featured_image FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Blog not found']);
        exit();
    }
    
    $current_blog = $result->fetch_assoc();
    $featured_image = $current_blog['featured_image'];
    
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/blogs/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Only delete old image if it's not a demo image
        if ($featured_image && file_exists(__DIR__ . '/../' . $featured_image) && 
            strpos($featured_image, 'demo') === false) {
            unlink(__DIR__ . '/../' . $featured_image);
        }
        
        $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('blog_') . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $filepath)) {
            $featured_image = 'images/blogs/' . $filename;
        }
    }
    
    // Use default demo image if still NULL or empty
    if (!$featured_image || empty($featured_image)) {
        $featured_image = 'images/blogs/demoBlog.jpg';
    }
    
    $stmt = $conn->prepare("UPDATE blogs SET title = ?, category = ?, tags = ?, content = ?, featured_image = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $title, $category, $tags, $content, $featured_image, $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Blog updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update blog']);
    }
    exit();
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT featured_image FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Blog not found']);
        exit();
    }
    
    $blog = $result->fetch_assoc();
    
    $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($blog['featured_image'] && file_exists(__DIR__ . '/../' . $blog['featured_image'])) {
            unlink(__DIR__ . '/../' . $blog['featured_image']);
        }
        echo json_encode(['success' => true, 'message' => 'Blog deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete blog']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
exit();
