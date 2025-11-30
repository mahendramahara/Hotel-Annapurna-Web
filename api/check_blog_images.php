<?php
require_once('../config/db.php');

$stmt = $conn->query("SELECT id, title, featured_image FROM blogs ORDER BY id DESC LIMIT 5");
echo "Recent blogs and their image paths:\n\n";
while($row = $stmt->fetch_assoc()) {
    echo "ID: " . $row['id'] . "\n";
    echo "Title: " . $row['title'] . "\n";
    echo "Image Path: " . ($row['featured_image'] ?? 'NULL') . "\n";
    if ($row['featured_image']) {
        $fullPath = __DIR__ . '/../' . $row['featured_image'];
        echo "Full Path: " . $fullPath . "\n";
        echo "File Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    }
    echo "\n";
}
?>
