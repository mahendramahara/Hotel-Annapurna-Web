<?php
/**
 * Tables Seeder
 * Creates 20 dining tables for Hotel Annapurna restaurant
 */

require_once __DIR__ . '/../config/db.php';

function seedTables($conn) {
    echo "<h3>🪑 Seeding Dining Tables...</h3>";
    
    $tables = [
        // Ground Floor - Inside
        ['T-101', 2, 'available', 500.00, 450.00, 'ground floor', 'Cozy corner table perfect for couples'],
        ['T-102', 4, 'available', 800.00, 750.00, 'ground floor', 'Family-friendly table near the window'],
        ['T-103', 4, 'available', 800.00, 750.00, 'ground floor', 'Comfortable seating with garden view'],
        ['T-104', 6, 'available', 1200.00, 1100.00, 'ground floor', 'Large table ideal for small gatherings'],
        ['T-105', 2, 'booked', 500.00, 450.00, 'ground floor', 'Intimate setting by the indoor fountain'],
        
        // Ground Floor - Outside
        ['T-106', 4, 'available', 850.00, 800.00, 'outside', 'Outdoor patio seating with mountain views'],
        ['T-107', 4, 'available', 850.00, 800.00, 'outside', 'Garden terrace table under the pergola'],
        ['T-108', 6, 'available', 1250.00, 1150.00, 'outside', 'Spacious outdoor table for family dining'],
        ['T-109', 2, 'available', 550.00, 500.00, 'outside', 'Romantic balcony seating'],
        ['T-110', 8, 'reserved', 1600.00, 1500.00, 'outside', 'Premium outdoor table for special occasions'],
        
        // First Floor - Inside
        ['T-201', 2, 'available', 600.00, 550.00, 'first floor', 'Private booth with cushioned seating'],
        ['T-202', 4, 'available', 900.00, 850.00, 'first floor', 'Executive table with city views'],
        ['T-203', 4, 'available', 900.00, 850.00, 'first floor', 'Elegant setting near the fireplace'],
        ['T-204', 6, 'available', 1300.00, 1200.00, 'first floor', 'Premium table in VIP section'],
        ['T-205', 8, 'available', 1700.00, 1600.00, 'first floor', 'Large family table with traditional décor'],
        
        // First Floor - Special Tables
        ['T-206', 10, 'available', 2000.00, 1900.00, 'first floor', 'Banquet table for celebrations and events'],
        ['T-207', 4, 'maintenance', 900.00, 850.00, 'first floor', 'Temporarily under maintenance'],
        ['T-208', 6, 'available', 1350.00, 1250.00, 'first floor', 'Corner table with panoramic windows'],
        ['T-209', 2, 'available', 650.00, 600.00, 'first floor', 'Luxury private dining nook'],
        ['T-210', 12, 'available', 2500.00, 2300.00, 'first floor', 'Grand table for large parties and business meetings'],
        
        // Additional Ground Floor Tables
        ['T-111', 4, 'available', 850.00, 800.00, 'ground floor', 'Modern table with ambient lighting'],
        ['T-112', 2, 'available', 520.00, 480.00, 'ground floor', 'Quiet corner for intimate dining'],
        ['T-113', 6, 'available', 1220.00, 1120.00, 'ground floor', 'Family table near the buffet area'],
        ['T-114', 4, 'booked', 880.00, 830.00, 'ground floor', 'Premium spot with central location'],
        ['T-115', 8, 'available', 1650.00, 1550.00, 'ground floor', 'Large table for group celebrations'],
        
        // Additional Outside Tables
        ['T-116', 2, 'available', 580.00, 530.00, 'outside', 'Rooftop seating with sunset views'],
        ['T-117', 4, 'available', 900.00, 850.00, 'outside', 'Garden table surrounded by flowers'],
        ['T-118', 6, 'reserved', 1280.00, 1200.00, 'outside', 'Premium terrace spot for special occasions'],
        ['T-119', 4, 'available', 920.00, 870.00, 'outside', 'Poolside dining table'],
        ['T-120', 10, 'available', 1850.00, 1750.00, 'outside', 'Large patio table for parties'],
        
        // Additional First Floor Tables
        ['T-211', 4, 'available', 950.00, 900.00, 'first floor', 'Business meeting table with projector access'],
        ['T-212', 2, 'available', 680.00, 630.00, 'first floor', 'Romantic corner with dim lighting'],
        ['T-213', 6, 'available', 1380.00, 1280.00, 'first floor', 'VIP section with exclusive service'],
        ['T-214', 8, 'available', 1750.00, 1650.00, 'first floor', 'Premium dining with mountain views'],
        ['T-215', 4, 'maintenance', 900.00, 850.00, 'first floor', 'Under renovation for upgrades'],
        ['T-216', 14, 'available', 2800.00, 2600.00, 'first floor', 'Grand banquet table for large events'],
        ['T-217', 2, 'available', 700.00, 650.00, 'first floor', 'Executive booth with privacy screen'],
        ['T-218', 6, 'available', 1400.00, 1300.00, 'first floor', 'Corner table with wine cellar view'],
        ['T-219', 4, 'available', 980.00, 930.00, 'first floor', 'Modern table with USB charging ports'],
        ['T-220', 10, 'available', 2100.00, 2000.00, 'first floor', 'Celebration table with decoration service'],
    ];
    
    $success = 0;
    $skipped = 0;
    
    foreach ($tables as $table) {
        // Check if table already exists
        $check = $conn->prepare("SELECT id FROM tables WHERE table_no = ?");
        $check->bind_param("s", $table[0]);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            echo "<div class='warning'>⚠️ Table <strong>{$table[0]}</strong> already exists - skipped</div>";
            $skipped++;
            continue;
        }
        
        $stmt = $conn->prepare("INSERT INTO tables (table_no, total_chairs, booking_status, price_main, price_today, location, short_description, image_path) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $image_path = "images/tables/demoTable.jpg";
        
        $stmt->bind_param(
            "sissdsss",
            $table[0], // table_no
            $table[1], // total_chairs
            $table[2], // booking_status
            $table[3], // price_main
            $table[4], // price_today
            $table[5], // location
            $table[6], // short_description
            $image_path
        );
        
        if ($stmt->execute()) {
            echo "<div class='success'>✅ Created table: {$table[0]} ({$table[1]} chairs) - {$table[5]} - Rs. {$table[3]}</div>";
            $success++;
        } else {
            echo "<div class='error'>❌ Error creating table {$table[0]}: " . $stmt->error . "</div>";
        }
        
        $stmt->close();
        $check->close();
    }
    
    echo "<div class='info'>📊 Tables: $success created, $skipped skipped</div>";
    return $success;
}
