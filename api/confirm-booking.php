<?php
session_start();
require_once('../config/db.php');
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $payment_method = trim($_POST['payment_method'] ?? '');
    $payment_status = trim($_POST['payment_status'] ?? 'pending');
    
    if ($booking_id <= 0) {
        throw new Exception('Invalid booking ID');
    }
    
    if (empty($payment_method)) {
        throw new Exception('Payment method is required');
    }
    
    $allowed_methods = ['cash', 'esewa', 'khalti', 'stripe'];
    if (!in_array($payment_method, $allowed_methods)) {
        throw new Exception('Invalid payment method');
    }
    
    $allowed_statuses = ['paid', 'pending', 'failed'];
    if (!in_array($payment_status, $allowed_statuses)) {
        throw new Exception('Invalid payment status');
    }
    
    $check_stmt = $conn->prepare("SELECT id, status as current_status FROM orders WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $booking_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Booking not found or access denied');
    }
    
    $booking = $result->fetch_assoc();
    
    if ($booking['current_status'] === 'confirmed') {
        echo json_encode([
            'success' => true,
            'message' => 'Booking already confirmed',
            'already_confirmed' => true
        ]);
        exit();
    }
    
    $conn->begin_transaction();
    
    try {
        $update_stmt = $conn->prepare("
            UPDATE orders 
            SET payment_method = ?, 
                payment_status = ?,
                status = 'confirmed',
                updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");
        
        $update_stmt->bind_param("ssii", $payment_method, $payment_status, $booking_id, $user_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception('Failed to update booking: ' . $update_stmt->error);
        }
        
        if ($update_stmt->affected_rows === 0) {
            throw new Exception('No rows updated - booking may not exist or already updated');
        }
        
        $transaction_ref = strtoupper($payment_method) . '_' . date('YmdHis') . '_' . $booking_id;
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Booking confirmed successfully',
            'booking_id' => $booking_id,
            'payment_method' => $payment_method,
            'payment_status' => $payment_status,
            'transaction_reference' => $transaction_ref
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('Booking Confirmation Error: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($stmt)) $stmt->close();
if (isset($conn)) $conn->close();
?>
