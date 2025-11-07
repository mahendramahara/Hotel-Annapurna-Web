<?php
// Add this code near the beginning of forget-password.php
if (isset($_SESSION['pwd-error'])) {
    $error = $_SESSION['pwd-error'];
    unset($_SESSION['pwd-error']);
}

session_start();
require_once 'config/connection.php';

// Function to generate secure random token
function generateToken($length = 64)
{
    return bin2hex(random_bytes($length / 2));
}

// Function to generate OTP
function generateOTP()
{
    return sprintf("%06d", mt_rand(0, 999999));
}

// Function to generate and store new OTP
function generateAndStoreOTP($conn, $email)
{
    $otp = generateOTP();
    $token = generateToken();
    $expiry = date('Y-m-d H:i:s', strtotime('+1 minute')); // Set expiry to 1 minute

    // First, invalidate all previous OTPs for this email
    $stmt = $conn->prepare("UPDATE password_resets SET is_expired = 1 WHERE email = ? AND is_expired = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Store new OTP
    $stmt = $conn->prepare("INSERT INTO password_resets (email, otp, token, expiry) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $otp, $token, $expiry);

    if ($stmt->execute()) {
        $_SESSION['pwd-resend-timer'] = time() + 60;
        $_SESSION['pwd-otp-expiry'] = strtotime($expiry);
        $_SESSION['pwd-timer-start'] = time();

        // For development/testing - remove in production
        $_SESSION['pwd-current-otp'] = $otp; // This is just for testing
        return true;
    }
    return false;
}

$error = '';
$success = '';
$step = 1;
$email = '';

// Restore email from session if available
if (isset($_SESSION['pwd-reset-email'])) {
    $email = $_SESSION['pwd-reset-email'];
    if (isset($_SESSION['pwd-otp-step']) && $_SESSION['pwd-otp-step'] === true) {
        $step = 2;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        // Check if user exists
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? AND status = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            if (generateAndStoreOTP($conn, $email)) {
                $success = "OTP has been generated successfully! OTP will expire in 1 minute.";
                $step = 2;
                $_SESSION['reset_email'] = $email;
                $_SESSION['otp_step'] = true;
            } else {
                $error = "Error generating OTP. Please try again.";
            }
        } else {
            $error = "Email address not found!";
        }
    } elseif (isset($_POST['otp'])) {
        $entered_otp = $_POST['otp'];
        $email = $_POST['user_email'];

        // Verify OTP
        $current_time = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("
            SELECT token, expiry 
            FROM password_resets 
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
            $row = $result->fetch_assoc();

            // Mark OTP as used
            $update_stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE email = ? AND otp = ?");
            $update_stmt->bind_param("ss", $email, $entered_otp);
            $update_stmt->execute();

            // Clear OTP-related session variables
            unset($_SESSION['resend_timer']);
            unset($_SESSION['otp_step']);
            unset($_SESSION['otp_expiry']);
            unset($_SESSION['current_otp']);

            header("Location: reset-password.php?token=" . $row['token']);
            exit();
        } else {
            // Check if OTP exists but expired
            $stmt = $conn->prepare("
                SELECT expiry 
                FROM password_resets 
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
            $step = 2;
        }
    } elseif (isset($_POST['resend_otp'])) {
        $email = $_POST['user_email'];

        if (!isset($_SESSION['resend_timer']) || time() >= $_SESSION['resend_timer']) {
            if (generateAndStoreOTP($conn, $email)) {
                $success = "New OTP has been generated and sent successfully! OTP will expire in 1 minute.";
                $step = 2;
            } else {
                $error = "Error generating new OTP. Please try again.";
            }
        } else {
            $error = "Please wait before requesting a new OTP.";
            $step = 2;
        }
    }
}

// Calculate remaining times
$resendTimeRemaining = isset($_SESSION['resend_timer']) ? max(0, $_SESSION['resend_timer'] - time()) : 0;
$otpExpiryRemaining = isset($_SESSION['otp_expiry']) ? max(0, $_SESSION['otp_expiry'] - time()) : 0;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Annapurna Hotel and Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="assets/css/forgot-password.css">
</head>

<body>
    <div class="pwd-container">
        <div class="pwd-form-container">
            <div class="pwd-logo">
                <img src="logo.png" alt="Annapurna Hotel Logo">
            </div>
            <h1>Reset Password</h1>

            <?php if ($error): ?>
                <div class="pwd-alert pwd-alert-error">
                    <ion-icon name="alert-circle"></ion-icon>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="pwd-alert pwd-alert-success">
                    <ion-icon name="checkmark-circle"></ion-icon>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <!-- For development/testing - remove in production -->
            <?php if (isset($_SESSION['pwd-current-otp'])): ?>
                <div class="pwd-alert pwd-alert-info">
                    <strong>Test OTP:</strong> <?php echo $_SESSION['pwd-current-otp']; ?>
                </div>
            <?php endif; ?>

            <div id="pwd-step1Form" <?php echo $step == 2 ? 'style="display:none;"' : ''; ?>>
                <form id="pwd-emailForm" method="POST" action="">
                    <div class="pwd-form-group">
                        <label for="pwd-email">
                            <ion-icon name="mail"></ion-icon>
                            Email Address
                        </label>
                        <input type="email" id="pwd-email" name="email"
                            value="<?php echo htmlspecialchars($email); ?>"
                            required placeholder="Enter your email address">
                    </div>
                    <div class="pwd-button-group">
                        <button type="submit" class="pwd-btn pwd-btn-primary">
                            <ion-icon name="paper-plane"></ion-icon>
                            Get OTP
                        </button>
                        <a href="login.php" class="pwd-text-link">
                            <ion-icon name="arrow-back"></ion-icon>
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>

            <!-- Step 2 form with modified email display and timer -->
            <div id="pwd-step2Form" <?php echo $step == 1 ? 'style="display:none;"' : ''; ?>>
                <div class="pwd-email-display">
                    <span>Your entered email is: <?php echo htmlspecialchars($email); ?></span>
                    <a href="javascript:void(0)" class="pwd-text-link" onclick="changeEmail()">Change</a>
                </div>

                <div class="pwd-timer-container">
                    <div class="pwd-otp-timer">
                        <ion-icon name="timer-outline"></ion-icon>
                        OTP expires in: <span id="pwd-otpTimer">00:00</span>
                    </div>
                </div>

                <form id="pwd-otpForm" method="POST" action="">
                    <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($email); ?>">
                    <div class="pwd-form-group">
                        <label for="pwd-otp">Enter OTP</label>
                        <div class="pwd-otp-container">
                            <input type="text" maxlength="1" class="pwd-otp-input" data-index="1">
                            <input type="text" maxlength="1" class="pwd-otp-input" data-index="2">
                            <input type="text" maxlength="1" class="pwd-otp-input" data-index="3">
                            <input type="text" maxlength="1" class="pwd-otp-input" data-index="4">
                            <input type="text" maxlength="1" class="pwd-otp-input" data-index="5">
                            <input type="text" maxlength="1" class="pwd-otp-input" data-index="6">
                        </div>
                        <input type="hidden" name="otp" id="pwd-otpFinal">
                    </div>
                    <div class="pwd-button-group">
                        <button type="submit" class="pwd-btn pwd-btn-primary">
                            <ion-icon name="checkmark-circle"></ion-icon>
                            Verify OTP
                        </button>
                    </div>
                </form>

                <!-- Resend OTP button will be shown only after OTP expires -->
                <div class="pwd-resend-container" style="display: none;">
                    <form method="POST" action="" class="pwd-resend-form">
                        <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($email); ?>">
                        <input type="hidden" name="resend_otp" value="1">
                        <button type="submit" class="pwd-text-link pwd-btn-resend">
                            <ion-icon name="refresh-circle"></ion-icon>
                            Resend OTP
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize PHP variables for JavaScript
        const initialResendTimeRemaining = <?php echo isset($_SESSION['pwd-resend-timer']) ? max(0, $_SESSION['pwd-resend-timer'] - time()) : 0; ?>;
        const initialOtpExpiryRemaining = <?php echo isset($_SESSION['pwd-otp-expiry']) ? max(0, $_SESSION['pwd-otp-expiry'] - time()) : 0; ?>;
    </script>

    <script src="assets/js/forgotpwd.js"></script>
</body>

</html>