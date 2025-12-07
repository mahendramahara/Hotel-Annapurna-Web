<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function getMailer() {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    // your smtp email here
    $mail->Username   = 'your_email@gmail.com';

    // your smtp app pwd here
    $mail->Password   = 'your_app_password_here';

    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('your_email@gmail.com', 'Annapurna Hotel - No Reply');

    return $mail;
}

function sendMail($to, $subject, $body, $from = 'your_email@gmail.com', $fromName = 'Annapurna Hotel - No Reply') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // your smtp email here
        $mail->Username   = 'your_email@gmail.com';

        // your smtp app pwd here
        $mail->Password   = 'your_app_password_here';

        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

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
