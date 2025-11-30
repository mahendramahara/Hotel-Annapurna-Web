<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT first_name, last_name, email, contact, profile_pic, address, role, status, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Parse address if exists
$address_parts = ['tole' => '', 'ward' => '', 'rural' => '', 'district' => '', 'country' => ''];
if (!empty($user['address'])) {
    $parsed = json_decode($user['address'], true);
    if ($parsed) {
        $address_parts = array_merge($address_parts, $parsed);
    }
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (in_array($_FILES['profile_pic']['type'], $allowed_types) && $_FILES['profile_pic']['size'] <= $max_size) {
        $upload_dir = 'images/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
            // Delete old profile picture if exists
            if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) {
                unlink($user['profile_pic']);
            }
            
            $update_pic = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $update_pic->bind_param("si", $upload_path, $user_id);
            $update_pic->execute();
            
            $user['profile_pic'] = $upload_path;
            $success_message = "Profile picture updated successfully!";
        }
    } else {
        $error_message = "Invalid file type or size. Please upload JPG, PNG, or GIF under 5MB.";
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $contact = trim($_POST['contact']);
    
    // Build address from parts
    $tole = trim($_POST['tole']);
    $ward = trim($_POST['ward']);
    $rural = trim($_POST['rural']);
    $district = trim($_POST['district']);
    $country = trim($_POST['country']);
    
    $address_data = [
        'tole' => $tole,
        'ward' => $ward,
        'rural' => $rural,
        'district' => $district,
        'country' => $country
    ];
    
    $address_json = json_encode($address_data);
    
    $update_stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, contact = ?, address = ? WHERE id = ?");
    $update_stmt->bind_param("ssssi", $first_name, $last_name, $contact, $address_json, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['user_first_name'] = $first_name;
        $_SESSION['user_last_name'] = $last_name;
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
        $success_message = "Profile updated successfully!";
        
        // Refresh user data
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        // Update address parts
        $parsed = json_decode($user['address'], true);
        if ($parsed) {
            $address_parts = $parsed;
        }
    }
}

// Format address for display
function formatAddress($address_json) {
    if (empty($address_json)) return 'Not provided';
    
    $parts = json_decode($address_json, true);
    if (!$parts) return 'Not provided';
    
    $address_string = [];
    if (!empty($parts['tole'])) $address_string[] = $parts['tole'];
    if (!empty($parts['ward'])) $address_string[] = "Ward " . $parts['ward'];
    if (!empty($parts['rural'])) $address_string[] = $parts['rural'];
    if (!empty($parts['district'])) $address_string[] = $parts['district'];
    if (!empty($parts['country'])) $address_string[] = $parts['country'];
    
    return !empty($address_string) ? implode(', ', $address_string) : 'Not provided';
}

include 'includes/header.php';
?>

<style>
    .profile-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
    }
    
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 30px;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #667eea;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-upload {
        position: relative;
        cursor: pointer;
    }
    
    .avatar-upload input[type="file"] {
        display: none;
    }
    
    .avatar-upload-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #667eea;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.3s;
    }
    
    .avatar-upload-overlay:hover {
        transform: scale(1.1);
    }
    
    .avatar-upload-overlay ion-icon {
        color: white;
        font-size: 20px;
    }
    
    .profile-info h1 {
        margin: 0 0 10px 0;
        font-size: 32px;
    }
    
    .profile-info p {
        margin: 5px 0;
        opacity: 0.9;
        font-size: 16px;
    }
    
    .profile-badge {
        display: inline-block;
        padding: 5px 15px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        font-size: 14px;
        margin-top: 10px;
    }
    
    .profile-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    
    .profile-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .profile-card h2 {
        margin: 0 0 20px 0;
        color: #333;
        font-size: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .profile-card h2 ion-icon {
        font-size: 28px;
        color: #667eea;
    }
    
    .info-row {
        display: flex;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #666;
        min-width: 140px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-label ion-icon {
        font-size: 20px;
        color: #667eea;
    }
    
    .info-value {
        color: #333;
        flex: 1;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        background: #f9f9f9;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .address-section {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 20px;
        border-radius: 10px;
        margin: 20px 0;
    }
    
    .address-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(102, 126, 234, 0.3);
    }
    
    .address-section-title ion-icon {
        font-size: 24px;
        color: #667eea;
    }
    
    .btn-update {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: transform 0.2s;
    }
    
    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        text-align: center;
    }
    
    .stat-icon {
        font-size: 40px;
        margin-bottom: 10px;
    }
    
    .stat-card:nth-child(1) .stat-icon { color: #667eea; }
    .stat-card:nth-child(2) .stat-icon { color: #f093fb; }
    .stat-card:nth-child(3) .stat-icon { color: #4facfe; }
    
    .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #333;
        margin: 10px 0 5px 0;
    }
    
    .stat-label {
        color: #666;
        font-size: 14px;
    }
    
    .stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }
    
    .profile-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .profile-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }
    
    @media (max-width: 768px) {
        .profile-content {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .profile-header {
            flex-direction: column;
            text-align: center;
        }
        
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-container">
    <?php if (isset($success_message)): ?>
        <div class="success-message" style="margin-bottom: 20px;">
            <ion-icon name="checkmark-circle"></ion-icon>
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <ion-icon name="alert-circle"></ion-icon>
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-header">
        <div class="avatar-upload">
            <form method="POST" enctype="multipart/form-data" id="profilePicForm">
                <label for="profile_pic_input">
                    <div class="profile-avatar">
                        <?php if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture">
                        <?php else: ?>
                            <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div class="avatar-upload-overlay" title="Change profile picture">
                        <ion-icon name="camera"></ion-icon>
                    </div>
                </label>
                <input type="file" id="profile_pic_input" name="profile_pic" accept="image/*" onchange="this.form.submit()">
            </form>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
            <p><ion-icon name="mail"></ion-icon> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><ion-icon name="call"></ion-icon> <?php echo htmlspecialchars($user['contact']); ?></p>
            <div class="profile-badge">
                <ion-icon name="shield-checkmark"></ion-icon> 
                <?php echo ucfirst($user['status']); ?> Account
            </div>
        </div>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <ion-icon name="calendar"></ion-icon>
            </div>
            <div class="stat-value">0</div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <ion-icon name="restaurant"></ion-icon>
            </div>
            <div class="stat-value">0</div>
            <div class="stat-label">Food Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <ion-icon name="time"></ion-icon>
            </div>
            <div class="stat-value">
                <?php 
                $join_date = new DateTime($user['created_at']);
                $now = new DateTime();
                $diff = $now->diff($join_date);
                echo $diff->days; 
                ?>
            </div>
            <div class="stat-label">Days Member</div>
        </div>
    </div>
    
    <div class="profile-content">
        <div class="profile-card">
            <h2>
                <ion-icon name="person-circle"></ion-icon>
                Personal Information
            </h2>
            
            <div class="info-row">
                <div class="info-label">
                    <ion-icon name="person"></ion-icon>
                    Full Name
                </div>
                <div class="info-value"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">
                    <ion-icon name="mail"></ion-icon>
                    Email
                </div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">
                    <ion-icon name="call"></ion-icon>
                    Contact
                </div>
                <div class="info-value"><?php echo htmlspecialchars($user['contact']); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">
                    <ion-icon name="location"></ion-icon>
                    Address
                </div>
                <div class="info-value"><?php echo formatAddress($user['address']); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">
                    <ion-icon name="shield"></ion-icon>
                    Role
                </div>
                <div class="info-value"><?php echo ucfirst($user['role']); ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">
                    <ion-icon name="calendar"></ion-icon>
                    Member Since
                </div>
                <div class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
            </div>
        </div>
        
        <div class="profile-card">
            <h2>
                <ion-icon name="create"></ion-icon>
                Update Profile
            </h2>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="contact">Contact Number *</label>
                    <input type="tel" id="contact" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" required>
                </div>
                
                <div class="address-section">
                    <div class="address-section-title">
                        <ion-icon name="location"></ion-icon>
                        <span>Address Details</span>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tole">Tole Name</label>
                            <input type="text" id="tole" name="tole" value="<?php echo htmlspecialchars($address_parts['tole']); ?>" placeholder="e.g., Tinkune">
                        </div>
                        
                        <div class="form-group">
                            <label for="ward">Ward Number</label>
                            <input type="text" id="ward" name="ward" value="<?php echo htmlspecialchars($address_parts['ward']); ?>" placeholder="e.g., 32">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rural">Municipality/Rural Municipality</label>
                            <input type="text" id="rural" name="rural" value="<?php echo htmlspecialchars($address_parts['rural']); ?>" placeholder="e.g., Kathmandu Metropolitan City">
                        </div>
                        
                        <div class="form-group">
                            <label for="district">District</label>
                            <input type="text" id="district" name="district" value="<?php echo htmlspecialchars($address_parts['district']); ?>" placeholder="e.g., Kathmandu">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($address_parts['country']); ?>" placeholder="e.g., Nepal">
                    </div>
                </div>
                
                <button type="submit" name="update_profile" class="btn-update">
                    <ion-icon name="save"></ion-icon>
                    Update Profile
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
