<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Annapurna Hotel and Restaurant</title>
    <link rel="stylesheet" href="assets/css/create-account.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background-overlay"></div>
    
    <main class="container">
        <a href="index.php" class="back-home">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>

        <div class="registration-card">
            <h1>Create Account</h1>
            <p class="welcome-text">Join Annapurna Hotel for exclusive benefits</p>

            <?php
            session_start();
            if (isset($_SESSION['reg_errors'])): ?>
                <div style="background: var(--color-cancelled-bg); color: var(--color-cancelled-text); padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?php foreach ($_SESSION['reg_errors'] as $error): ?>
                        <p>• <?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php 
                unset($_SESSION['reg_errors']);
            endif; 
            
            $formData = isset($_SESSION['reg_form_data']) ? $_SESSION['reg_form_data'] : [];
            unset($_SESSION['reg_form_data']);
            ?>

            <form id="registrationForm" class="registration-form" method="POST" action="register-handler.php">
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="firstName" name="firstName" value="<?php echo isset($formData['firstName']) ? htmlspecialchars($formData['firstName']) : ''; ?>" required>
                    <label for="firstName">First Name</label>
                </div>

                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="lastName" name="lastName" value="<?php echo isset($formData['lastName']) ? htmlspecialchars($formData['lastName']) : ''; ?>" required>
                    <label for="lastName">Last Name</label>
                </div>

                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>" required>
                    <label for="email">Email Address</label>
                </div>

                <div class="form-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" id="contact" name="contact" value="<?php echo isset($formData['contact']) ? htmlspecialchars($formData['contact']) : ''; ?>" required>
                    <label for="contact">Contact Number</label>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                    <label for="password">Password</label>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('confirmPassword')"></i>
                    <label for="confirmPassword">Confirm Password</label>
                </div>

                <div class="terms-container">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms & Conditions</a></label>
                </div>

                <button type="submit" class="register-btn">
                    Create Account
                    <i class="fas fa-arrow-right"></i>
                </button>

                <p class="login-link">
                    Already have an account? <a href="login.php">Login</a>
                </p>
            </form>
        </div>

        <a href="admin" class="admin-login" title="Admin Login">
            <i class="fas fa-user-shield"></i>
        </a>
    </main>

    <script>
        // Password visibility toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Form validation
function validateForm(event) {
    event.preventDefault();
    
    const fullName = document.getElementById('fullName').value;
    const email = document.getElementById('email').value;
    const contact = document.getElementById('contact').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const terms = document.getElementById('terms').checked;

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('Please enter a valid email address');
        return false;
    }

    // Contact number validation
    const contactRegex = /^\d{10}$/;
    if (!contactRegex.test(contact)) {
        showError('Please enter a valid 10-digit contact number');
        return false;
    }

    // Password validation
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!passwordRegex.test(password)) {
        showError('Password must contain at least 8 characters, including uppercase, lowercase, number and special character');
        return false;
    }

    // Confirm password validation
    if (password !== confirmPassword) {
        showError('Passwords do not match');
        return false;
    }

    // Terms validation
    if (!terms) {
        showError('Please agree to the Terms & Conditions');
        return false;
    }

    // If all validations pass, show success message and redirect
    showSuccess();
    return false;
}

// Error message display
function showError(message) {
    const existingError = document.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.cssText = `
        color: var(--color-cancelled-text);
        background: var(--color-cancelled-bg);
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: center;
    `;
    errorDiv.textContent = message;

    const form = document.getElementById('registrationForm');
    form.insertBefore(errorDiv, form.firstChild);

    // Remove error message after 3 seconds
    setTimeout(() => {
        errorDiv.remove();
    }, 3000);
}

// Success message and redirect
function showSuccess() {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.style.cssText = `
        color: var(--color-confirmed-text);
        background: var(--color-confirmed-bg);
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: center;
    `;
    successDiv.textContent = 'Registration successful! Redirecting to login page...';

    const form = document.getElementById('registrationForm');
    form.insertBefore(successDiv, form.firstChild);

    // Redirect to login page after 2 seconds
    setTimeout(() => {
        window.location.href = 'login.html';
    }, 2000);
}

// Add floating label effect
document.querySelectorAll('.form-group input').forEach(input => {
    input.addEventListener('input', function() {
        if (this.value) {
            this.classList.add('has-value');
        } else {
            this.classList.remove('has-value');
        }
    });
});
    </script>
    <script src="createAccount.js"></script>
</body>
</html>