<?php require_once("includes/sidebar.php"); ?>

<section class="dashboard">
<div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <ion-icon name="person-circle-outline" class="profile-icon"></ion-icon>
            <span class="profile-title">Admin Profile</span>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Profile Card -->
            <section class="profile-card">
                <div class="profile-image-container">
                    <img src="admin-avatar.jpg" alt="Admin Profile Picture" class="profile-image">
                    <button class="profile-image-edit" aria-label="Edit profile picture">
                        <ion-icon name="camera"></ion-icon>
                    </button>
                </div>

                <div class="profile-info">
                    <h2 class="profile-name">John Doe</h2>
                    <span class="profile-role">Super Admin</span>

                    <div class="profile-details">
                        <div class="profile-detail-item">
                            <ion-icon name="mail-outline"></ion-icon>
                            <span class="profile-label">Email:</span>
                            <span class="profile-value">john.doe@annapurna.com</span>
                        </div>

                        <div class="profile-detail-item">
                            <ion-icon name="call-outline"></ion-icon>
                            <span class="profile-label">Contact:</span>
                            <span class="profile-value">+977-9876543210</span>
                        </div>

                        <div class="profile-detail-item">
                            <ion-icon name="person-outline"></ion-icon>
                            <span class="profile-label">Username:</span>
                            <span class="profile-value">johndoe</span>
                        </div>

                        <div class="profile-detail-item">
                            <ion-icon name="calendar-outline"></ion-icon>
                            <span class="profile-label">Joined Date:</span>
                            <span class="profile-value">January 15, 2024</span>
                        </div>

                        <div class="profile-detail-item">
                            <ion-icon name="time-outline"></ion-icon>
                            <span class="profile-label">Last Login:</span>
                            <span class="profile-value">February 9, 2025 13:33</span>
                        </div>

                        <div class="profile-detail-item">
                            <ion-icon name="shield-checkmark-outline"></ion-icon>
                            <span class="profile-label">Security Status:</span>
                            <span class="profile-value profile-status-active">Active (2FA Enabled)</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Password Change Section -->
            <section class="profile-password-section">
                <h3>Change Password</h3>
                <form id="passwordChangeForm" class="profile-password-form">
                    <div class="profile-form-group">
                        <label for="currentPassword">Current Password</label>
                        <div class="profile-password-input">
                            <input type="password" id="currentPassword" required>
                            <button type="button" class="profile-password-toggle">
                                <ion-icon name="eye-outline"></ion-icon>
                            </button>
                        </div>
                    </div>

                    <div class="profile-form-group">
                        <label for="newPassword">New Password</label>
                        <div class="profile-password-input">
                            <input type="password" id="newPassword" required>
                            <button type="button" class="profile-password-toggle">
                                <ion-icon name="eye-outline"></ion-icon>
                            </button>
                        </div>
                        <div class="profile-password-strength"></div>
                    </div>

                    <div class="profile-form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <div class="profile-password-input">
                            <input type="password" id="confirmPassword" required>
                            <button type="button" class="profile-password-toggle">
                                <ion-icon name="eye-outline"></ion-icon>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="profile-save-btn">
                        <ion-icon name="save-outline"></ion-icon>
                        Save Changes
                    </button>
                </form>
            </section>
        </div>

</div>
</section>


<script src="assets/js/script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>