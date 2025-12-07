<?php
session_start();
require_once('config/db.php');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

$data = $_GET['data'] ?? '';
if (empty($data)) {
    header("Location: my-bookings.php?error=invalid_response");
    exit();
}

$decoded = base64_decode($data);
$response = json_decode($decoded, true);

if (!$response) {
    header("Location: my-bookings.php?error=invalid_data");
    exit();
}

$transaction_code = $response['transaction_code'] ?? '';
$status = $response['status'] ?? '';
$total_amount = $response['total_amount'] ?? 0;
$transaction_uuid = $response['transaction_uuid'] ?? '';
$product_code = $response['product_code'] ?? '';
$signature = $response['signature'] ?? '';

$esewa_secret = '8gBm/:&EnhH.1/q';
$message = "transaction_code={$transaction_code},status={$status},total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$product_code},signed_field_names=transaction_code,status,total_amount,transaction_uuid,product_code,signed_field_names";
$hash = base64_encode(hash_hmac('sha256', $message, $esewa_secret, true));

if ($hash !== $signature) {
    header("Location: my-bookings.php?error=signature_mismatch");
    exit();
}

if ($status !== 'COMPLETE') {
    header("Location: my-bookings.php?error=payment_incomplete");
    exit();
}

// Check if this is a cart order or booking
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
            
            $stmt = $conn->prepare("UPDATE orders SET payment_method = 'esewa', payment_status = 'paid', status = 'confirmed', notes = CONCAT(COALESCE(notes, ''), ' | eSewa Transaction: ', ?) WHERE booking_reference = ? AND user_id = ?");
            $stmt->bind_param("ssi", $transaction_code, $booking_ref, $user_id);
            
            if ($stmt->execute()) {
                echo "<script>
                    if (typeof sessionStorage !== 'undefined') {
                        sessionStorage.removeItem('checkoutData');
                    }
                    if (typeof localStorage !== 'undefined') {
                        localStorage.removeItem('hotelCart');
                    }
                    window.location.href = 'my-bookings.php?success=payment_complete';
                </script>";
                exit;
            } else {
                header("Location: my-orders.php?error=update_failed");
                exit;
            }
            
            $stmt->close();
        } else {
            header("Location: my-orders.php?error=order_not_found");
            exit;
        }
    } else {
        header("Location: my-orders.php?error=invalid_order_id");
        exit;
    }
} else {
    $booking_id = explode('-', $transaction_uuid)[0] ?? 0;
    
    $stmt = $conn->prepare("UPDATE orders SET payment_method = 'esewa', payment_status = 'paid', status = 'confirmed', notes = CONCAT(COALESCE(notes, ''), ' | eSewa Transaction: ', ?) WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $transaction_code, $booking_id, $user_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        header("Location: my-bookings.php?success=payment_complete");
    } else {
        header("Location: my-bookings.php?error=update_failed");
    }
    
    $stmt->close();
}

$conn->close();
?>
