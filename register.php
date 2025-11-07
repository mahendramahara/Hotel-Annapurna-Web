<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Annapurna Hotel and Restaurant</title>
    <link rel="stylesheet" href="createAccount.css">
    <!-- Font Awesome CDN -->
     <style>
        :root {
    /* General Colors */
    --primary-color: #1891d1;
    --primary-dark: #1d64c2;
    --background-color: #e6f2ff;
    --text-dark: #002960;
    --white: #ffffff;

    /* Box Colors */
    --box1-color: #ffd39a;
    --box2-color: #c1d5a4;
    --box3-color: #E2D9FF;
    --box4-color: #99BEBC;

    /* Table Stripes */
    --table-stripe: #ceecf6;

    /* Status Colors */
    --color-pending-bg: #fff3cd;
    --color-pending-text: #856404;
    --color-confirmed-bg: #d4edda;
    --color-confirmed-text: #155724;
    --color-completed-bg: #cce5ff;
    --color-completed-text: #004085;
    --color-cancelled-bg: #f8d7da;
    --color-cancelled-text: #721c24;

    /* Button Colors */
    --color-button-success: #28a745;
    --color-button-danger: #dc3545;
}
        /* Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    min-height: 100vh;
    background-image: url('hotel-background.jpg');
    background-size: cover;
    background-position: center;
    position: relative;
}

.background-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(8px);
    z-index: 1;
}

.container {
    position: relative;
    z-index: 2;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

/* Registration Card Styles */
.registration-card {
    background: var(--white);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

h1 {
    color: var(--text-dark);
    text-align: center;
    margin-bottom: 10px;
    font-size: 2rem;
}

.welcome-text {
    text-align: center;
    color: #666;
    margin-bottom: 30px;
}

/* Form Styles */
.form-group {
    position: relative;
    margin-bottom: 25px;
}

.form-group i:not(.password-toggle) {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary-color);
    font-size: 16px;
}

.form-group input {
    width: 100%;
    padding: 12px 40px; /* Reduced padding for smaller height */
    border: 2px solid #eee;
    border-radius: 8px; /* Slightly reduced border radius */
    font-size: 14px; /* Slightly smaller font size */
    transition: all 0.3s ease;
    height: 42px; /* Fixed height */
    line-height: 1;
}

.form-group input:focus {
    border-color: var(--primary-color);
    outline: none;
}

.form-group label {
    position: absolute;
    left: 45px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    transition: all 0.3s ease;
    pointer-events: none;
    font-size: 14px; /* Slightly smaller font size */
}

.form-group input:focus ~ label,
.form-group input:valid ~ label {
    top: -10px;
    left: 15px;
    font-size: 12px;
    background: var(--white);
    padding: 0 5px;
    color: var(--primary-color);
}

/* Password Toggle Icon Styles - Updated */
.password-toggle {
    position: absolute;
    right: 12px; /* Adjusted position */
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #999;
    font-size: 16px;
    padding: 4px;
    z-index: 2;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: var(--primary-color);
}

/* Terms & Conditions */
.terms-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 25px;
    font-size: 14px;
}

.terms-container input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
}

.terms-container a {
    color: var(--primary-color);
    text-decoration: none;
}

/* Button Styles */
.register-btn {
    width: 100%;
    padding: 12px; /* Reduced padding */
    background: var(--primary-color);
    color: var(--white);
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    height: 42px; /* Fixed height */
}

.register-btn:hover {
    background: var(--primary-dark);
}

/* Navigation Links */
.back-home {
    position: absolute;
    top: 20px;
    left: 20px;
    color: var(--white);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.back-home:hover {
    transform: translateX(-5px);
}

.login-link {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
}

.login-link a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.admin-login {
    position: fixed;
    bottom: 20px;
    right: 20px;
    color: var(--white);
    opacity: 0.6;
    font-size: 20px;
    transition: all 0.3s ease;
}

.admin-login:hover {
    opacity: 1;
}

/* Responsive Design */
@media (max-width: 768px) {
    .registration-card {
        padding: 30px 20px;
    }

    h1 {
        font-size: 1.75rem;
    }

    .form-group input {
        padding: 10px 35px;
        height: 40px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 10px;
    }

    .back-home {
        font-size: 14px;
    }

    .registration-card {
        padding: 20px 15px;
    }

    h1 {
        font-size: 1.5rem;
    }

    .form-group input {
        font-size: 13px;
        height: 38px;
    }
}
     </style>
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

            <form id="registrationForm" class="registration-form" onsubmit="return validateForm(event)">
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="fullName" required>
                    <label for="fullName">Full Name</label>
                </div>

                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" required>
                    <label for="email">Email Address</label>
                </div>

                <div class="form-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" id="contact" required>
                    <label for="contact">Contact Number</label>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                    <label for="password">Password</label>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirmPassword" required>
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