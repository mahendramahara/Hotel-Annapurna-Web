<?php
/**
 * Users Seeder
 * Creates 20 demo users with realistic data
 * Password for all users: 12345678
 */

require_once __DIR__ . '/../config/db.php';

function seedUsers($conn) {
    echo "<h3>👥 Seeding Users...</h3>";
    
    // Password hash for '12345678'
    $password = password_hash('12345678', PASSWORD_DEFAULT);
    
    $users = [
        // Admin Users
        ['Ramesh', 'Sharma', 'admin@hotelannapurna.com', '9841234567', 'admin', 'verified', 50000.00],
        ['Sita', 'Rai', 'sita.manager@hotelannapurna.com', '9841234568', 'admin', 'verified', 45000.00],
        ['Mahendra', 'Mahara', 'mahendra.manager@hotelannapurna.com', '9841234569', 'admin', 'verified', 48000.00],
        
        // Staff Members
        ['Krishna', 'Thapa', 'krishna.chef@hotelannapurna.com', '9841234569', 'staff', 'verified', 35000.00],
        ['Gita', 'Gurung', 'gita.receptionist@hotelannapurna.com', '9841234570', 'staff', 'verified', 28000.00],
        ['Hari', 'Magar', 'hari.waiter@hotelannapurna.com', '9841234571', 'staff', 'verified', 25000.00],
        ['Laxmi', 'Tamang', 'laxmi.housekeeping@hotelannapurna.com', '9841234572', 'staff', 'verified', 24000.00],
        ['Bikash', 'Shrestha', 'bikash.kitchen@hotelannapurna.com', '9841234573', 'staff', 'verified', 30000.00],
        ['Sunita', 'Poudel', 'sunita.bartender@hotelannapurna.com', '9841234574', 'staff', 'verified', 27000.00],
        ['Sanjay', 'Lama', 'sanjay.security@hotelannapurna.com', '9841234587', 'staff', 'verified', 26000.00],
        ['Bishnu', 'Rai', 'bishnu.assistant@hotelannapurna.com', '9841234588', 'staff', 'verified', 29000.00],
        ['Rama', 'Bhattarai', 'rama.cleaner@hotelannapurna.com', '9841234589', 'staff', 'verified', 22000.00],
        ['Tej', 'Gurung', 'tej.cook@hotelannapurna.com', '9841234590', 'staff', 'verified', 32000.00],
   
        
        // Customer Accounts
        ['Rajesh', 'Kumar', 'rajesh.kumar@gmail.com', '9841234575', 'customer', 'verified', null],
        ['Priya', 'Singh', 'priya.singh@yahoo.com', '9841234576', 'customer', 'verified', null],
        ['Amit', 'Patel', 'amit.patel@hotmail.com', '9841234577', 'customer', 'verified', null],
        ['Anjali', 'Verma', 'anjali.verma@outlook.com', '9841234578', 'customer', 'verified', null],
        ['Suresh', 'Bahadur', 'suresh.bdr@gmail.com', '9841234579', 'customer', 'verified', null],
        ['Kavita', 'Adhikari', 'kavita.adhikari@gmail.com', '9841234580', 'customer', 'verified', null],
        ['Deepak', 'Rana', 'deepak.rana@yahoo.com', '9841234581', 'customer', 'pending', null],
        ['Manisha', 'Karki', 'manisha.karki@gmail.com', '9841234582', 'customer', 'verified', null],
        ['Prakash', 'Limbu', 'prakash.limbu@hotmail.com', '9841234583', 'customer', 'verified', null],
        ['Ritu', 'Chaudhary', 'ritu.chaudhary@gmail.com', '9841234584', 'customer', 'pending', null],
        ['Nabin', 'Bhandari', 'nabin.bhandari@yahoo.com', '9841234585', 'customer', 'verified', null],
        ['Pooja', 'Thakur', 'pooja.thakur@outlook.com', '9841234586', 'customer', 'verified', null],
        ['Anita', 'Shrestha', 'anita.shrestha@gmail.com', '9841234591', 'customer', 'verified', null],
        ['Ramesh', 'KC', 'ramesh.kc@yahoo.com', '9841234592', 'customer', 'verified', null],
        ['Sabina', 'Maharjan', 'sabina.maharjan@gmail.com', '9841234593', 'customer', 'verified', null],
        ['Anil', 'Thapa', 'anil.thapa@hotmail.com', '9841234594', 'customer', 'pending', null],
        ['Maya', 'Gurung', 'maya.gurung@outlook.com', '9841234595', 'customer', 'verified', null],
        ['Santosh', 'Pradhan', 'santosh.pradhan@gmail.com', '9841234596', 'customer', 'verified', null],
        ['Puja', 'Karki', 'puja.karki@yahoo.com', '9841234597', 'customer', 'verified', null],
        ['Dinesh', 'Basnet', 'dinesh.basnet@gmail.com', '9841234598', 'customer', 'verified', null],
        ['Sarita', 'Malla', 'sarita.malla@hotmail.com', '9841234599', 'customer', 'pending', null],
        ['Bibek', 'Sharma', 'bibek.sharma@outlook.com', '9841234600', 'customer', 'verified', null],
        ['Sushma', 'Rana', 'sushma.rana@gmail.com', '9841234601', 'customer', 'verified', null],
        ['Kamal', 'Shahi', 'kamal.shahi@yahoo.com', '9841234602', 'customer', 'verified', null],
        ['Nirmala', 'Thakuri', 'nirmala.thakuri@gmail.com', '9841234603', 'customer', 'verified', null],
        ['Uttam', 'Bohara', 'uttam.bohara@hotmail.com', '9841234604', 'customer', 'verified', null],
        ['Sapana', 'Dahal', 'sapana.dahal@outlook.com', '9841234605', 'customer', 'pending', null],
        ['Rajan', 'Adhikari', 'rajan.adhikari@gmail.com', '9841234606', 'customer', 'verified', null],
    ];
    
    $success = 0;
    $skipped = 0;
    
    foreach ($users as $user) {
        // Check if user already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $user[2]);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            echo "<div class='warning'>⚠️ User <strong>{$user[2]}</strong> already exists - skipped</div>";
            $skipped++;
            continue;
        }
        
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, contact, password, role, status, salary, address, profile_pic) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $address = "Kathmandu, Nepal";
        // Set default demo image based on role
        if ($user[4] === 'admin') {
            $profile_pic = "images/profiles/demoAdmin.jpg";
        } elseif ($user[4] === 'staff') {
            $profile_pic = "images/profiles/demoStaff.jpg";
        } else {
            $profile_pic = "images/profiles/demoUser.jpg";
        }
        
        $stmt->bind_param(
            "sssssssdss",
            $user[0], // first_name
            $user[1], // last_name
            $user[2], // email
            $user[3], // contact
            $password, // password
            $user[4], // role
            $user[5], // status
            $user[6], // salary
            $address,
            $profile_pic
        );
        
        if ($stmt->execute()) {
            echo "<div class='success'>✅ Created user: {$user[0]} {$user[1]} ({$user[4]})</div>";
            $success++;
        } else {
            echo "<div class='error'>❌ Error creating user {$user[2]}: " . $stmt->error . "</div>";
        }
        
        $stmt->close();
        $check->close();
    }
    
    echo "<div class='info'>📊 Users: $success created, $skipped skipped</div>";
    return $success;
}
