<?php
require_once __DIR__ . '/../config/db.php';

echo "<h3>Blogs Table Structure:</h3>";
$result = mysqli_query($conn, 'DESCRIBE blogs');

if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . mysqli_error($conn);
}

// Check if status column exists
$result = mysqli_query($conn, "SHOW COLUMNS FROM blogs LIKE 'status'");
if (mysqli_num_rows($result) == 0) {
    echo "<h3 style='color:red;'>Status column does NOT exist!</h3>";
    echo "<p>Adding status column...</p>";
    
    $alter = "ALTER TABLE blogs ADD COLUMN status ENUM('draft','published','archived') DEFAULT 'draft' AFTER author_id";
    if (mysqli_query($conn, $alter)) {
        echo "<p style='color:green;'>✅ Status column added successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error adding status column: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<h3 style='color:green;'>Status column EXISTS!</h3>";
}
?>
