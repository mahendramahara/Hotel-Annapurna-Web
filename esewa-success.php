<?php
session_start();
require_once('config/db.php');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

$data = $_GET['data'] ?? '';
if (empty($data)) {
    // eSewa sometimes sends data via POST instead of GET
    header("Location: my-bookings.php?error=no_payment_data");
    exit();
}

// Decode the base64 data from eSewa
$decoded = base64_decode($data, true);
if ($decoded === false) {
    header("Location: my-bookings.php?error=invalid_data_format");
    exit();
}

$response = json_decode($decoded, true);
if (!$response) {
    header("Location: my-bookings.php?error=invalid_json");
    exit();
}

// Extract eSewa response fields
$transaction_code = $response['transaction_code'] ?? '';
$status = $response['status'] ?? '';
$total_amount = $response['total_amount'] ?? 0;
$transaction_uuid = $response['transaction_uuid'] ?? '';
$product_code = $response['product_code'] ?? '';
$signature = $response['signature'] ?? '';

if (empty($transaction_code) || empty($status) || empty($transaction_uuid)) {
    header("Location: my-bookings.php?error=incomplete_response");
    exit();
}

// Verify signature
$esewa_secret = '8gBm/:&EnhH.1/q';
// eSewa signature format: transaction_code,status,total_amount,transaction_uuid,product_code
$message = "transaction_code={$transaction_code},status={$status},total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$product_code}";
$hash = base64_encode(hash_hmac('sha256', $message, $esewa_secret, true));

if ($hash !== $signature) {
    error_log("eSewa Signature Mismatch - Expected: {$hash}, Got: {$signature}");
    header("Location: my-bookings.php?error=signature_invalid");
    exit();
}

// Check payment status
if ($status !== 'COMPLETE') {
    header("Location: my-bookings.php?error=payment_not_completed");
    exit();
}

// Process payment based on transaction type
$is_cart_order = strpos($transaction_uuid, 'cart_') === 0;
$user_id = $_SESSION['user_id'];

if ($is_cart_order) {
    // Extract order ID from transaction_uuid (format: cart_ORDER_ID_TIMESTAMP)
    $parts = explode('_', $transaction_uuid);
    $order_id = isset($parts[1]) ? (int)$parts[1] : 0;
    
    if ($order_id > 0) {
        $get_ref_stmt = $conn->prepare("SELECT booking_reference FROM orders WHERE id = ? AND user_id = ?");
        $get_ref_stmt->bind_param("ii", $order_id, $user_id);
        $get_ref_stmt->execute();
        $ref_result = $get_ref_stmt->get_result();
        
        if ($ref_result->num_rows > 0) {
            $ref_row = $ref_result->fetch_assoc();
            $booking_ref = $ref_row['booking_reference'];
            $get_ref_stmt->close();
            
            $stmt = $conn->prepare("UPDATE orders SET payment_method = 'esewa', payment_status = 'paid', status = 'confirmed', notes = CONCAT(COALESCE(notes, ''), ' | eSewa Ref: ', ?) WHERE booking_reference = ? AND user_id = ?");
            $stmt->bind_param("ssi", $transaction_code, $booking_ref, $user_id);
            
            if ($stmt->execute()) {
                echo "<script>
                    try {
                        if (typeof sessionStorage !== 'undefined') {
                            sessionStorage.removeItem('checkoutData');
                        }
                        if (typeof localStorage !== 'undefined') {
                            localStorage.removeItem('hotelCart');
                        }
                    } catch(e) {}
                    window.location.href = 'my-bookings.php?success=payment_complete';
                </script>";
                exit;
            } else {
                header("Location: my-bookings.php?error=payment_saved_failed");
                exit;
            }
            
            $stmt->close();
        } else {
            header("Location: my-bookings.php?error=order_not_found");
            exit;
        }
    } else {
        header("Location: my-bookings.php?error=invalid_order_format");
        exit;
    }
} else {
    // Direct booking payment
    $booking_id = explode('-', $transaction_uuid)[0] ?? 0;
    $booking_id = (int)$booking_id;
    
    if ($booking_id <= 0) {
        header("Location: my-bookings.php?error=invalid_booking_id");
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE orders SET payment_method = 'esewa', payment_status = 'paid', status = 'confirmed', notes = CONCAT(COALESCE(notes, ''), ' | eSewa Ref: ', ?) WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $transaction_code, $booking_id, $user_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        header("Location: my-bookings.php?success=payment_complete");
    } else {
        header("Location: my-bookings.php?error=payment_confirmation_failed");
    }
    
    $stmt->close();
}

$conn->close();
?>
