<?php
/**
 * Rooms Seeder
 * Creates 20 luxurious rooms for Hotel Annapurna
 */

require_once __DIR__ . '/../config/db.php';

function seedRooms($conn) {
    echo "<h3>🛏️ Seeding Hotel Rooms...</h3>";
    
    $rooms = [
        // Single Rooms
        ['R-101', 'single', 1, 'single', 'available', 1500.00, 1350.00, 'Free WiFi, AC, TV, Mini Fridge', 'Compact single room with modern amenities'],
        ['R-102', 'single', 1, 'single', 'available', 1500.00, 1350.00, 'Free WiFi, AC, TV, Work Desk', 'Perfect for solo business travelers'],
        ['R-103', 'single', 1, 'double', 'occupied', 1800.00, 1650.00, 'Free WiFi, AC, TV, Mini Fridge, Balcony', 'Single room with double bed and mountain view'],
        
        // Double Rooms
        ['R-201', 'double', 2, 'double', 'available', 2500.00, 2300.00, 'Free WiFi, AC, TV, Mini Fridge, Coffee Maker', 'Spacious double room with twin beds'],
        ['R-202', 'double', 2, 'queen', 'available', 2800.00, 2600.00, 'Free WiFi, AC, TV, Mini Fridge, Balcony, City View', 'Comfortable double room with queen bed'],
        ['R-203', 'double', 2, 'double', 'booked', 2500.00, 2300.00, 'Free WiFi, AC, TV, Work Area', 'Modern double room perfect for couples'],
        ['R-204', 'double', 2, 'queen', 'available', 2800.00, 2600.00, 'Free WiFi, AC, TV, Mini Bar, Mountain View', 'Elegant room with stunning views'],
        ['R-205', 'double', 2, 'king', 'available', 3200.00, 3000.00, 'Free WiFi, AC, Smart TV, Mini Bar, Balcony', 'Luxurious double room with king-size bed'],
        
        // Deluxe Rooms
        ['R-301', 'deluxe', 2, 'king', 'available', 4000.00, 3800.00, 'Free WiFi, AC, Smart TV, Mini Bar, Jacuzzi, Living Area', 'Premium deluxe room with premium amenities'],
        ['R-302', 'deluxe', 3, 'king', 'available', 4500.00, 4200.00, 'Free WiFi, AC, Smart TV, Mini Bar, Sofa Bed, Balcony', 'Deluxe room with extra bed option'],
        ['R-303', 'deluxe', 2, 'king', 'maintenance', 4000.00, 3800.00, 'Free WiFi, AC, Smart TV, Mini Bar, City View', 'Currently under renovation'],
        ['R-304', 'deluxe', 2, 'king', 'available', 4200.00, 4000.00, 'Free WiFi, AC, Smart TV, Mini Bar, Walk-in Closet', 'Deluxe room with spacious wardrobe'],
        ['R-305', 'deluxe', 2, 'king', 'available', 4300.00, 4100.00, 'Free WiFi, AC, Smart TV, Mini Bar, Garden View, Terrace', 'Deluxe room with private terrace'],
        
        // Suite Rooms
        ['R-401', 'suite', 3, 'king', 'available', 6000.00, 5700.00, 'Free WiFi, AC, Smart TV, Full Kitchen, Living Room, Dining Area', 'Executive suite perfect for long stays'],
        ['R-402', 'suite', 4, 'king', 'available', 7000.00, 6700.00, 'Free WiFi, AC, Smart TV, Kitchenette, 2 Bathrooms, Balcony', 'Family suite with two bedrooms'],
        ['R-403', 'suite', 3, 'king', 'booked', 6500.00, 6200.00, 'Free WiFi, AC, Smart TV, Mini Bar, Jacuzzi, Mountain View', 'Luxury suite with panoramic views'],
        ['R-404', 'suite', 4, 'king', 'available', 7500.00, 7200.00, 'Free WiFi, AC, Smart TV, Full Kitchen, Living Room, 2 Balconies', 'Spacious suite ideal for families'],
        ['R-501', 'suite', 5, 'king', 'available', 10000.00, 9500.00, 'Free WiFi, AC, Smart TV, Full Kitchen, 2 Bathrooms, Terrace, Butler Service', 'Presidential suite with VIP services'],
        ['R-502', 'suite', 4, 'king', 'available', 8000.00, 7600.00, 'Free WiFi, AC, Smart TV, Dining Room, Study Room, Premium Toiletries', 'Grand suite with business facilities'],
        ['R-503', 'suite', 6, 'king', 'available', 12000.00, 11500.00, 'Free WiFi, AC, Smart TV, Full Kitchen, 3 Bedrooms, 2.5 Baths, Rooftop Access', 'Penthouse suite with ultimate luxury'],
        
        // Additional Single Rooms
        ['R-104', 'single', 1, 'single', 'available', 1550.00, 1400.00, 'Free WiFi, AC, TV, Safe Box', 'Cozy single with extra security'],
        ['R-105', 'single', 1, 'double', 'available', 1850.00, 1700.00, 'Free WiFi, AC, TV, Mini Fridge, Coffee Maker', 'Single with premium amenities'],
        
        // Additional Double Rooms
        ['R-206', 'double', 2, 'queen', 'available', 2900.00, 2700.00, 'Free WiFi, AC, Smart TV, Mini Bar, Balcony', 'Double room with premium views'],
        ['R-207', 'double', 2, 'double', 'available', 2600.00, 2400.00, 'Free WiFi, AC, TV, Work Desk, Safe', 'Business traveler double room'],
        ['R-208', 'double', 2, 'king', 'booked', 3300.00, 3100.00, 'Free WiFi, AC, Smart TV, Mini Bar, Bathtub', 'Luxury double with spa bath'],
        ['R-209', 'double', 2, 'queen', 'available', 2850.00, 2650.00, 'Free WiFi, AC, TV, Coffee Maker, Garden View', 'Peaceful garden-facing room'],
        ['R-210', 'double', 2, 'king', 'available', 3400.00, 3200.00, 'Free WiFi, AC, Smart TV, Mini Bar, Sitting Area', 'Spacious double with lounge'],
        
        // Additional Deluxe Rooms
        ['R-306', 'deluxe', 2, 'king', 'available', 4400.00, 4200.00, 'Free WiFi, AC, Smart TV, Mini Bar, Jacuzzi, City Lights', 'Deluxe with skyline views'],
        ['R-307', 'deluxe', 3, 'king', 'available', 4700.00, 4500.00, 'Free WiFi, AC, Smart TV, Mini Bar, Sofa Bed, 2 Balconies', 'Deluxe suite with dual balconies'],
        ['R-308', 'deluxe', 2, 'king', 'available', 4250.00, 4050.00, 'Free WiFi, AC, Smart TV, Mini Bar, Steam Room', 'Deluxe with private steam bath'],
        ['R-309', 'deluxe', 2, 'king', 'maintenance', 4000.00, 3800.00, 'Free WiFi, AC, Smart TV, Mini Bar', 'Currently being upgraded'],
        ['R-310', 'deluxe', 3, 'king', 'available', 4800.00, 4600.00, 'Free WiFi, AC, Smart TV, Mini Bar, Office Space', 'Deluxe business suite'],
        
        // Additional Suite Rooms
        ['R-504', 'suite', 4, 'king', 'available', 8500.00, 8100.00, 'Free WiFi, AC, Smart TV, Full Kitchen, 2 Bedrooms, Gym Access', 'Family suite with fitness'],
        ['R-505', 'suite', 3, 'king', 'available', 7200.00, 6900.00, 'Free WiFi, AC, Smart TV, Kitchenette, Private Pool', 'Suite with exclusive pool'],
        ['R-506', 'suite', 5, 'king', 'available', 9500.00, 9100.00, 'Free WiFi, AC, Smart TV, Full Kitchen, Cinema Room', 'Entertainment suite with theater'],
        ['R-507', 'suite', 4, 'king', 'booked', 8200.00, 7800.00, 'Free WiFi, AC, Smart TV, Full Kitchen, Spa Bath', 'Wellness suite with spa'],
        ['R-508', 'suite', 6, 'king', 'available', 11000.00, 10500.00, 'Free WiFi, AC, Smart TV, Full Kitchen, 2 Living Rooms, Library', 'Luxury suite with reading room'],
    ];
    
    $success = 0;
    $skipped = 0;
    
    foreach ($rooms as $room) {
        // Check if room already exists
        $check = $conn->prepare("SELECT id FROM rooms WHERE room_no = ?");
        $check->bind_param("s", $room[0]);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            echo "<div class='warning'>⚠️ Room <strong>{$room[0]}</strong> already exists - skipped</div>";
            $skipped++;
            continue;
        }
        
        $stmt = $conn->prepare("INSERT INTO rooms (room_no, room_type, total_beds, bed_size, status, price, price_today, amenities, short_description, image_path) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $image_path = "images/rooms/demoRoom.jpg";
        
        $stmt->bind_param(
            "ssissddsss",
            $room[0], // room_no
            $room[1], // room_type
            $room[2], // total_beds
            $room[3], // bed_size
            $room[4], // status
            $room[5], // price
            $room[6], // price_today
            $room[7], // amenities
            $room[8], // short_description
            $image_path
        );
        
        if ($stmt->execute()) {
            echo "<div class='success'>✅ Created room: {$room[0]} ({$room[1]}) - Rs. {$room[5]}</div>";
            $success++;
        } else {
            echo "<div class='error'>❌ Error creating room {$room[0]}: " . $stmt->error . "</div>";
        }
        
        $stmt->close();
        $check->close();
    }
    
    echo "<div class='info'>📊 Rooms: $success created, $skipped skipped</div>";
    return $success;
}
