<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function getMailer() {
    $mail = new PHPMailer(true);

    // Gmail SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'thebcatu@gmail.com';
    $mail->Password   = 'bocvhrfpnksedtvw';  
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('thebcatu@gmail.com', 'Annapurna Hotel - No Reply');

    return $mail;
}

function sendMail($to, $subject, $body, $from = 'thebcatu@gmail.com', $fromName = 'Annapurna Hotel - No Reply') {
    $mail = new PHPMailer(true);

    try {
        // Gmail SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'thebcatu@gmail.com';
        $mail->Password   = 'bocvhrfpnksedtvw';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender
        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return "Mail could not be sent. Error: {$mail->ErrorInfo}";
    }
}
?>