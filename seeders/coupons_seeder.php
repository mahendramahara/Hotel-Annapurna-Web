<?php
/**
 * Coupons Seeder
 * Creates 20 promotional coupons for Hotel Annapurna
 */

require_once __DIR__ . '/../config/db.php';

function seedCoupons($conn) {
    echo "<h3>🎟️ Seeding Coupons...</h3>";
    
    $coupons = [
        ['WELCOME10', 'percentage', 10.00, 500.00, 100.00, 100, '2024-12-01', '2025-12-31', 'active'],
        ['NEWUSER20', 'percentage', 20.00, 1000.00, 200.00, 50, '2024-12-01', '2025-06-30', 'active'],
        ['SAVE50', 'fixed', 50.00, 300.00, null, 200, '2024-12-01', '2025-12-31', 'active'],
        ['FOOD15', 'percentage', 15.00, 500.00, 150.00, 150, '2024-12-01', '2025-12-31', 'active'],
        ['WEEKEND25', 'percentage', 25.00, 1500.00, 300.00, 75, '2024-12-01', '2025-12-31', 'active'],
        ['ROOM100', 'fixed', 100.00, 2000.00, null, 80, '2024-12-01', '2025-12-31', 'active'],
        ['FAMILY30', 'percentage', 30.00, 2500.00, 500.00, 60, '2024-12-01', '2025-12-31', 'active'],
        ['DASHAIN50', 'fixed', 50.00, 500.00, null, 200, '2024-12-01', '2025-12-31', 'active'],
        ['TIHAR100', 'fixed', 100.00, 1000.00, null, 150, '2024-12-01', '2025-12-31', 'active'],
        ['VIP500', 'fixed', 500.00, 5000.00, null, 20, '2024-12-01', '2025-12-31', 'active'],
        ['EARLYBIRD', 'percentage', 35.00, 3000.00, 700.00, 40, '2024-12-01', '2025-03-31', 'active'],
        ['LOYALTY40', 'percentage', 40.00, 2000.00, 600.00, 30, '2024-12-01', '2025-12-31', 'active'],
        ['GROUPDEAL', 'percentage', 20.00, 3000.00, 500.00, 50, '2024-12-01', '2025-12-31', 'active'],
        ['MIDWEEK15', 'percentage', 15.00, 800.00, 150.00, 120, '2024-12-01', '2025-12-31', 'active'],
        ['BIRTHDAY50', 'fixed', 50.00, 500.00, null, 100, '2024-12-01', '2025-12-31', 'active'],
        ['ANNIVERSARY', 'percentage', 25.00, 2000.00, 400.00, 50, '2024-12-01', '2025-12-31', 'active'],
        ['STUDENT20', 'percentage', 20.00, 500.00, 100.00, 200, '2024-12-01', '2025-12-31', 'active'],
        ['CORPORATE', 'percentage', 30.00, 5000.00, 1000.00, 25, '2024-12-01', '2025-12-31', 'active'],
        ['FESTIVE200', 'fixed', 200.00, 2000.00, null, 60, '2024-12-01', '2025-01-31', 'active'],
        ['MEGA50OFF', 'percentage', 50.00, 10000.00, 2000.00, 10, '2024-12-01', '2025-01-15', 'active'],
        
        // Additional Coupons
        ['SUPERSAVE', 'percentage', 45.00, 4000.00, 800.00, 35, '2024-12-01', '2025-06-30', 'active'],
        ['FLASH100', 'fixed', 100.00, 800.00, null, 100, '2024-12-01', '2025-03-31', 'active'],
        ['HOTEL10', 'percentage', 10.00, 300.00, 50.00, 250, '2024-12-01', '2025-12-31', 'active'],
        ['SENIOR25', 'percentage', 25.00, 1000.00, 300.00, 80, '2024-12-01', '2025-12-31', 'active'],
        ['REFERRAL150', 'fixed', 150.00, 1500.00, null, 70, '2024-12-01', '2025-12-31', 'active'],
        ['NEWYEAR300', 'fixed', 300.00, 3000.00, null, 40, '2024-12-01', '2025-01-31', 'active'],
        ['REPEAT30', 'percentage', 30.00, 1500.00, 400.00, 90, '2024-12-01', '2025-12-31', 'active'],
        ['LUNCH20', 'percentage', 20.00, 600.00, 120.00, 150, '2024-12-01', '2025-12-31', 'active'],
        ['DINNER25', 'percentage', 25.00, 1200.00, 250.00, 120, '2024-12-01', '2025-12-31', 'active'],
        ['BREAKFAST15', 'percentage', 15.00, 400.00, 60.00, 180, '2024-12-01', '2025-12-31', 'active'],
        ['SUITE20', 'percentage', 20.00, 6000.00, 1000.00, 25, '2024-12-01', '2025-12-31', 'active'],
        ['DELUXE15', 'percentage', 15.00, 4000.00, 600.00, 40, '2024-12-01', '2025-12-31', 'active'],
        ['VALENTINE200', 'fixed', 200.00, 2500.00, null, 50, '2024-12-01', '2025-02-28', 'active'],
        ['MONSOON35', 'percentage', 35.00, 2000.00, 500.00, 60, '2024-12-01', '2025-09-30', 'active'],
        ['SUMMER40', 'percentage', 40.00, 3500.00, 800.00, 45, '2024-12-01', '2025-08-31', 'active'],
        ['WINTER30', 'percentage', 30.00, 2500.00, 600.00, 55, '2024-12-01', '2025-03-31', 'active'],
        ['MILITARY20', 'percentage', 20.00, 1000.00, 200.00, 100, '2024-12-01', '2025-12-31', 'active'],
        ['FIRSTTIME25', 'percentage', 25.00, 800.00, 180.00, 200, '2024-12-01', '2025-12-31', 'active'],
        ['PLATINUM50', 'percentage', 50.00, 8000.00, 1500.00, 15, '2024-12-01', '2025-12-31', 'active'],
        ['GOLD35', 'percentage', 35.00, 5000.00, 1000.00, 30, '2024-12-01', '2025-12-31', 'active'],
    ];
    
    $success = 0;
    $skipped = 0;
    
    foreach ($coupons as $coupon) {
        // Check if coupon already exists
        $check = $conn->prepare("SELECT id FROM coupons WHERE code = ?");
        $check->bind_param("s", $coupon[0]);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            echo "<div class='warning'>⚠️ Coupon <strong>{$coupon[0]}</strong> already exists - skipped</div>";
            $skipped++;
            continue;
        }
        
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount_type, discount_value, min_purchase, max_discount, usage_limit, used_count, valid_from, valid_until, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $used_count = 0;
        $valid_from = $coupon[6] . ' 00:00:00';
        $valid_until = $coupon[7] . ' 23:59:59';
        
        $stmt->bind_param(
            "ssdddiisss",
            $coupon[0], // code
            $coupon[1], // discount_type
            $coupon[2], // discount_value
            $coupon[3], // min_purchase
            $coupon[4], // max_discount
            $coupon[5], // usage_limit
            $used_count,
            $valid_from,
            $valid_until,
            $coupon[8]  // status
        );
        
        if ($stmt->execute()) {
            $discount_display = $coupon[1] == 'percentage' ? $coupon[2] . '%' : 'Rs. ' . $coupon[2];
            echo "<div class='success'>✅ Created coupon: {$coupon[0]} - {$discount_display} off</div>";
            $success++;
        } else {
            echo "<div class='error'>❌ Error creating coupon {$coupon[0]}: " . $stmt->error . "</div>";
        }
        
        $stmt->close();
        $check->close();
    }
    
    echo "<div class='info'>📊 Coupons: $success created, $skipped skipped</div>";
    return $success;
}
