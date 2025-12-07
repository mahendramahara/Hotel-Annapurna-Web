<?php
/**
 * Complete Database Setup & Migration Script
 * Hotel Annapurna - All-in-One Database Initialization
 * 
 * This script creates all necessary tables with proper structure,
 * indexes, and relationships for easy migration and deployment.
 */

require_once __DIR__ . '/config/db.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Setup - Hotel Annapurna</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        h2 { color: #34495e; margin-top: 30px; }
        .success { color: #27ae60; padding: 10px; background: #d5f4e6; border-left: 4px solid #27ae60; margin: 10px 0; }
        .error { color: #e74c3c; padding: 10px; background: #fadbd8; border-left: 4px solid #e74c3c; margin: 10px 0; }
        .warning { color: #f39c12; padding: 10px; background: #fcf3cf; border-left: 4px solid #f39c12; margin: 10px 0; }
        .info { color: #3498db; padding: 10px; background: #d6eaf8; border-left: 4px solid #3498db; margin: 10px 0; }
        .summary { background: #ecf0f1; padding: 20px; border-radius: 5px; margin-top: 30px; }
        .btn { display: inline-block; padding: 12px 24px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #2980b9; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🗄️ Hotel Annapurna - Database Setup</h1>";
echo "<p>This script will create all necessary database tables with proper structure and indexes.</p>";

$tables_created = 0;
$tables_existed = 0;
$errors = 0;

// =============================================================================
// 1. USERS TABLE
// =============================================================================
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contact VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    role ENUM('admin','staff','customer') DEFAULT 'customer',
    status ENUM('pending','verified','suspended') DEFAULT 'pending',
    salary DECIMAL(10,2) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 2. FOOD ITEMS TABLE
// =============================================================================
$sql_food_items = "CREATE TABLE IF NOT EXISTS food_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category ENUM('veg','non-veg','special') NOT NULL,
    food_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    discount_price DECIMAL(10,2) DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    available_days VARCHAR(200) DEFAULT 'All Days',
    short_description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 3. TABLES (DINING TABLES)
// =============================================================================
$sql_tables = "CREATE TABLE IF NOT EXISTS tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) DEFAULT NULL,
    table_no VARCHAR(50) NOT NULL UNIQUE,
    total_chairs INT NOT NULL,
    booking_status ENUM('available','booked','reserved','maintenance','occupied') DEFAULT 'available',
    price_main DECIMAL(10,2) NOT NULL,
    price_today DECIMAL(10,2) DEFAULT NULL,
    location ENUM('ground floor','first floor','outside','inside') DEFAULT 'ground floor',
    short_description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (booking_status),
    INDEX idx_location (location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 4. ROOMS TABLE
// =============================================================================
$sql_rooms = "CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) DEFAULT NULL,
    room_no VARCHAR(50) NOT NULL UNIQUE,
    room_type ENUM('single','double','deluxe','suite') DEFAULT 'single',
    total_beds INT NOT NULL,
    bed_size ENUM('single','double','queen','king') DEFAULT 'double',
    status ENUM('available', 'booked', 'reserved', 'maintenance','occupied') DEFAULT 'available',
    price DECIMAL(10,2) NOT NULL,
    price_today DECIMAL(10,2) DEFAULT NULL,
    amenities TEXT DEFAULT NULL,
    short_description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_type (room_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 5. BLOGS TABLE
// =============================================================================
$sql_blogs = "CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    tags VARCHAR(255) DEFAULT NULL,
    content TEXT NOT NULL,
    featured_image VARCHAR(255) DEFAULT NULL,
    views INT DEFAULT 0,
    author_id INT DEFAULT NULL,
    status ENUM('draft','published','archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_category (category),
    INDEX idx_author (author_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 6. BLOG INTERACTIONS TABLE
// =============================================================================
$sql_blog_interactions = "CREATE TABLE IF NOT EXISTS blog_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    interaction_type ENUM('like','comment','share') NOT NULL,
    comment_text TEXT DEFAULT NULL,
    rating INT DEFAULT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_blog (blog_id),
    INDEX idx_user (user_id),
    INDEX idx_type (interaction_type),
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 7. ORDERS TABLE
// =============================================================================
$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_type ENUM('food','room','table') NOT NULL,
    item_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash','esewa','khalti','card') DEFAULT 'cash',
    payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
    booking_reference VARCHAR(50) DEFAULT NULL,
    status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_type (order_type),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// // 6. Orders (User Purchases - includes food orders, room bookings, table bookings)
// $sql_orders = "CREATE TABLE IF NOT EXISTS orders (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     user_id INT NOT NULL,
//     order_type ENUM('food','room','table') NOT NULL,
//     item_id INT NOT NULL,
//     item_name VARCHAR(255) NOT NULL,
//     price DECIMAL(10,2) NOT NULL,
//     quantity INT DEFAULT 1,
//     delivery_date DATE DEFAULT NULL,
//     status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
//     INDEX (user_id),
//     INDEX (order_type),
//     INDEX (status),
//     INDEX (created_at)
// )";

// =============================================================================
// 8. CART ITEMS TABLE
// =============================================================================
$sql_cart_items = "CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_type ENUM('food','room','table') NOT NULL,
    item_id INT NOT NULL,
    item_data JSON NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_type (item_type),
    INDEX idx_item (item_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 9. COUPONS TABLE
// =============================================================================
$sql_coupons = "CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_type ENUM('percentage','fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_purchase DECIMAL(10,2) DEFAULT 0,
    max_discount DECIMAL(10,2) DEFAULT NULL,
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    valid_from DATETIME NOT NULL,
    valid_until DATETIME NOT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_code (code),
    INDEX idx_status (status),
    INDEX idx_dates (valid_from, valid_until)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 10. CONTACT REQUESTS TABLE
// =============================================================================
$sql_contact_requests = "CREATE TABLE IF NOT EXISTS contact_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending','in-progress','resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 11. REGISTRATION OTPS TABLE
// =============================================================================
$sql_registration_otps = "CREATE TABLE IF NOT EXISTS registration_otps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    otp VARCHAR(6) NOT NULL,
    expiry DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    is_expired TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_expiry (expiry)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 12. PASSWORD RESETS TABLE
// =============================================================================
$sql_password_resets = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    otp VARCHAR(6) NOT NULL,
    token VARCHAR(128) NOT NULL,
    expiry DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    is_expired TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expiry (expiry)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// 13. ACTIVITY LOGS TABLE
// =============================================================================
$sql_activity_logs = "CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    activity_type ENUM('order','booking','reservation','login','logout','registration','update','delete','other') NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_type (activity_type),
    INDEX idx_created (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// =============================================================================
// EXECUTE TABLE CREATION
// =============================================================================
$tables = [
    'Users' => $sql_users,
    'Food Items' => $sql_food_items,
    'Tables' => $sql_tables,
    'Rooms' => $sql_rooms,
    'Blogs' => $sql_blogs,
    'Blog Interactions' => $sql_blog_interactions,
    'Orders' => $sql_orders,
    'Cart Items' => $sql_cart_items,
    'Coupons' => $sql_coupons,
    'Contact Requests' => $sql_contact_requests,
    'Registration OTPs' => $sql_registration_otps,
    'Password Resets' => $sql_password_resets,
    'Activity Logs' => $sql_activity_logs
];

echo "<h2>📋 Creating Database Tables</h2>";

foreach ($tables as $name => $sql) {
    // Check if table exists
    $check = $conn->query("SHOW TABLES LIKE '" . strtolower(str_replace(' ', '_', $name)) . "'");
    
    if ($check && $check->num_rows > 0) {
        echo "<div class='warning'>⚠️ Table <strong>$name</strong> already exists - skipped</div>";
        $tables_existed++;
    } else {
        if ($conn->query($sql)) {
            echo "<div class='success'>✅ Table <strong>$name</strong> created successfully</div>";
            $tables_created++;
        } else {
            echo "<div class='error'>❌ Error creating <strong>$name</strong>: " . $conn->error . "</div>";
            $errors++;
        }
    }
}

// =============================================================================
// SUMMARY
// =============================================================================
echo "<div class='summary'>";
echo "<h2>📊 Setup Summary</h2>";
echo "<p><strong>✅ Tables Created:</strong> $tables_created</p>";
echo "<p><strong>⚠️ Tables Already Existed:</strong> $tables_existed</p>";
echo "<p><strong>❌ Errors:</strong> $errors</p>";
echo "<p><strong>📦 Total Tables:</strong> " . count($tables) . "</p>";

if ($errors === 0) {
    echo "<div class='success'>";
    echo "<h3>🎉 Database Setup Completed Successfully!</h3>";
    echo "<p>All tables have been created with proper indexes and foreign keys.</p>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<h3>⚠️ Setup Completed with Errors</h3>";
    echo "<p>Please review the errors above and fix them manually.</p>";
    echo "</div>";
}

echo "<h3>🔗 Quick Links</h3>";
echo "<a href='admin/index.php' class='btn'>🏠 Admin Dashboard</a>";
echo "<a href='index.php' class='btn'>🌐 Main Website</a>";
echo "<a href='register.php' class='btn'>👤 Register User</a>";
echo "</div>";

echo "</div></body></html>";

$conn->close();
?>
