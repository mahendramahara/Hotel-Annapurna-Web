<?php
/**
 * Main Database Seeder
 * Hotel Annapurna - Comprehensive Data Seeding Script
 * 
 * This script runs all seeder files to populate the database with demo data.
 * Run this AFTER database_setup.php has created all tables.
 * 
 * Usage: Navigate to http://localhost/github/Hotel-Annapurna-Web/seed_database.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection FIRST
require_once __DIR__ . '/config/db.php';

// Then include all seeder files
require_once __DIR__ . '/seeders/users_seeder.php';
require_once __DIR__ . '/seeders/food_items_seeder.php';
require_once __DIR__ . '/seeders/tables_seeder.php';
require_once __DIR__ . '/seeders/rooms_seeder.php';
require_once __DIR__ . '/seeders/blogs_seeder.php';
require_once __DIR__ . '/seeders/coupons_seeder.php';

// HTML Header
echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Seeder - Hotel Annapurna</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            max-width: 1200px; 
            margin: 50px auto; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2); 
        }
        h1 { 
            color: #2c3e50; 
            border-bottom: 4px solid #3498db; 
            padding-bottom: 15px; 
            margin-bottom: 30px;
            font-size: 32px;
        }
        h2 { 
            color: #34495e; 
            margin-top: 40px; 
            font-size: 24px;
            border-left: 5px solid #3498db;
            padding-left: 15px;
        }
        h3 {
            color: #2c3e50;
            margin-top: 25px;
            font-size: 20px;
        }
        .success { 
            color: #27ae60; 
            padding: 12px 15px; 
            background: #d5f4e6; 
            border-left: 5px solid #27ae60; 
            margin: 8px 0; 
            border-radius: 4px;
            font-size: 14px;
        }
        .error { 
            color: #e74c3c; 
            padding: 12px 15px; 
            background: #fadbd8; 
            border-left: 5px solid #e74c3c; 
            margin: 8px 0; 
            border-radius: 4px;
            font-size: 14px;
        }
        .warning { 
            color: #f39c12; 
            padding: 12px 15px; 
            background: #fcf3cf; 
            border-left: 5px solid #f39c12; 
            margin: 8px 0; 
            border-radius: 4px;
            font-size: 14px;
        }
        .info { 
            color: #3498db; 
            padding: 12px 15px; 
            background: #d6eaf8; 
            border-left: 5px solid #3498db; 
            margin: 8px 0; 
            border-radius: 4px;
            font-weight: bold;
            font-size: 15px;
        }
        .summary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px; 
            border-radius: 10px; 
            margin-top: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        .summary h2 {
            color: white;
            border-left: 5px solid white;
        }
        .summary p {
            font-size: 18px;
            margin: 10px 0;
        }
        .btn { 
            display: inline-block; 
            padding: 15px 30px; 
            background: #3498db; 
            color: white; 
            text-decoration: none; 
            border-radius: 8px; 
            margin: 10px 10px 10px 0; 
            font-weight: bold;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .btn:hover { 
            background: #2980b9; 
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }
        .btn-success {
            background: #27ae60;
        }
        .btn-success:hover {
            background: #229954;
        }
        .progress-section {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .header-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header-banner h1 {
            color: white;
            border: none;
            margin: 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #e0e0e0;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #3498db;
        }
        .stat-label {
            color: #7f8c8d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class='container'>";

echo "<div class='header-banner'>";
echo "<h1>🌟 Hotel Annapurna Database Seeder 🌟</h1>";
echo "<p>Populating your database with realistic demo data</p>";
echo "</div>";

echo "<div class='info'>⏰ Started at: " . date('Y-m-d H:i:s') . "</div>";
echo "<div class='warning'>⚠️ <strong>Important:</strong> Make sure you have run database_setup.php first to create all tables.</div>";

// Initialize counters
$total_created = 0;
$total_skipped = 0;
$start_time = microtime(true);

// =============================================================================
// Run All Seeders
// =============================================================================

echo "<h2>🚀 Starting Data Seeding Process</h2>";

// Initialize individual counters
$users_count = 0;
$food_count = 0;
$tables_count = 0;
$rooms_count = 0;
$blogs_count = 0;
$coupons_count = 0;

// 1. Seed Users
echo "<div class='progress-section'>";
try {
    $users_count = seedUsers($conn);
    $total_created += $users_count;
} catch (Exception $e) {
    echo "<div class='error'>❌ Error seeding users: " . $e->getMessage() . "</div>";
    $users_count = 0;
}
echo "</div>";

// 2. Seed Food Items
echo "<div class='progress-section'>";
try {
    $food_count = seedFoodItems($conn);
    $total_created += $food_count;
} catch (Exception $e) {
    echo "<div class='error'>❌ Error seeding food items: " . $e->getMessage() . "</div>";
    $food_count = 0;
}
echo "</div>";

// 3. Seed Tables
echo "<div class='progress-section'>";
try {
    $tables_count = seedTables($conn);
    $total_created += $tables_count;
} catch (Exception $e) {
    echo "<div class='error'>❌ Error seeding tables: " . $e->getMessage() . "</div>";
    $tables_count = 0;
}
echo "</div>";

// 4. Seed Rooms
echo "<div class='progress-section'>";
try {
    $rooms_count = seedRooms($conn);
    $total_created += $rooms_count;
} catch (Exception $e) {
    echo "<div class='error'>❌ Error seeding rooms: " . $e->getMessage() . "</div>";
    $rooms_count = 0;
}
echo "</div>";

// 5. Seed Blogs
echo "<div class='progress-section'>";
try {
    $blogs_count = seedBlogs($conn);
    $total_created += $blogs_count;
} catch (Exception $e) {
    echo "<div class='error'>❌ Error seeding blogs: " . $e->getMessage() . "</div>";
    $blogs_count = 0;
}
echo "</div>";

// 6. Seed Coupons
echo "<div class='progress-section'>";
try {
    $coupons_count = seedCoupons($conn);
    $total_created += $coupons_count;
} catch (Exception $e) {
    echo "<div class='error'>❌ Error seeding coupons: " . $e->getMessage() . "</div>";
    $coupons_count = 0;
}
echo "</div>";

// Calculate execution time
$end_time = microtime(true);
$execution_time = round($end_time - $start_time, 2);

// =============================================================================
// Final Summary
// =============================================================================

echo "<div class='summary'>";
echo "<h2>📊 Seeding Summary</h2>";

echo "<div class='stats-grid'>";
echo "<div class='stat-card'><div class='stat-number'>{$users_count}</div><div class='stat-label'>Users</div></div>";
echo "<div class='stat-card'><div class='stat-number'>{$food_count}</div><div class='stat-label'>Food Items</div></div>";
echo "<div class='stat-card'><div class='stat-number'>{$tables_count}</div><div class='stat-label'>Tables</div></div>";
echo "<div class='stat-card'><div class='stat-number'>{$rooms_count}</div><div class='stat-label'>Rooms</div></div>";
echo "<div class='stat-card'><div class='stat-number'>{$blogs_count}</div><div class='stat-label'>Blogs</div></div>";
echo "<div class='stat-card'><div class='stat-number'>{$coupons_count}</div><div class='stat-label'>Coupons</div></div>";
echo "</div>";

echo "<p><strong>✅ Total Records Created:</strong> {$total_created}</p>";
echo "<p><strong>⏱️ Execution Time:</strong> {$execution_time} seconds</p>";
echo "<p><strong>🕐 Completed At:</strong> " . date('Y-m-d H:i:s') . "</p>";

if ($total_created > 0) {
    echo "<div style='background: rgba(255,255,255,0.2); padding: 20px; border-radius: 8px; margin-top: 20px;'>";
    echo "<h3 style='color: white; margin-top: 0;'>🎉 Database Seeding Completed Successfully!</h3>";
    echo "<p style='font-size: 16px;'>Your database is now populated with realistic demo data. You can start testing the application.</p>";
    echo "<p style='font-size: 14px; margin-top: 15px;'><strong>Default Login Credentials:</strong></p>";
    echo "<ul style='font-size: 14px;'>";
    echo "<li><strong>Admin:</strong> admin@hotelannapurna.com / 12345678</li>";
    echo "<li><strong>Staff:</strong> krishna.chef@hotelannapurna.com / 12345678</li>";
    echo "<li><strong>Customer:</strong> rajesh.kumar@gmail.com / 12345678</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: rgba(255,255,255,0.2); padding: 20px; border-radius: 8px; margin-top: 20px;'>";
    echo "<h3 style='color: white; margin-top: 0;'>⚠️ No New Records Created</h3>";
    echo "<p>All data already exists in the database. If you want to re-seed, please clear the existing data first.</p>";
    echo "</div>";
}

echo "<h3 style='color: white;'>🔗 Quick Links</h3>";
echo "<a href='index.php' class='btn btn-success'>🌐 Visit Website</a>";
echo "<a href='admin/index.php' class='btn'>🏠 Admin Dashboard</a>";
echo "<a href='login.php' class='btn'>👤 Customer Login</a>";
echo "<a href='register.php' class='btn'>📝 Register</a>";
echo "</div>";

echo "</div></body></html>";

$conn->close();
?>
