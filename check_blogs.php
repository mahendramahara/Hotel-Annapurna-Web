<?php
require 'config/db.php';
$result = $conn->query("SELECT id, title, featured_image FROM blogs ORDER BY id DESC LIMIT 5");
echo "<h3>Recent Blogs:</h3>";
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . "<br>";
    echo "Title: " . $row['title'] . "<br>";
    echo "Image: " . ($row['featured_image'] ?? 'NULL') . "<br>";
    if ($row['featured_image']) {
        $path = $row['featured_image'];
        echo "Full path: " . __DIR__ . '/' . $path . "<br>";
        echo "File exists: " . (file_exists(__DIR__ . '/' . $path) ? 'YES' : 'NO') . "<br>";
    }
    echo "<hr>";
}
?>
