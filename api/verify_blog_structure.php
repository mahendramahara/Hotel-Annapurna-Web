<?php
require_once __DIR__ . '/../config/db.php';

echo "<h2>Blog Table Structure Verification</h2>";

// Get actual database structure
$result = mysqli_query($conn, 'DESCRIBE blogs');
$actual_columns = [];

echo "<h3>Current Database Structure:</h3>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
echo "<tr style='background:#1891d1;color:white;'><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";

while($row = mysqli_fetch_assoc($result)) {
    $actual_columns[$row['Field']] = [
        'Type' => $row['Type'],
        'Null' => $row['Null'],
        'Default' => $row['Default']
    ];
    echo "<tr>";
    echo "<td><strong>" . $row['Field'] . "</strong></td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Expected structure from setup_tables.php
$expected_columns = [
    'id' => ['Type' => 'int', 'Null' => 'NO'],
    'title' => ['Type' => 'varchar(255)', 'Null' => 'NO'],
    'category' => ['Type' => 'varchar(100)', 'Null' => 'NO'],
    'tags' => ['Type' => 'varchar(255)', 'Null' => 'YES'],
    'content' => ['Type' => 'text', 'Null' => 'NO'],
    'featured_image' => ['Type' => 'varchar(255)', 'Null' => 'YES'],
    'author_id' => ['Type' => 'int', 'Null' => 'YES'],
    'status' => ['Type' => "enum('draft','published','archived')", 'Null' => 'YES'],
    'created_at' => ['Type' => 'timestamp', 'Null' => 'YES'],
    'updated_at' => ['Type' => 'timestamp', 'Null' => 'YES']
];

echo "<hr>";
echo "<h3>Verification Results:</h3>";

$all_good = true;

foreach ($expected_columns as $col_name => $expected) {
    if (!isset($actual_columns[$col_name])) {
        echo "<p style='color:red;'>❌ Missing column: <strong>$col_name</strong></p>";
        $all_good = false;
    } else {
        $actual_type = strtolower($actual_columns[$col_name]['Type']);
        $expected_type = strtolower($expected['Type']);
        
        if (strpos($actual_type, $expected_type) === false && strpos($expected_type, $actual_type) === false) {
            echo "<p style='color:orange;'>⚠ Column <strong>$col_name</strong> type mismatch: Expected '$expected_type', Got '$actual_type'</p>";
        }
    }
}

// Check for extra columns
foreach ($actual_columns as $col_name => $data) {
    if (!isset($expected_columns[$col_name])) {
        echo "<p style='color:blue;'>ℹ Extra column found: <strong>$col_name</strong> (Not in setup_tables.php)</p>";
    }
}

if ($all_good) {
    echo "<h3 style='color:green;'>✅ All required columns are present and correctly structured!</h3>";
} else {
    echo "<h3 style='color:red;'>⚠ Some columns need attention!</h3>";
}

echo "<hr>";
echo "<h3>Setup File Structure (Reference):</h3>";
echo "<pre style='background:#f5f5f5;padding:15px;border:1px solid #ddd;'>";
echo "CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    tags VARCHAR(255) DEFAULT NULL,
    content TEXT NOT NULL,
    featured_image VARCHAR(255) DEFAULT NULL,
    author_id INT DEFAULT NULL,
    status ENUM('draft','published','archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
)";
echo "</pre>";

echo "<p><a href='../admin/index.php?page=blogs'>← Go to Blog Management</a></p>";
?>
