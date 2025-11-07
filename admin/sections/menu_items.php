<div class="menu-dashboard">
    <!-- Vegetarian Menu Section -->
    <section class="menu-section" id="vegetarian-menu">
        <div class="menu-section-title">
            <ion-icon name="leaf-outline"></ion-icon>
            <h2>Vegetarian Menu</h2>
        </div>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Discount Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sample Vegetarian Menu Item -->
                <tr>
                    <td>1</td>
                    <td><img src="/api/placeholder/80/80" alt="Vegetarian Dish"></td>
                    <td>Vegetable Curry</td>
                    <td>₹250</td>
                    <td>₹200</td>
                    <td>
                        <div class="menu-action-icons">
                            <i class="fas fa-edit edit" onclick="openEditModal('vegetarian')"></i>
                            <i class="fas fa-trash-alt delete" onclick="openDeleteConfirm()"></i>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <button class="menu-add-button" onclick="openAddModal('vegetarian')">
            <i class="fas fa-plus"></i> Add New Vegetarian Item
        </button>
    </section>

    <!-- Non-Vegetarian Menu Section -->
    <section class="menu-section" id="non-vegetarian-menu">
        <div class="menu-section-title">
            <ion-icon name="restaurant-outline"></ion-icon>
            <h2>Non-Vegetarian Menu</h2>
        </div>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Discount Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sample Non-Vegetarian Menu Item -->
                <tr>
                    <td>1</td>
                    <td><img src="/api/placeholder/80/80" alt="Non-Vegetarian Dish"></td>
                    <td>Chicken Tikka Masala</td>
                    <td>₹350</td>
                    <td>₹300</td>
                    <td>
                        <div class="menu-action-icons">
                            <i class="fas fa-edit edit" onclick="openEditModal('non-vegetarian')"></i>
                            <i class="fas fa-trash-alt delete" onclick="openDeleteConfirm()"></i>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <button class="menu-add-button" onclick="openAddModal('non-vegetarian')">
            <i class="fas fa-plus"></i> Add New Non-Vegetarian Item
        </button>
    </section>

    <!-- Special Items Menu Section -->
    <section class="menu-section" id="special-items-menu">
        <div class="menu-section-title">
            <ion-icon name="star-outline"></ion-icon>
            <h2>Special Items</h2>
        </div>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Discount Price</th>
                    <th>Available On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sample Special Menu Item -->
                <tr>
                    <td>1</td>
                    <td><img src="/api/placeholder/80/80" alt="Special Dish"></td>
                    <td>Chef's Special Thali</td>
                    <td>₹450</td>
                    <td>₹400</td>
                    <td>Sunday</td>
                    <td>
                        <div class="menu-action-icons">
                            <i class="fas fa-edit edit" onclick="openEditModal('special')"></i>
                            <i class="fas fa-trash-alt delete" onclick="openDeleteConfirm()"></i>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <button class="menu-add-button" onclick="openAddModal('special')">
            <i class="fas fa-plus"></i> Add New Special Item
        </button>
    </section>

    <!-- Add/Edit Modal -->
    <div id="menu-modal" class="menu-modal">
        <div class="menu-modal-content">
            <span class="menu-modal-close" onclick="closeModal()">&times;</span>
            <h2 id="modal-title">Add/Edit Menu Item</h2>
            <form id="menu-item-form">
                <div class="menu-form-group">
                    <label for="food-name">Food Name</label>
                    <input type="text" id="food-name" name="food-name" required>
                </div>
                <div class="menu-form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" required>
                </div>
                <div class="menu-form-group">
                    <label for="discount-price">Discount Price</label>
                    <input type="number" id="discount-price" name="discount-price">
                </div>
                <div class="menu-form-group">
                    <label for="image-upload">Image Upload</label>
                    <input type="file" id="image-upload" name="image-upload" accept="image/*">
                </div>
                <div id="special-day-group" class="menu-form-group" style="display:none;">
                    <label for="available-day">Available On</label>
                    <select id="available-day" name="available-day">
                        <option value="">Select Day</option>
                        <option value="Sunday">Sunday</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                    </select>
                </div>
                <div class="menu-modal-actions">
                    <button type="button" class="menu-modal-button save" onclick="saveMenuItem()">Save Changes</button>
                    <button type="button" class="menu-modal-button clear" onclick="clearForm()">Clear Data</button>
                    <button type="button" id="delete-button" class="menu-modal-button delete" onclick="deleteMenuItem()" style="display:none;">Delete Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="menu-modal">
        <div class="menu-modal-content">
            <h2>Confirm Operation</h2>
            <p id="confirm-message">Are you sure you want to perform this action?</p>
            <div class="menu-modal-actions">
                <button class="menu-modal-button save" onclick="confirmAction()">Yes</button>
                <button class="menu-modal-button delete" onclick="closeConfirmModal()">No</button>
            </div>
        </div>
    </div>
</div>