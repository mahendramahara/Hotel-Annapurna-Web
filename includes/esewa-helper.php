<?php
define('ESEWA_PAYMENT_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form');
define('ESEWA_MERCHANT_CODE', 'EPAYTEST');
define('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q');

function generateEsewaSignature($message, $secret_key) {
    $hash = hash_hmac('sha256', $message, $secret_key, true);
    return base64_encode($hash);
}

function prepareEsewaPayment($booking_id, $amount, $tax_amount, $total_amount) {
    $esewa_secret = '8gBm/:&EnhH.1/q';
    $product_code = 'EPAYTEST';
    
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $success_url = $base_url . "/Hotel-Annapurna-Web/esewa-success.php";
    $failure_url = $base_url . "/Hotel-Annapurna-Web/esewa-failure.php";
    
    $transaction_uuid = $booking_id . '-' . date('Ymd-His');
    
    $formatted_amount = floatval($amount);
    $formatted_tax = floatval($tax_amount);
    $formatted_total = floatval($total_amount);
    
    $signed_field_names = "total_amount,transaction_uuid,product_code";
    $message = "total_amount=" . $formatted_total . ",transaction_uuid=" . $transaction_uuid . ",product_code=" . $product_code;
    $signature = generateEsewaSignature($message, $esewa_secret);
    
    return [
        'action_url' => 'https://rc-epay.esewa.com.np/api/epay/main/v2/form',
        'amount' => $formatted_amount,
        'tax_amount' => $formatted_tax,
        'total_amount' => $formatted_total,
        'transaction_uuid' => $transaction_uuid,
        'product_code' => $product_code,
        'product_service_charge' => 0,
        'product_delivery_charge' => 0,
        'success_url' => $success_url,
        'failure_url' => $failure_url,
        'signed_field_names' => $signed_field_names,
        'signature' => $signature
    ];
}

function checkEsewaTransactionStatus($product_code, $total_amount, $transaction_uuid) {
    $status_url = "https://rc.esewa.com.np/api/epay/transaction/status/?product_code={$product_code}&total_amount={$total_amount}&transaction_uuid={$transaction_uuid}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $status_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200 && $response) {
        return json_decode($response, true);
    }
    
    return null;
}
?>
