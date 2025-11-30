<?php
function logActivity($conn, $user_id, $activity_type, $description) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, activity_type, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $activity_type, $description, $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
}
?>
