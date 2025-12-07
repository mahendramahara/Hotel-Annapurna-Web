<?php
require_once('includes/auth-guard.php');
require_once('../config/db.php');
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

// Parse address into components
$address_parts = [
    'tole' => '',
    'ward' => '',
    'rural' => '',
    'district' => '',
    'country' => ''
];

if (!empty($admin['address'])) {
    // Try to decode as JSON first
    $decoded = json_decode($admin['address'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        // It's JSON format
        $address_parts['tole'] = $decoded['tole'] ?? '';
        $address_parts['ward'] = $decoded['ward'] ?? '';
        $address_parts['rural'] = $decoded['rural'] ?? '';
        $address_parts['district'] = $decoded['district'] ?? '';
        $address_parts['country'] = $decoded['country'] ?? '';
    } else {
        // Try pipe-delimited format
        $parts = explode('|', $admin['address']);
        if (count($parts) >= 5) {
            $address_parts['tole'] = $parts[0];
            $address_parts['ward'] = $parts[1];
            $address_parts['rural'] = $parts[2];
            $address_parts['district'] = $parts[3];
            $address_parts['country'] = $parts[4];
        }
    }
}
?>

<div class="profile-container" style="display:none;">
    <div class="profile-header">
        <div class="profile-title">
            <ion-icon name="person-circle-outline"></ion-icon>
            <h2>My Profile</h2>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-card">
            <div class="profile-image-section">
                <div class="profile-avatar">
                    <?php if(!empty($admin['profile_pic'])): ?>
                        <img src="../<?= htmlspecialchars($admin['profile_pic']) ?>" alt="Profile">
                    <?php else: ?>
                        <ion-icon name="person-circle-outline"></ion-icon>
                    <?php endif; ?>
                </div>
                <button class="profile-btn profile-btn-secondary" onclick="openProfileImageModal()">
                    <ion-icon name="camera-outline"></ion-icon> Change Photo
                </button>
            </div>

            <div class="profile-info-section">
                <div class="profile-info-header">
                    <h3>Personal Information</h3>
                    <button class="profile-btn profile-btn-primary" onclick="openEditProfileModal()">
                        <ion-icon name="create-outline"></ion-icon> Edit Profile
                    </button>
                </div>

                <div class="profile-info-grid">
                    <div class="profile-info-item">
                        <label><ion-icon name="person-outline"></ion-icon> Full Name</label>
                        <p><?= htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']) ?></p>
                    </div>
                    <div class="profile-info-item">
                        <label><ion-icon name="mail-outline"></ion-icon> Email</label>
                        <p><?= htmlspecialchars($admin['email']) ?></p>
                    </div>
                    <div class="profile-info-item">
                        <label><ion-icon name="call-outline"></ion-icon> Contact</label>
                        <p><?= htmlspecialchars($admin['contact'] ?? 'Not provided') ?></p>
                    </div>
                    <div class="profile-info-item">
                        <label><ion-icon name="shield-checkmark-outline"></ion-icon> Role</label>
                        <p><span class="profile-role-badge"><?= htmlspecialchars(ucfirst($admin['role'])) ?></span></p>
                    </div>
                    <div class="profile-info-item full-width">
                        <label><ion-icon name="location-outline"></ion-icon> Address</label>
                        <p data-address-tole="<?= htmlspecialchars($address_parts['tole']) ?>"
                           data-address-ward="<?= htmlspecialchars($address_parts['ward']) ?>"
                           data-address-rural="<?= htmlspecialchars($address_parts['rural']) ?>"
                           data-address-district="<?= htmlspecialchars($address_parts['district']) ?>"
                           data-address-country="<?= htmlspecialchars($address_parts['country']) ?>"><?php 
                            if (!empty($admin['address'])) {
                                $formatted = array_filter([
                                    $address_parts['tole'],
                                    $address_parts['ward'],
                                    $address_parts['rural'],
                                    $address_parts['district'],
                                    $address_parts['country']
                                ]);
                                if (!empty($formatted)) {
                                    echo htmlspecialchars(implode(', ', $formatted));
                                } else {
                                    echo 'Not provided';
                                }
                            } else {
                                echo 'Not provided';
                            }
                        ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-security-header">
                <h3><ion-icon name="lock-closed-outline"></ion-icon> Security Settings</h3>
            </div>

            <form id="passwordChangeForm" class="profile-security-form">
                <div class="profile-form-group">
                    <label for="currentPassword">Current Password</label>
                    <div class="profile-password-input">
                        <input type="password" id="currentPassword" required>
                        <span class="profile-password-toggle">
                            <ion-icon name="eye-outline"></ion-icon>
                        </span>
                    </div>
                </div>

                <div class="profile-form-group">
                    <label for="newPassword">New Password</label>
                    <div class="profile-password-input">
                        <input type="password" id="newPassword" required minlength="8">
                        <span class="profile-password-toggle">
                            <ion-icon name="eye-outline"></ion-icon>
                        </span>
                    </div>
                    <div class="profile-password-strength-container">
                        <div class="profile-password-strength"></div>
                    </div>
                </div>

                <div class="profile-form-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <div class="profile-password-input">
                        <input type="password" id="confirmPassword" required minlength="8">
                        <span class="profile-password-toggle">
                            <ion-icon name="eye-outline"></ion-icon>
                        </span>
                    </div>
                </div>

                <button type="submit" class="profile-btn profile-btn-primary">
                    <ion-icon name="shield-checkmark-outline"></ion-icon> Change Password
                </button>
            </form>
        </div>
    </div>
</div>

<div id="editProfileModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3><ion-icon name="create-outline"></ion-icon> Edit Profile</h3>
            <button class="modal-close" onclick="closeEditProfileModal()">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>
        <form id="editProfileForm">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="editFirstName">First Name</label>
                        <input type="text" id="editFirstName" value="<?= htmlspecialchars($admin['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editLastName">Last Name</label>
                        <input type="text" id="editLastName" value="<?= htmlspecialchars($admin['last_name']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" value="<?= htmlspecialchars($admin['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="editContact">Contact Number</label>
                    <input type="tel" id="editContact" value="<?= htmlspecialchars($admin['contact'] ?? '') ?>">
                </div>
                <div class="address-section">
                    <h4><ion-icon name="location-outline"></ion-icon> Address Details</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editTole">Tole Name</label>
                            <input type="text" id="editTole" placeholder="e.g., Tinkune">
                        </div>
                        <div class="form-group">
                            <label for="editWard">Ward Number</label>
                            <input type="text" id="editWard" placeholder="e.g., 32">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editRural">Municipality/Rural Municipality</label>
                            <input type="text" id="editRural" placeholder="e.g., Kathmandu Metropolitan City">
                        </div>
                        <div class="form-group">
                            <label for="editDistrict">District</label>
                            <input type="text" id="editDistrict" placeholder="e.g., Kathmandu">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editCountry">Country</label>
                        <input type="text" id="editCountry" placeholder="e.g., Nepal">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditProfileModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <ion-icon name="save-outline"></ion-icon> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<div id="profileImageModal" class="modal-overlay">
    <div class="modal-container modal-sm">
        <div class="modal-header">
            <h3><ion-icon name="camera-outline"></ion-icon> Change Profile Picture</h3>
            <button class="modal-close" onclick="closeProfileImageModal()">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>
        <form id="profileImageForm">
            <div class="modal-body">
                <div class="profile-image-upload">
                    <div class="profile-image-preview">
                        <?php if(!empty($admin['profile_pic'])): ?>
                            <img id="imagePreview" src="../<?= htmlspecialchars($admin['profile_pic']) ?>" alt="Preview">
                        <?php else: ?>
                            <img id="imagePreview" src="../assets/images/default-avatar.png" alt="Preview">
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="profileImage" class="file-upload-label">
                            <ion-icon name="cloud-upload-outline"></ion-icon>
                            Choose Image
                        </label>
                        <input type="file" id="profileImage" accept="image/*" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeProfileImageModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <ion-icon name="cloud-upload-outline"></ion-icon> Upload
                </button>
            </div>
        </form>
    </div>
</div>
