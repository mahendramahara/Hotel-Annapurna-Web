<?php
session_start();
require_once 'config/db.php';
require_once 'config/setup_mailer.php';
require_once 'includes/activity-logger.php';

// Function to generate OTP
function generateOTP() {
    return sprintf("%06d", mt_rand(0, 999999));
}

// Function to send OTP email
function sendOTPEmail($email, $firstName, $otp) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $firstName);
        $mail->Subject = 'Registration OTP - Annapurna Hotel';
        
        $mail->isHTML(true);
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #1891d1; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 30px; border-radius: 5px; }
                    .otp-box { background: white; border: 2px solid #1891d1; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; color: #1891d1; margin: 20px 0; letter-spacing: 5px; }
                    .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Welcome to Annapurna Hotel</h1>
                    </div>
                    <div class='content'>
                        <h2>Hello $firstName,</h2>
                        <p>Thank you for registering with Annapurna Hotel and Restaurant!</p>
                        <p>Your One-Time Password (OTP) for registration verification is:</p>
                        <div class='otp-box'>$otp</div>
                        <p><strong>This OTP will expire in 10 minutes.</strong></p>
                        <p>Please enter this code on the verification page to complete your registration.</p>
                        <p>If you didn't request this registration, please ignore this email.</p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Annapurna Hotel and Restaurant. All rights reserved.</p>
                        <p>Tinkune-Kathmandu, Nepal</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Your OTP for registration is: $otp. This OTP will expire in 10 minutes.";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $contact = trim($_POST['contact']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Validation
    $errors = [];
    
    // Name validation
    if (empty($firstName) || empty($lastName)) {
        $errors[] = "First name and last name are required";
    }
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }
    
    // Contact validation
    if (!preg_match('/^\d{10}$/', $contact)) {
        $errors[] = "Contact number must be 10 digits";
    }
    
    // Password validation
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email already registered";
    }
    
    if (!empty($errors)) {
        $_SESSION['reg_errors'] = $errors;
        $_SESSION['reg_form_data'] = $_POST;
        header("Location: register.php");
        exit();
    }
    
    // Generate OTP
    $otp = generateOTP();
    $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    // Store registration data in session temporarily
    $_SESSION['reg_data'] = [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'contact' => $contact,
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ];
    
    // Invalidate previous OTPs for this email
    $stmt = $conn->prepare("UPDATE registration_otps SET is_expired = 1 WHERE email = ? AND is_expired = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Store new OTP
    $stmt = $conn->prepare("INSERT INTO registration_otps (email, otp, expiry) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $otp, $expiry);
    
    if ($stmt->execute()) {
        // Send OTP email
        if (sendOTPEmail($email, $firstName, $otp)) {
            $_SESSION['reg_email'] = $email;
            $_SESSION['reg_otp_expiry'] = strtotime($expiry);
            $_SESSION['reg_resend_timer'] = time() + 60; // 60 seconds cooldown
            
            // For testing purposes - remove in production
            $_SESSION['test_otp'] = $otp;
            
            // Log registration activity
            logActivity($conn, null, 'registration', "$firstName $lastName registered with email: $email");
            
            header("Location: verify-register.php");
            exit();
        } else {
            $_SESSION['reg_errors'] = ["Failed to send OTP email. Please try again."];
            header("Location: register.php");
            exit();
        }
    } else {
        $_SESSION['reg_errors'] = ["Registration failed. Please try again."];
        header("Location: register.php");
        exit();
    }
}

// If accessed directly, redirect to register page
header("Location: register.php");
exit();
