<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "Session check:\n";
echo "admin_logged_in: " . (isset($_SESSION['admin_logged_in']) ? 'true' : 'false') . "\n";
echo "admin_id: " . ($_SESSION['admin_id'] ?? 'not set') . "\n";

require_once __DIR__ . '/../config/db.php';

echo "\nDatabase connection: " . ($conn ? 'connected' : 'failed') . "\n";

if (isset($_POST['action'])) {
    echo "\nPOST data received:\n";
    echo "action: " . $_POST['action'] . "\n";
    echo "title: " . ($_POST['title'] ?? 'not set') . "\n";
    echo "category: " . ($_POST['category'] ?? 'not set') . "\n";
    echo "status: " . ($_POST['status'] ?? 'not set') . "\n";
    echo "content length: " . strlen($_POST['content'] ?? '') . "\n";
    echo "tags: " . ($_POST['tags'] ?? 'not set') . "\n";
    
    if (isset($_FILES['featured_image'])) {
        echo "Image file: " . $_FILES['featured_image']['name'] . "\n";
        echo "Image error: " . $_FILES['featured_image']['error'] . "\n";
    }
    
    // Try the actual insert
    if ($_POST['action'] === 'add') {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = trim($_POST['status'] ?? 'draft');
        $author_id = $_SESSION['admin_id'] ?? null;
        
        echo "\nTrying to insert with:\n";
        echo "title: $title\n";
        echo "category: $category\n";
        echo "tags: $tags\n";
        echo "status: $status\n";
        echo "author_id: $author_id\n";
        
        $stmt = $conn->prepare("INSERT INTO blogs (title, category, tags, content, featured_image, author_id, status) VALUES (?, ?, ?, ?, NULL, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssss", $title, $category, $tags, $content, $author_id, $status);
            if ($stmt->execute()) {
                echo "\nSUCCESS! Blog inserted with ID: " . $stmt->insert_id . "\n";
            } else {
                echo "\nERROR executing: " . $stmt->error . "\n";
            }
        } else {
            echo "\nERROR preparing: " . $conn->error . "\n";
        }
    }
} else {
    echo "\nNo POST action received\n";
}
?>
