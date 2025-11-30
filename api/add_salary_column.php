<?php
include(__DIR__ . '/../config/db.php');

echo "Adding salary column to users table...\n\n";

$sql = "ALTER TABLE users ADD COLUMN salary DECIMAL(10,2) DEFAULT NULL AFTER status";

if ($conn->query($sql)) {
    echo "✅ Successfully added salary column!\n\n";
    
    // Verify it was added
    $result = $conn->query('SHOW COLUMNS FROM users');
    echo "Current columns:\n";
    while($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "❌ Error: " . $conn->error . "\n";
}
?>
