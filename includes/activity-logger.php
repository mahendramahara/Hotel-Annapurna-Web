<?php
function logActivity($conn, $user_id, $activity_type, $description) {
    if(!$conn || !$user_id) {
        return false;
    }
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    try {
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, activity_type, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        if(!$stmt) {
            error_log('Prepare failed: ' . $conn->error);
            return false;
        }
        
        $user_id = intval($user_id);
        $stmt->bind_param("issss", $user_id, $activity_type, $description, $ip_address, $user_agent);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        error_log('Activity logging error: ' . $e->getMessage());
        return false;
    }
}
?>
