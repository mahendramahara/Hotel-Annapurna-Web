<?php
session_start();
require_once('config/db.php');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

$data = $_GET['data'] ?? '';
if (empty($data)) {
    header("Location: my-bookings.php?error=no_payment_data");
    exit();
}

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

$transaction_code = $response['transaction_code'] ?? '';
$status = $response['status'] ?? '';
$total_amount = $response['total_amount'] ?? 0;
$transaction_uuid = $response['transaction_uuid'] ?? '';
$product_code = $response['product_code'] ?? '';
$signature = $response['signature'] ?? '';
$signed_field_names = $response['signed_field_names'] ?? '';

if (empty($transaction_code) || empty($status) || empty($transaction_uuid)) {
    header("Location: my-bookings.php?error=incomplete_response");
    exit();
}

$esewa_secret = '8gBm/:&EnhH.1/q';

$message = "transaction_code={$transaction_code},status={$status},total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$product_code},signed_field_names={$signed_field_names}";
$hash = base64_encode(hash_hmac('sha256', $message, $esewa_secret, true));

if ($status !== 'COMPLETE') {
    header("Location: my-bookings.php?error=payment_not_completed");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_cart_order = strpos($transaction_uuid, 'cart_') === 0;

try {
    $conn->begin_transaction();

    if ($is_cart_order) {
        $parts = explode('_', $transaction_uuid);
        $order_id = isset($parts[1]) ? (int)$parts[1] : 0;
        
        if ($order_id <= 0) {
            throw new Exception('Invalid order ID');
        }

        $get_order_stmt = $conn->prepare("SELECT booking_reference, order_type, item_id FROM orders WHERE id = ? AND user_id = ?");
        $get_order_stmt->bind_param("ii", $order_id, $user_id);
        $get_order_stmt->execute();
        $order_result = $get_order_stmt->get_result();
        
        if ($order_result->num_rows === 0) {
            throw new Exception('Order not found');
        }
        
        $order_row = $order_result->fetch_assoc();
        $booking_ref = $order_row['booking_reference'];
        $order_type = $order_row['order_type'];
        $item_id = $order_row['item_id'];
        
        $get_order_stmt->close();

        $update_order_stmt = $conn->prepare("UPDATE orders SET payment_method = 'esewa', payment_status = 'paid', status = 'confirmed', notes = CONCAT(COALESCE(notes, ''), ' | eSewa Transaction: ', ?) WHERE booking_reference = ? AND user_id = ?");
        $update_order_stmt->bind_param("ssi", $transaction_code, $booking_ref, $user_id);
        
        if (!$update_order_stmt->execute()) {
            throw new Exception('Failed to update order status');
        }
        
        $update_order_stmt->close();

        if ($order_type === 'room') {
            $room_update = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
            $room_update->bind_param("i", $item_id);
            $room_update->execute();
            $room_update->close();
            $redirect_page = 'my-bookings.php';
        } elseif ($order_type === 'table') {
            $table_update = $conn->prepare("UPDATE tables SET booking_status = 'booked' WHERE id = ?");
            $table_update->bind_param("i", $item_id);
            $table_update->execute();
            $table_update->close();
            $redirect_page = 'my-bookings.php';
        } else {
            $redirect_page = 'my-orders.php';
        }

        $conn->commit();
        
        echo "<script>
            try {
                if (typeof sessionStorage !== 'undefined') {
                    sessionStorage.removeItem('checkoutData');
                }
                if (typeof localStorage !== 'undefined') {
                    localStorage.removeItem('hotelCart');
                }
            } catch(e) {}
            window.location.href = '{$redirect_page}?success=payment_complete';
        </script>";
        exit();
        
    } else {
        $booking_id = explode('-', $transaction_uuid)[0] ?? 0;
        $booking_id = (int)$booking_id;
        
        if ($booking_id <= 0) {
            throw new Exception('Invalid booking ID');
        }

        $check_stmt = $conn->prepare("SELECT order_type, item_id FROM orders WHERE id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $booking_id, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception('Booking not found');
        }
        
        $booking = $check_result->fetch_assoc();
        $order_type = $booking['order_type'];
        $item_id = $booking['item_id'];
        $check_stmt->close();

        $update_stmt = $conn->prepare("UPDATE orders SET payment_method = 'esewa', payment_status = 'paid', status = 'confirmed', notes = CONCAT(COALESCE(notes, ''), ' | eSewa Transaction: ', ?) WHERE id = ? AND user_id = ?");
        $update_stmt->bind_param("sii", $transaction_code, $booking_id, $user_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception('Failed to confirm booking');
        }
        
        $update_stmt->close();

        if ($order_type === 'room') {
            $room_update = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
            $room_update->bind_param("i", $item_id);
            $room_update->execute();
            $room_update->close();
        } elseif ($order_type === 'table') {
            $table_update = $conn->prepare("UPDATE tables SET booking_status = 'booked' WHERE id = ?");
            $table_update->bind_param("i", $item_id);
            $table_update->execute();
            $table_update->close();
        }

        $conn->commit();
        header("Location: my-bookings.php?success=payment_complete");
        exit();
    }

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    error_log('Payment processing error: ' . $e->getMessage());
    header("Location: my-bookings.php?error=payment_processing_failed");
    exit();
}

$conn->close();
?>
