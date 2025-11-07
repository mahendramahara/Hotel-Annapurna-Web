<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Annapurna Hotel & Restaurant</title>
    <link rel="stylesheet" href="login-styles.css">
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
        .login-body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    height: 100vh;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)),
                url('https://source.unsplash.com/1920x1080/?luxury-hotel') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.login-container {
    width: 100%;
    max-width: 400px;
    padding: 20px;
    position: relative;
}

.login-card {
    background: var(--white);
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    animation: login-card-appear 0.5s ease;
}

.login-header {
    text-align: center;
    margin-bottom: 28px;
}

.login-header h1 {
    margin: 0;
    color: var(--text-dark);
    font-size: 26px;
    font-weight: 600;
}

.login-header p {
    margin: 8px 0 0;
    color: var(--text-dark);
    opacity: 0.8;
    font-size: 15px;
}

.login-input-group {
    margin-bottom: 20px;
}

.login-input-group label {
    display: block;
    margin-bottom: 6px;
    color: var(--text-dark);
    font-size: 14px;
    font-weight: 500;
}

.login-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.login-input-icon {
    position: absolute;
    left: 12px;
    color: var(--text-dark);
    opacity: 0.6;
    font-size: 18px;
}

.login-input-wrapper input {
    width: 100%;
    padding: 12px 12px 12px 40px;
    border: 1.5px solid var(--background-color);
    border-radius: 8px;
    font-size: 14px;
    color: var(--text-dark);
    transition: all 0.3s ease;
}

.login-input-wrapper input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(24, 145, 209, 0.2);
    outline: none;
}

.login-toggle-password {
    position: absolute;
    right: 12px;
    cursor: pointer;
    color: var(--text-dark);
    opacity: 0.6;
    font-size: 18px;
    transition: opacity 0.3s ease;
}

.login-toggle-password:hover {
    opacity: 1;
}

.login-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.login-remember {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    color: var(--text-dark);
    font-size: 14px;
}

.login-remember input[type="checkbox"] {
    accent-color: var(--primary-color);
    width: 16px;
    height: 16px;
}

.login-forgot {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s ease;
}

.login-forgot:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

.login-button {
    width: 100%;
    padding: 12px;
    background: var(--primary-color);
    color: var(--white);
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background-color 0.3s ease, transform 0.1s ease;
}

.login-button:hover {
    background: var(--primary-dark);
}

.login-button:active {
    transform: scale(0.98);
}

.login-button ion-icon {
    font-size: 18px;
}

.login-signup {
    text-align: center;
    margin-top: 20px;
    margin-bottom: 0;
    color: var(--text-dark);
    font-size: 14px;
}

.login-signup a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.login-signup a:hover {
    text-decoration: underline;
}

.login-back-home {
    position: absolute;
    top: -40px;
    left: 20px;
    color: var(--white);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    transition: opacity 0.3s ease;
}

.login-back-home ion-icon {
    font-size: 18px;
}

.login-back-home:hover {
    opacity: 0.8;
}

.login-admin-link {
    position: absolute;
    bottom: -40px;
    right: 20px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 12px;
    transition: color 0.3s ease;
}

.login-admin-link:hover {
    color: var(--white);
}

@keyframes login-card-appear {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .login-container {
        padding: 16px;
    }

    .login-card {
        padding: 24px;
    }
}

@media (max-width: 480px) {
    .login-card {
        padding: 20px;
    }

    .login-header h1 {
        font-size: 24px;
    }

    .login-header p {
        font-size: 14px;
    }

    .login-input-wrapper input {
        padding: 10px 10px 10px 36px;
    }

    .login-options {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }

    .login-back-home {
        top: -35px;
    }

    .login-admin-link {
        bottom: -35px;
    }
}

@media (max-height: 600px) {
    .login-container {
        padding: 10px;
    }

    .login-card {
        padding: 16px;
    }

    .login-header {
        margin-bottom: 16px;
    }

    .login-input-group {
        margin-bottom: 12px;
    }
}
    </style>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body class="login-body">
    <div class="login-container">
        <a href="index.php" class="login-back-home">
            <ion-icon name="arrow-back-outline"></ion-icon>
            Back to Home
        </a>
        
        <div class="login-card">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>
            </div>

            <form class="login-form" id="loginForm">
                <div class="login-input-group">
                    <label for="email">Email / Username</label>
                    <div class="login-input-wrapper">
                        <ion-icon name="person-outline" class="login-input-icon"></ion-icon>
                        <input type="email" id="email" required 
                               placeholder="Enter your email or username">
                    </div>
                </div>

                <div class="login-input-group">
                    <label for="password">Password</label>
                    <div class="login-input-wrapper">
                        <ion-icon name="lock-closed-outline" class="login-input-icon"></ion-icon>
                        <input type="password" id="password" required 
                               placeholder="Enter your password">
                        <ion-icon name="eye-off-outline" class="login-toggle-password" id="togglePassword"></ion-icon>
                    </div>
                </div>

                <div class="login-options">
                    <label class="login-remember">
                        <input type="checkbox" id="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="forgot-password.php" class="login-forgot">Forgot Password?</a>
                </div>

                <button type="submit" class="login-button">
                    <span>Sign In</span>
                    <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>

                <p class="login-signup">
                    Don't have an account? 
                    <a href="register.php">Create one</a>
                </p>
            </form>
        </div>

        <a href="admin" class="login-admin-link">
            Login as Admin
        </a>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.setAttribute('name', 'eye-outline');
            } else {
                passwordInput.type = 'password';
                icon.setAttribute('name', 'eye-off-outline');
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                alert('Please fill in all fields');
                return;
            }

            if (password.length < 6) {
                alert('Password must be at least 6 characters long');
                return;
            }

            console.log('Form submitted:', { email, password });
        });
    </script>
</body>
</html>