<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$code = strtoupper(trim($data['code'] ?? ''));
$subtotal = floatval($data['subtotal'] ?? 0);

if (empty($code)) {
    echo json_encode(['success' => false, 'message' => 'Coupon code is required']);
    exit;
}

if ($subtotal <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart amount']);
    exit;
}

$stmt = $conn->prepare("SELECT id, code, discount_type, discount_value, min_purchase, max_discount, usage_limit, used_count, valid_from, valid_until, status FROM coupons WHERE code = ? AND status = 'active'");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid coupon code']);
    exit;
}

$coupon = $result->fetch_assoc();
$now = new DateTime();
$valid_from = new DateTime($coupon['valid_from']);
$valid_until = new DateTime($coupon['valid_until']);

if ($now < $valid_from) {
    echo json_encode(['success' => false, 'message' => 'Coupon is not yet valid']);
    exit;
}

if ($now > $valid_until) {
    echo json_encode(['success' => false, 'message' => 'Coupon has expired']);
    exit;
}

if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
    echo json_encode(['success' => false, 'message' => 'Coupon usage limit reached']);
    exit;
}

if ($subtotal < $coupon['min_purchase']) {
    echo json_encode([
        'success' => false, 
        'message' => 'Minimum purchase of Rs. ' . number_format($coupon['min_purchase'], 2) . ' required'
    ]);
    exit;
}

$discount_amount = 0;
if ($coupon['discount_type'] === 'percentage') {
    $discount_amount = ($subtotal * $coupon['discount_value']) / 100;
    if ($coupon['max_discount'] && $discount_amount > $coupon['max_discount']) {
        $discount_amount = $coupon['max_discount'];
    }
} else {
    $discount_amount = $coupon['discount_value'];
}

$discount_amount = min($discount_amount, $subtotal);

echo json_encode([
    'success' => true,
    'message' => 'Coupon applied successfully',
    'coupon' => [
        'code' => $coupon['code'],
        'discount_type' => $coupon['discount_type'],
        'discount_value' => $coupon['discount_value'],
        'discount_amount' => round($discount_amount, 2)
    ]
]);

$stmt->close();
$conn->close();
?>