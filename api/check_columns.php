<?php
include(__DIR__ . '/../config/db.php');

echo "Checking users table structure:\n\n";

$result = $conn->query('SHOW COLUMNS FROM users');

if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Default'] . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
