<?php
session_start();
require_once 'config/db.php';

// Check if user has registration data
if (!isset($_SESSION['reg_email']) || !isset($_SESSION['reg_data'])) {
    header("Location: register.php");
    exit();
}

$email = $_SESSION['reg_email'];
$error = '';
$success = '';

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify_otp'])) {
        $entered_otp = $_POST['otp'];
        $current_time = date('Y-m-d H:i:s');
        
        // Verify OTP
        $stmt = $conn->prepare("
            SELECT otp, expiry 
            FROM registration_otps 
            WHERE email = ? 
            AND otp = ? 
            AND used = 0 
            AND is_expired = 0 
            AND expiry > ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->bind_param("sss", $email, $entered_otp, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // OTP is valid, create user account
            $reg_data = $_SESSION['reg_data'];
            
            $stmt = $conn->prepare("
                INSERT INTO users (first_name, last_name, email, contact, password, status) 
                VALUES (?, ?, ?, ?, ?, 'verified')
            ");
            $stmt->bind_param("sssss", 
                $reg_data['first_name'], 
                $reg_data['last_name'], 
                $reg_data['email'], 
                $reg_data['contact'], 
                $reg_data['password']
            );
            
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                
                // Mark OTP as used
                $update_stmt = $conn->prepare("UPDATE registration_otps SET used = 1 WHERE email = ? AND otp = ?");
                $update_stmt->bind_param("ss", $email, $entered_otp);
                $update_stmt->execute();
                
                // Set session and cookie
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_email'] = $reg_data['email'];
                $_SESSION['user_first_name'] = $reg_data['first_name'];
                $_SESSION['user_last_name'] = $reg_data['last_name'];
                $_SESSION['user_name'] = $reg_data['first_name'] . ' ' . $reg_data['last_name'];
                $_SESSION['user_role'] = 'customer';
                $_SESSION['logged_in'] = true;
                
                // Set cookie for 30 days
                $cookie_value = base64_encode($user_id . '|' . $reg_data['email']);
                setcookie('user_auth', $cookie_value, time() + (30 * 24 * 60 * 60), '/');
                
                // Clear registration session data
                unset($_SESSION['reg_data']);
                unset($_SESSION['reg_email']);
                unset($_SESSION['reg_otp_expiry']);
                unset($_SESSION['reg_resend_timer']);
                unset($_SESSION['test_otp']);
                
                $success = "Registration successful! Redirecting...";
                header("refresh:2;url=index.php");
            } else {
                $error = "Failed to create account. Please try again.";
            }
        } else {
            // Check if OTP exists but expired
            $stmt = $conn->prepare("
                SELECT expiry 
                FROM registration_otps 
                WHERE email = ? 
                AND otp = ? 
                AND used = 0 
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->bind_param("ss", $email, $entered_otp);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (strtotime($row['expiry']) <= strtotime($current_time)) {
                    $error = "OTP has expired! Please request a new OTP.";
                }
            } else {
                $error = "Invalid OTP! Please check and try again.";
            }
        }
    } elseif (isset($_POST['resend_otp'])) {
        // Check if cooldown period has passed
        if (!isset($_SESSION['reg_resend_timer']) || time() >= $_SESSION['reg_resend_timer']) {
            // Generate new OTP
            require_once 'config/setup_mailer.php';
            
            function generateOTP() {
                return sprintf("%06d", mt_rand(0, 999999));
            }
            
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
                                    <p>Your new One-Time Password (OTP) for registration verification is:</p>
                                    <div class='otp-box'>$otp</div>
                                    <p><strong>This OTP will expire in 10 minutes.</strong></p>
                                    <p>Please enter this code on the verification page to complete your registration.</p>
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
            
            $otp = generateOTP();
            $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            // Invalidate previous OTPs
            $stmt = $conn->prepare("UPDATE registration_otps SET is_expired = 1 WHERE email = ? AND is_expired = 0");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            // Store new OTP
            $stmt = $conn->prepare("INSERT INTO registration_otps (email, otp, expiry) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $otp, $expiry);
            
            if ($stmt->execute()) {
                // Send OTP email
                $reg_data = $_SESSION['reg_data'];
                if (sendOTPEmail($email, $reg_data['first_name'], $otp)) {
                    $_SESSION['reg_otp_expiry'] = strtotime($expiry);
                    $_SESSION['reg_resend_timer'] = time() + 60; // 60 seconds cooldown
                    $_SESSION['test_otp'] = $otp; // For testing
                    
                    $success = "New OTP has been sent to your email!";
                } else {
                    $error = "Failed to send OTP. Please try again.";
                }
            } else {
                $error = "Failed to generate new OTP. Please try again.";
            }
        } else {
            $remaining = $_SESSION['reg_resend_timer'] - time();
            $error = "Please wait $remaining seconds before requesting a new OTP.";
        }
    }
}

// Calculate remaining time
$otpExpiryRemaining = isset($_SESSION['reg_otp_expiry']) ? max(0, $_SESSION['reg_otp_expiry'] - time()) : 0;
$resendTimeRemaining = isset($_SESSION['reg_resend_timer']) ? max(0, $_SESSION['reg_resend_timer'] - time()) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Registration - Annapurna Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        :root {
            --primary-color: #1891d1;
            --primary-dark: #1d64c2;
            --background-color: #e6f2ff;
            --text-dark: #002960;
            --white: #ffffff;
            --color-confirmed-bg: #d4edda;
            --color-confirmed-text: #155724;
            --color-cancelled-bg: #f8d7da;
            --color-cancelled-text: #721c24;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .verify-container {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .verify-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .verify-header h1 {
            color: var(--text-dark);
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .verify-header p {
            color: #666;
            font-size: 14px;
        }

        .email-display {
            background: var(--background-color);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            color: var(--text-dark);
            font-weight: 500;
        }

        .timer-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 10px;
        }

        .timer-box {
            flex: 1;
            background: var(--background-color);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .timer-box ion-icon {
            font-size: 24px;
            color: var(--primary-color);
        }

        .timer-box .timer-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .timer-box .timer-value {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-top: 5px;
        }

        .otp-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 25px;
        }

        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            border: 2px solid #ddd;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .otp-input:focus {
            outline: none;
            border-color: var(--primary-color);
            transform: scale(1.05);
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert ion-icon {
            font-size: 24px;
        }

        .alert-error {
            background: var(--color-cancelled-bg);
            color: var(--color-cancelled-text);
        }

        .alert-success {
            background: var(--color-confirmed-bg);
            color: var(--color-confirmed-text);
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--white);
            margin-bottom: 15px;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: #6c757d;
            color: var(--white);
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 480px) {
            .verify-container {
                padding: 30px 20px;
            }

            .otp-input {
                width: 40px;
                height: 50px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-header">
            <h1>Verify Your Email</h1>
            <p>Enter the OTP sent to your email address</p>
        </div>

        <div class="email-display">
            <ion-icon name="mail"></ion-icon>
            <?php echo htmlspecialchars($email); ?>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <ion-icon name="alert-circle"></ion-icon>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <ion-icon name="checkmark-circle"></ion-icon>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['test_otp'])): ?>
            <div class="alert alert-info">
                <strong>Test OTP:</strong> <?php echo $_SESSION['test_otp']; ?>
            </div>
        <?php endif; ?>

        <div class="timer-container">
            <div class="timer-box">
                <ion-icon name="timer-outline"></ion-icon>
                <div class="timer-label">OTP Expires In</div>
                <div class="timer-value" id="otpTimer">00:00</div>
            </div>
            <div class="timer-box">
                <ion-icon name="refresh-outline"></ion-icon>
                <div class="timer-label">Resend Available In</div>
                <div class="timer-value" id="resendTimer">00:00</div>
            </div>
        </div>

        <form method="POST" id="otpForm">
            <div class="otp-container">
                <input type="text" maxlength="1" class="otp-input" data-index="0" autofocus>
                <input type="text" maxlength="1" class="otp-input" data-index="1">
                <input type="text" maxlength="1" class="otp-input" data-index="2">
                <input type="text" maxlength="1" class="otp-input" data-index="3">
                <input type="text" maxlength="1" class="otp-input" data-index="4">
                <input type="text" maxlength="1" class="otp-input" data-index="5">
            </div>
            <input type="hidden" name="otp" id="otpFinal">
            <button type="submit" name="verify_otp" class="btn btn-primary">
                <ion-icon name="checkmark-circle"></ion-icon>
                Verify OTP
            </button>
        </form>

        <form method="POST" id="resendForm">
            <button type="submit" name="resend_otp" id="resendBtn" class="btn btn-secondary" disabled>
                <ion-icon name="refresh"></ion-icon>
                Resend OTP
            </button>
        </form>

        <div class="back-link">
            <a href="register.php">
                <ion-icon name="arrow-back"></ion-icon>
                Back to Registration
            </a>
        </div>
    </div>

    <script>
        // Initialize timers
        let otpExpiryTime = <?php echo $otpExpiryRemaining; ?>;
        let resendTime = <?php echo $resendTimeRemaining; ?>;

        // OTP input handling
        const otpInputs = document.querySelectorAll('.otp-input');
        const otpForm = document.getElementById('otpForm');
        const otpFinal = document.getElementById('otpFinal');

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value;
                
                if (value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                
                updateOTPValue();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').slice(0, 6);
                
                pastedData.split('').forEach((char, i) => {
                    if (i < otpInputs.length) {
                        otpInputs[i].value = char;
                    }
                });
                
                updateOTPValue();
                
                if (pastedData.length === 6) {
                    otpInputs[5].focus();
                }
            });
        });

        function updateOTPValue() {
            let otp = '';
            otpInputs.forEach(input => {
                otp += input.value;
            });
            otpFinal.value = otp;
        }

        // Timer functions
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        function updateTimers() {
            if (otpExpiryTime > 0) {
                document.getElementById('otpTimer').textContent = formatTime(otpExpiryTime);
                otpExpiryTime--;
            } else {
                document.getElementById('otpTimer').textContent = '00:00';
                document.getElementById('otpTimer').style.color = '#dc3545';
            }

            if (resendTime > 0) {
                document.getElementById('resendTimer').textContent = formatTime(resendTime);
                document.getElementById('resendBtn').disabled = true;
                resendTime--;
            } else {
                document.getElementById('resendTimer').textContent = 'Ready';
                document.getElementById('resendBtn').disabled = false;
            }
        }

        // Update timers every second
        updateTimers();
        setInterval(updateTimers, 1000);
    </script>
</body>
</html>
