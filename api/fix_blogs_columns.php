<?php
require_once __DIR__ . '/../config/db.php';

echo "<h2>Fixing Blogs Table Structure</h2>";

// Get current table structure
$result = mysqli_query($conn, 'DESCRIBE blogs');
$existing_columns = [];

if ($result) {
    echo "<h3>Current Columns:</h3><ul>";
    while($row = mysqli_fetch_assoc($result)) {
        $existing_columns[] = $row['Field'];
        echo "<li>{$row['Field']} - {$row['Type']}</li>";
    }
    echo "</ul>";
}

echo "<h3>Checking and Adding Missing Columns:</h3>";

// Required columns according to the setup file
$required_columns = [
    'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
    'title' => 'VARCHAR(255) NOT NULL',
    'category' => 'VARCHAR(100) NOT NULL',
    'tags' => 'VARCHAR(255) DEFAULT NULL',
    'content' => 'TEXT NOT NULL',
    'featured_image' => 'VARCHAR(255) DEFAULT NULL',
    'author_id' => 'INT DEFAULT NULL',
    'status' => "ENUM('draft','published','archived') DEFAULT 'draft'",
    'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
];

$columns_added = 0;
$columns_existed = 0;

foreach ($required_columns as $column_name => $column_definition) {
    if (!in_array($column_name, $existing_columns)) {
        echo "<p style='color:orange;'>⚠ Column '$column_name' is missing. Adding...</p>";
        
        // Determine position for the column
        $position = '';
        if ($column_name === 'status') {
            $position = 'AFTER author_id';
        } elseif ($column_name === 'created_at') {
            $position = 'AFTER status';
        } elseif ($column_name === 'updated_at') {
            $position = 'AFTER created_at';
        }
        
        $alter_query = "ALTER TABLE blogs ADD COLUMN $column_name $column_definition $position";
        
        if (mysqli_query($conn, $alter_query)) {
            echo "<p style='color:green;'>✅ Added '$column_name' successfully!</p>";
            $columns_added++;
        } else {
            echo "<p style='color:red;'>❌ Error adding '$column_name': " . mysqli_error($conn) . "</p>";
        }
    } else {
        $columns_existed++;
    }
}

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "<p>✅ Columns already existed: $columns_existed</p>";
echo "<p>➕ Columns added: $columns_added</p>";

// Show final table structure
echo "<hr>";
echo "<h3>Final Blogs Table Structure:</h3>";
$result = mysqli_query($conn, 'DESCRIBE blogs');

if ($result) {
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr style='background:#1891d1;color:white;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<p style='color:green;font-size:18px;'><strong>✅ Blogs table is now ready!</strong></p>";
echo "<p><a href='../admin/index.php?page=blogs'>← Go to Blog Management</a></p>";
?>
