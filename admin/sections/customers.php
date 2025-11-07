<div class="customers-container">
        <div class="customers-title">
            <ion-icon name="people-outline"></ion-icon>
            <span>Customer Management</span>
        </div>

        <div class="customers-table-wrapper">
            <table class="customers-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Profile</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Last Login</th>
                        <th>Orders</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="customersTableBody">
                    <!-- Table content will be dynamically populated -->
                </tbody>
            </table>
        </div>

        <button class="customers-btn customers-btn-primary customers-load-more" onclick="loadMoreCustomers()">
            Load More
        </button>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="customers-modal">
        <div class="customers-modal-content">
            <h2>Edit Customer Details</h2>
            <form id="editCustomerForm">
                <div class="customers-form-group">
                    <label>Full Name</label>
                    <input type="text" id="editName" required>
                </div>
                <div class="customers-form-group">
                    <label>Contact Number</label>
                    <input type="tel" id="editContact">
                </div>
                <div class="customers-form-group">
                    <label>Status</label>
                    <select id="editStatus">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="warned">Warned</option>
                        <option value="deleted">Deleted</option>
                    </select>
                </div>
                <div class="customers-form-group">
                    <label>Additional Notes</label>
                    <textarea id="editNotes" rows="3"></textarea>
                </div>
                <div class="customers-modal-actions">
                    <button type="submit" class="customers-btn customers-btn-primary">Save Changes</button>
                    <button type="button" class="customers-btn customers-btn-secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>