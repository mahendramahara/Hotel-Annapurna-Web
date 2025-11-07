<main class="table-management-container">
    <section class="table-management-section">
        <header class="table-management-header">
            <ion-icon name="grid-outline" class="table-title-icon"></ion-icon>
            <h2 class="table-section-title">Table Management</h2>
        </header>

        <div class="table-actions">
            <button id="table-add-new-btn" class="table-btn table-btn-primary">
                <ion-icon name="add-circle-outline"></ion-icon> Add New Table
            </button>
        </div>

        <div class="table-list-container">
            <table class="table-management-list">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Table Image</th>
                        <th>Table No</th>
                        <th>Total Chairs</th>
                        <th>Booking Status</th>
                        <th>Price (Main)</th>
                        <th>Price Today</th>
                        <th>Location</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="table-list-body">
                    <!-- Dynamic table rows will be inserted here -->
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal: Add/Edit Table -->
    <div id="table-modal" class="table-modal">
        <div class="table-modal-content">
            <span class="table-modal-close">&times;</span>
            <h3 id="table-modal-title">Add New Table</h3>
            <form id="table-form">
                <input type="hidden" id="table-edit-id">
                <div class="table-form-group">
                    <label for="table-image">Table Image</label>
                    <input type="file" id="table-image" accept="image/*">
                </div>
                <div class="table-form-group">
                    <label for="table-number">Table Number</label>
                    <input type="text" id="table-number" required>
                </div>
                <div class="table-form-group">
                    <label for="table-capacity">Seating Capacity</label>
                    <input type="number" id="table-capacity" required>
                </div>
                <div class="table-form-group">
                    <label for="table-status">Booking Status</label>
                    <select id="table-status">
                        <option value="free">Free</option>
                        <option value="booked">Booked</option>
                    </select>
                </div>
                <div class="table-form-group">
                    <label for="table-price-standard">Standard Price</label>
                    <input type="number" id="table-price-standard" step="0.01" required>
                </div>
                <div class="table-form-group">
                    <label for="table-price-today">Today's Special Price</label>
                    <input type="number" id="table-price-today" step="0.01">
                </div>
                <div class="table-form-group">
                    <label for="table-location">Location</label>
                    <select id="table-location">
                        <option value="indoor">Indoor</option>
                        <option value="outdoor">Outdoor</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>
                <div class="table-form-actions">
                    <button type="submit" class="table-btn table-btn-success">Save</button>
                    <button type="button" class="table-btn table-btn-secondary" id="table-modal-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</main>