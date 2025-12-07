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
