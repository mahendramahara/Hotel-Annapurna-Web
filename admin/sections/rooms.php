<section class="room-section">
    <div class="room-header">
        <div class="room-title">
            <ion-icon name="bed-outline" class="room-title-icon"></ion-icon>
            <h1>Room Management</h1>
        </div>
        <button class="room-add-btn" onclick="openModal('add')">
            <ion-icon name="add-outline"></ion-icon>
            Add New Room
        </button>
    </div>

    <div class="room-container">
        <table class="room-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Room No</th>
                    <th>Room Type</th>
                    <th>Total Beds</th>
                    <th>Bed Size</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th>Today's Price</th>
                    <th>Amenities</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><img src="/api/placeholder/60/60" alt="Room 101" class="room-image"></td>
                    <td>101</td>
                    <td>Deluxe</td>
                    <td>2</td>
                    <td>Queen</td>
                    <td><span class="room-status room-status-free">Free</span></td>
                    <td>$150</td>
                    <td>$120</td>
                    <td>WiFi, AC, TV</td>
                    <td>2024-02-07 10:30</td>
                    <td class="room-actions">
                        <button class="room-btn room-btn-edit" onclick="openModal('edit', 1)">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button class="room-btn room-btn-delete" onclick="deleteRoom(1)">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- Modal for Add/Edit Room -->
<div class="room-modal" id="roomModal">
    <div class="room-modal-content">
        <button class="room-modal-close" onclick="closeModal()">
            <ion-icon name="close-outline"></ion-icon>
        </button>
        <h3 id="modalTitle">Add New Room</h3>
        <form id="roomForm" onsubmit="handleSubmit(event)">
            <div class="room-form-group">
                <label for="roomImage">Room Image</label>
                <input type="file" id="roomImage" class="room-form-control" accept="image/*">
            </div>
            <div class="room-form-group">
                <label for="roomNumber">Room Number</label>
                <input type="text" id="roomNumber" class="room-form-control" required>
            </div>
            <div class="room-form-group">
                <label for="roomType">Room Type</label>
                <select id="roomType" class="room-form-control" required>
                    <option value="Standard">Standard</option>
                    <option value="Deluxe">Deluxe</option>
                    <option value="Suite">Suite</option>
                    <option value="VIP">VIP</option>
                </select>
            </div>
            <div class="room-form-group">
                <label for="totalBeds">Total Beds</label>
                <input type="number" id="totalBeds" class="room-form-control" required>
            </div>
            <div class="room-form-group">
                <label for="bedSize">Bed Size</label>
                <select id="bedSize" class="room-form-control" required>
                    <option value="Single">Single</option>
                    <option value="Double">Double</option>
                    <option value="Queen">Queen</option>
                    <option value="King">King</option>
                </select>
            </div>
            <div class="room-form-group">
                <label for="price">Standard Price</label>
                <input type="number" id="price" class="room-form-control" required>
            </div>
            <div class="room-form-group">
                <label for="todayPrice">Today's Price</label>
                <input type="number" id="todayPrice" class="room-form-control" required>
            </div>
            <div class="room-form-group">
                <label for="amenities">Amenities</label>
                <input type="text" id="amenities" class="room-form-control" placeholder="WiFi, AC, TV, etc.">
            </div>
            <button type="submit" class="room-btn room-btn-primary">Save Room</button>
        </form>
    </div>
</div>