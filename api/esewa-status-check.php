<?php
session_start();
require_once('../config/db.php');
require_once('../includes/esewa-helper.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (isset($input['message']) || isset($_POST['message'])) {
    $message = $input['message'] ?? ($_POST['message'] ?? '');
    
    if (empty($message)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Message is required']);
        exit;
    }
    
    $signature = generateEsewaSignature($message, ESEWA_SECRET_KEY);
    
    echo json_encode([
        'success' => true,
        'signature' => $signature
    ]);
    exit;
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

$booking_id = intval($_POST['booking_id'] ?? 0);
$transaction_uuid = trim($_POST['transaction_uuid'] ?? '');
$user_id = $_SESSION['user_id'];

if ($booking_id <= 0 || empty($transaction_uuid)) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$stmt = $conn->prepare("SELECT price FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit();
}

$booking = $result->fetch_assoc();
$total_amount = floatval($booking['price']);
$product_code = 'EPAYTEST';

$status_response = checkEsewaTransactionStatus($product_code, $total_amount, $transaction_uuid);

if (!$status_response) {
    echo json_encode([
        'success' => false,
        'message' => 'Unable to check transaction status',
        'status' => 'ERROR'
    ]);
    exit();
}

$transaction_status = $status_response['status'] ?? 'UNKNOWN';
$ref_id = $status_response['ref_id'] ?? null;

$status_messages = [
    'COMPLETE' => 'Payment completed successfully',
    'PENDING' => 'Payment is pending, please complete the payment',
    'FULL_REFUND' => 'Payment has been fully refunded',
    'PARTIAL_REFUND' => 'Payment has been partially refunded',
    'AMBIGUOUS' => 'Payment is in ambiguous state, please contact support',
    'NOT_FOUND' => 'Transaction not found or session expired',
    'CANCELED' => 'Transaction was canceled',
];

$message = $status_messages[$transaction_status] ?? 'Unknown transaction status';

if ($transaction_status === 'COMPLETE') {
    $stmt = $conn->prepare("UPDATE orders SET payment_status = 'paid', booking_status = 'confirmed', notes = CONCAT(COALESCE(notes, ''), ' | eSewa Ref: ', ?) WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $ref_id, $booking_id, $user_id);
    $stmt->execute();
}

echo json_encode([
    'success' => true,
    'status' => $transaction_status,
    'ref_id' => $ref_id,
    'message' => $message,
    'product_code' => $product_code,
    'transaction_uuid' => $transaction_uuid,
    'total_amount' => $total_amount
]);

$stmt->close();
$conn->close();
?>
