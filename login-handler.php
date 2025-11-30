<?php
session_start();
require_once 'config/db.php';
require_once 'includes/activity-logger.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validation
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Please fill in all fields";
        header("Location: login.php");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "Invalid email address";
        header("Location: login.php");
        exit();
    }
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: login.php");
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    // Check if account is verified
    if ($user['status'] !== 'verified') {
        $_SESSION['login_error'] = "Please verify your email address before logging in";
        header("Location: login.php");
        exit();
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: login.php");
        exit();
    }
    
    // Login successful - Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_first_name'] = $user['first_name'];
    $_SESSION['user_last_name'] = $user['last_name'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in'] = true;
    
    // Log activity
    logActivity($conn, $user['id'], 'login', 'logged in to the system');
    
    // Set cookie if "Remember me" is checked (30 days)
    if ($remember) {
        $cookie_value = base64_encode($user['id'] . '|' . $user['email']);
        setcookie('user_auth', $cookie_value, time() + (30 * 24 * 60 * 60), '/');
    }
    
    // Redirect based on role
    if ($user['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// If accessed directly, redirect to login page
header("Location: login.php");
exit();
