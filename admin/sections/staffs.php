<div class="staff-container">
    <div class="staff-header">
        <div class="staff-title">
            <ion-icon name="people-outline"></ion-icon>
            <h2>Staff Management</h2>
        </div>
        <button class="staff-add-button" onclick="openStaffModal()">
            <ion-icon name="add-outline"></ion-icon>
            Add New Staff
        </button>
    </div>

    <div class="staff-table-container">
        <table class="staff-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Profile</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Joined Date</th>
                    <th>Contract</th>
                    <th>Shift</th>
                    <th>Salary</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="staffTableBody">
                <!-- Sample row -->
                <tr>
                    <td>1</td>
                    <td><img src="/api/placeholder/40/40" alt="Staff" class="staff-profile-img"></td>
                    <td>John Doe</td>
                    <td>Chef</td>
                    <td>+977-9876543210</td>
                    <td>john@example.com</td>
                    <td>2024-01-15</td>
                    <td>1 Year</td>
                    <td>Morning</td>
                    <td>$1,200</td>
                    <td><span class="staff-status active">Active</span></td>
                    <td class="staff-actions">
                        <button class="staff-action-btn edit" onclick="editStaff(1)">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button class="staff-action-btn delete" onclick="deleteStaff(1)">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Staff Modal -->
<div id="staffModal" class="staff-modal">
    <div class="staff-modal-content">
        <span class="staff-close-btn" onclick="closeStaffModal()">&times;</span>
        <h2 id="modalTitle">Add New Staff</h2>

        <form id="staffForm" onsubmit="handleStaffSubmit(event)">
            <div class="staff-form-group">
                <label for="staffImage">Profile Image</label>
                <input type="file" id="staffImage" accept="image/*">
                <div id="imagePreview" class="staff-image-preview"></div>
            </div>

            <div class="staff-form-group">
                <label for="fullName">Full Name*</label>
                <input type="text" id="fullName" required>
            </div>

            <div class="staff-form-row">
                <div class="staff-form-group first-row">
                    <label for="role">Role*</label>
                    <select id="role" required>
                        <option value="">Select Role</option>
                        <option value="Manager">Manager</option>
                        <option value="Chef">Chef</option>
                        <option value="Waiter">Waiter</option>
                        <option value="Receptionist">Receptionist</option>
                    </select>
                </div>

                <div class="staff-form-group">
                    <label for="shift">Shift*</label>
                    <select id="shift" required>
                        <option value="">Select Shift</option>
                        <option value="Morning">Morning</option>
                        <option value="Evening">Evening</option>
                        <option value="Night">Night</option>
                        <option value="Rotational">Rotational</option>
                    </select>
                </div>
            </div>

            <div class="staff-form-row">
                <div class="staff-form-group">
                    <label for="contact">Contact Number*</label>
                    <input type="tel" id="contact" required>
                </div>

                <div class="staff-form-group">
                    <label for="email">Email Address*</label>
                    <input type="email" id="email" required>
                </div>
            </div>

            <div class="staff-form-row">
                <div class="staff-form-group">
                    <label for="joinedDate">Joined Date*</label>
                    <input type="date" id="joinedDate" required>
                </div>

                <div class="staff-form-group">
                    <label for="contract">Contract Duration*</label>
                    <select id="contract" required>
                        <option value="">Select Duration</option>
                        <option value="Permanent">Permanent</option>
                        <option value="6 months">6 months</option>
                        <option value="1 year">1 year</option>
                    </select>
                </div>
            </div>

            <div class="staff-form-row">
                <div class="staff-form-group">
                    <label for="salary">Salary</label>
                    <input type="number" id="salary">
                </div>

                <div class="staff-form-group">
                    <label for="status">Status</label>
                    <div class="staff-toggle">
                        <input type="checkbox" id="status" checked>
                        <label for="status" class="staff-toggle-label"></label>
                    </div>
                </div>
            </div>

            <div class="staff-form-actions">
                <button type="button" class="staff-cancel-btn" onclick="closeStaffModal()">Cancel</button>
                <button type="submit" class="staff-submit-btn">Save Staff</button>
            </div>
        </form>
    </div>
</div>