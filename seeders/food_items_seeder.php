<?php
/**
 * Food Items Seeder
 * Creates 20 delicious food items for Hotel Annapurna restaurant
 */

require_once __DIR__ . '/../config/db.php';

function seedFoodItems($conn) {
    echo "<h3>🍽️ Seeding Food Items...</h3>";
    
    $food_items = [
        // Vegetarian Items
        ['veg', 'Vegetable Momo (10 pcs)', 180.00, 150.00, 'Steamed dumplings filled with fresh vegetables and Himalayan spices', 'All Days'],
        ['veg', 'Paneer Butter Masala', 350.00, 320.00, 'Cottage cheese cubes in rich tomato and butter gravy', 'All Days'],
        ['veg', 'Dal Bhat Tarkari', 250.00, 220.00, 'Traditional Nepali lentil soup with rice and vegetable curry', 'All Days'],
        ['veg', 'Mixed Vegetable Thali', 300.00, 280.00, 'Complete meal with rice, dal, vegetables, pickle, and papad', 'All Days'],
        ['veg', 'Mushroom Chhoila', 280.00, 250.00, 'Spicy grilled mushroom in Newari style with authentic spices', 'All Days'],
        ['veg', 'Veg Fried Rice', 220.00, 200.00, 'Aromatic rice stir-fried with fresh vegetables and soy sauce', 'All Days'],
        ['veg', 'Palak Paneer', 340.00, 310.00, 'Cottage cheese cubes in creamy spinach gravy', 'All Days'],
        
        // Non-Vegetarian Items
        ['non-veg', 'Chicken Momo (10 pcs)', 220.00, 200.00, 'Juicy chicken dumplings steamed to perfection', 'All Days'],
        ['non-veg', 'Chicken Chhoila', 380.00, 350.00, 'Grilled chicken marinated in Newari spices, served with beaten rice', 'All Days'],
        ['non-veg', 'Butter Chicken', 420.00, 390.00, 'Tender chicken pieces in creamy tomato butter sauce', 'All Days'],
        ['non-veg', 'Chicken Biryani', 400.00, 370.00, 'Aromatic basmati rice layered with spiced chicken', 'All Days'],
        ['non-veg', 'Fish Fry', 450.00, 420.00, 'Crispy fried fish marinated in special herbs and spices', 'Monday,Wednesday,Friday'],
        ['non-veg', 'Mutton Curry', 550.00, 520.00, 'Slow-cooked mutton in traditional Nepali spices', 'All Days'],
        ['non-veg', 'Chicken Sekuwa', 360.00, 330.00, 'Barbecued chicken skewers with authentic Nepali marinade', 'All Days'],
        
        // Special Items
        ['special', 'Newari Khaja Set', 650.00, 600.00, 'Authentic Newari platter with beaten rice, bara, choila, and achar', 'Friday,Saturday,Sunday'],
        ['special', 'Thakali Thali', 700.00, 650.00, 'Complete Thakali meal with rice, dal, tarkari, gundruk, and achar', 'All Days'],
        ['special', 'Tandoori Chicken (Full)', 850.00, 800.00, 'Whole chicken marinated in yogurt and spices, cooked in tandoor', 'All Days'],
        ['special', 'Mixed Grill Platter', 950.00, 900.00, 'Assorted grilled meats including chicken, mutton, and fish', 'Friday,Saturday,Sunday'],
        ['special', 'Seafood Special', 1200.00, 1150.00, 'Chef\'s special seafood combination with prawns, fish, and calamari', 'Saturday,Sunday'],
        ['special', 'Royal Annapurna Feast', 1500.00, 1400.00, 'Grand feast with multiple courses including appetizers, mains, and desserts', 'All Days'],
        
        // Additional Vegetarian Items
        ['veg', 'Aloo Gobi Masala', 260.00, 240.00, 'Potato and cauliflower curry with aromatic spices', 'All Days'],
        ['veg', 'Saag Paneer', 330.00, 300.00, 'Fresh greens with cottage cheese in mild spices', 'All Days'],
        ['veg', 'Chana Masala', 240.00, 220.00, 'Chickpeas cooked in tangy tomato onion gravy', 'All Days'],
        ['veg', 'Baingan Bharta', 270.00, 250.00, 'Roasted eggplant mashed with spices and herbs', 'All Days'],
        ['veg', 'Veg Spring Roll (6 pcs)', 200.00, 180.00, 'Crispy rolls filled with mixed vegetables', 'All Days'],
        ['veg', 'Mushroom Fried Rice', 240.00, 220.00, 'Fried rice with fresh mushrooms and vegetables', 'All Days'],
        ['veg', 'Veg Noodles', 230.00, 210.00, 'Stir-fried noodles with seasonal vegetables', 'All Days'],
        
        // Additional Non-Vegetarian Items
        ['non-veg', 'Chicken Curry', 380.00, 350.00, 'Traditional chicken curry with home-style spices', 'All Days'],
        ['non-veg', 'Chicken Fried Rice', 280.00, 260.00, 'Fragrant rice with chicken and vegetables', 'All Days'],
        ['non-veg', 'Pork Chhoila', 420.00, 390.00, 'Grilled pork in authentic Newari marinade', 'All Days'],
        ['non-veg', 'Chicken Noodles', 270.00, 250.00, 'Wok-tossed noodles with juicy chicken pieces', 'All Days'],
        ['non-veg', 'Mutton Sekuwa', 580.00, 550.00, 'Grilled mutton skewers with traditional spices', 'All Days'],
        ['non-veg', 'Prawn Curry', 650.00, 620.00, 'Succulent prawns in rich coconut gravy', 'Monday,Wednesday,Friday'],
        ['non-veg', 'Chicken Thukpa', 320.00, 300.00, 'Hearty noodle soup with chicken and vegetables', 'All Days'],
        
        // More Special Items
        ['special', 'Lamb Rogan Josh', 800.00, 750.00, 'Aromatic lamb curry from Kashmir with rich spices', 'All Days'],
        ['special', 'Prawn Biryani', 750.00, 700.00, 'Premium biryani with fresh prawns and aromatic rice', 'Friday,Saturday,Sunday'],
        ['special', 'Duck Roast', 950.00, 900.00, 'Slow-roasted duck with herbs and vegetables', 'Saturday,Sunday'],
        ['special', 'Lobster Thermidor', 1800.00, 1700.00, 'Luxurious lobster in creamy sauce', 'Saturday,Sunday'],
        ['special', 'Chef Special BBQ', 1100.00, 1050.00, 'Mixed BBQ platter with chef\'s secret marinade', 'Friday,Saturday,Sunday'],
        ['special', 'Himalayan Yak Steak', 1300.00, 1250.00, 'Premium yak meat steak with mountain herbs', 'All Days'],
    ];
    
    $success = 0;
    $skipped = 0;
    
    foreach ($food_items as $item) {
        // Check if item already exists
        $check = $conn->prepare("SELECT id FROM food_items WHERE food_name = ?");
        $check->bind_param("s", $item[1]);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            echo "<div class='warning'>⚠️ Food item <strong>{$item[1]}</strong> already exists - skipped</div>";
            $skipped++;
            continue;
        }
        
        $stmt = $conn->prepare("INSERT INTO food_items (category, food_name, price, discount_price, short_description, available_days, image_path) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $image_path = "images/menu/demoFood.jpg";
        
        $stmt->bind_param(
            "ssddsss",
            $item[0], // category
            $item[1], // food_name
            $item[2], // price
            $item[3], // discount_price
            $item[4], // short_description
            $item[5], // available_days
            $image_path
        );
        
        if ($stmt->execute()) {
            echo "<div class='success'>✅ Created food item: {$item[1]} - Rs. {$item[2]}</div>";
            $success++;
        } else {
            echo "<div class='error'>❌ Error creating food item {$item[1]}: " . $stmt->error . "</div>";
        }
        
        $stmt->close();
        $check->close();
    }
    
    echo "<div class='info'>📊 Food Items: $success created, $skipped skipped</div>";
    return $success;
}
