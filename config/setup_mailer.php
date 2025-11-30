<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function getMailer() {
    $mail = new PHPMailer(true);
    
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'demo_username';
    $mail->Password   = 'demo_password';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 2525;
    
    // Default From address
    $mail->setFrom('noreply@annapurnahotel.com', 'Annapurna Hotel');
    
    return $mail;
}

function sendMail($to, $subject, $body, $from = 'noreply@annapurnahotel.com', $fromName = 'Annapurna Hotel') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'b37616df963a85';
        $mail->Password   = '7832c9e7f31b33';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 2525;

        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);

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
