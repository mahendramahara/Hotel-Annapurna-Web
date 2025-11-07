<?php

function cleanupOTPSession()
{
    unset($_SESSION['pwd-resend-timer']);
    unset($_SESSION['pwd-otp-expiry']);
    unset($_SESSION['pwd-timer-start']);
    unset($_SESSION['pwd-current-otp']);
}
cleanupOTPSession();

session_start();
require_once 'config/connection.php';

$error = '';
$success = '';

// Check if token exists in URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $_SESSION['pwd-error'] = "Invalid or missing token!";
    header("Location: forget-password.php");
    exit();
}

$token = $_GET['token'];

// First verify if the token exists and is valid
$stmt = $conn->prepare("
    SELECT pr.email, pr.token, pr.expiry, pr.used, pr.is_expired, u.id as user_id 
    FROM password_resets pr 
    JOIN users u ON pr.email = u.email 
    WHERE pr.token = ? 
    AND pr.used = 1 
    ORDER BY pr.created_at DESC 
    LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['pwd-error'] = "Invalid or expired token. Please request a new password reset.";
    header("Location: forget-password.php?error=invalidtoken");
    exit();
}

$row = $result->fetch_assoc();
$email = $row['email'];
$user_id = $row['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['pwd-password'] ?? '';
    $confirm_password = $_POST['pwd-confirm-password'] ?? '';

    // Validate password
    if (empty($password)) {
        $error = "Password is required!";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Begin transaction
        $conn->begin_transaction();

        try {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update user's password
            $update_pwd = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $update_pwd->bind_param("si", $hashed_password, $user_id);

            if (!$update_pwd->execute()) {
                throw new Exception("Failed to update password");
            }

            // Mark token as used
            $update_token = $conn->prepare("
                UPDATE password_resets 
                SET used = 1, is_expired = 1 
                WHERE token = ?
            ");
            $update_token->bind_param("s", $token);

            if (!$update_token->execute()) {
                throw new Exception("Failed to update token status");
            }

            // If everything is successful, commit the transaction
            $conn->commit();

            // Set success message and redirect
            $_SESSION['pwd-success'] = "Password has been reset successfully! Please login with your new password.";
            $_SESSION['pwd-user-email'] = $email;

            header("Location: login.php");
            exit();
        } catch (Exception $e) {
            // If there's an error, rollback the transaction
            $conn->rollback();
            $error = "An error occurred while resetting your password. Please try again.";
        }
    }
}
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
            <h1>Set New Password</h1>

            <?php if ($error): ?>
                <div class="pwd-alert pwd-alert-error">
                    <ion-icon name="alert-circle"></ion-icon>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="pwd-reset-password-form">
                <div class="pwd-form-group">
                    <label for="pwd-password">
                        <ion-icon name="lock-closed"></ion-icon>
                        New Password
                    </label>
                    <div class="pwd-password-input-group">
                        <input type="password" id="pwd-password" name="pwd-password" required
                            minlength="8" placeholder="Enter new password">
                        <button type="button" class="pwd-toggle-password" aria-label="Toggle password visibility">
                            <ion-icon name="eye-outline" class="pwd-show-icon"></ion-icon>
                            <ion-icon name="eye-off-outline" class="pwd-hide-icon"></ion-icon>
                        </button>
                    </div>
                    <div class="pwd-password-requirements">
                        <small>Password must be at least 8 characters long</small>
                    </div>
                </div>

                <div class="pwd-form-group">
                    <label for="pwd-confirm-password">
                        <ion-icon name="shield-checkmark"></ion-icon>
                        Confirm Password
                    </label>
                    <div class="pwd-password-input-group">
                        <input type="password" id="pwd-confirm-password" name="pwd-confirm-password" required
                            minlength="8" placeholder="Confirm new password">
                        <button type="button" class="pwd-toggle-password" aria-label="Toggle confirm password visibility">
                            <ion-icon name="eye-outline" class="pwd-show-icon"></ion-icon>
                            <ion-icon name="eye-off-outline" class="pwd-hide-icon"></ion-icon>
                        </button>
                    </div>
                </div>

                <div class="pwd-button-group">
                    <button type="submit" class="pwd-btn pwd-btn-primary">
                        <ion-icon name="refresh-circle"></ion-icon>
                        Reset Password
                    </button>
                </div>
            </form>

        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pwd-reset-password-form');
            const password = document.getElementById('pwd-password');
            const confirmPassword = document.getElementById('pwd-confirm-password');
            const toggleButtons = document.querySelectorAll('.pwd-toggle-password');

            // Show/hide password functionality
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const type = input.type === 'password' ? 'text' : 'password';
                    input.type = type;

                    // Update aria-label for accessibility
                    this.setAttribute('aria-label',
                        type === 'password' ? 'Show password' : 'Hide password'
                    );
                });
            });

            // Real-time password validation
            password.addEventListener('input', function() {
                const isValid = this.value.length >= 8;
                this.setCustomValidity(isValid ? '' : 'Password must be at least 8 characters long');

                // Update confirm password validation
                if (confirmPassword.value) {
                    const matches = confirmPassword.value === this.value;
                    confirmPassword.setCustomValidity(matches ? '' : 'Passwords do not match');
                }
            });

            // Real-time password match validation
            confirmPassword.addEventListener('input', function() {
                const matches = this.value === password.value;
                this.setCustomValidity(matches ? '' : 'Passwords do not match');
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    const alertContainer = document.createElement('div');
                    alertContainer.className = 'pwd-alert pwd-alert-error';
                    alertContainer.innerHTML = '<ion-icon name="alert-circle"></ion-icon>Passwords do not match!';

                    // Remove any existing error messages
                    const existingAlerts = document.querySelectorAll('.pwd-alert.pwd-alert-error');
                    existingAlerts.forEach(alert => alert.remove());

                    this.insertBefore(alertContainer, this.firstChild);
                    return false;
                }

                if (password.value.length < 8) {
                    e.preventDefault();
                    const alertContainer = document.createElement('div');
                    alertContainer.className = 'pwd-alert pwd-alert-error';
                    alertContainer.innerHTML = '<ion-icon name="alert-circle"></ion-icon>Password must be at least 8 characters long!';

                    // Remove any existing error messages
                    const existingAlerts = document.querySelectorAll('.pwd-alert.pwd-alert-error');
                    existingAlerts.forEach(alert => alert.remove());

                    this.insertBefore(alertContainer, this.firstChild);
                    return false;
                }

                // Disable submit button to prevent double submission
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<ion-icon name="hourglass-outline"></ion-icon>Processing...';

                // Re-enable button after 30 seconds if form hasn't redirected
                setTimeout(() => {
                    if (!submitButton.disabled) return;
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<ion-icon name="refresh-circle"></ion-icon>Reset Password';
                }, 30000);
            });
        });
    </script>
</body>

</html>