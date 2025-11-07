<div class="request-data-table">
    <div class="title">
        <ion-icon name="list-outline" class="title-icon"></ion-icon>
        <span class="text">Recent Service Requests</span>
    </div>
    <div class="service-requests-container">
        <div class="service-tab-switcher">
            <div class="service-tab active" data-tab="food-orders">Food Orders</div>
            <div class="service-tab" data-tab="table-bookings">Table Bookings</div>
            <div class="service-tab" data-tab="room-bookings">Room Bookings</div>
        </div>

        <div class="service-table-wrapper">
            <!-- Food Orders Table -->
            <table class="service-table active" id="food-orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Food Items</th>
                        <th>Ordered Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>FO-001</td>
                        <td>John Doe</td>
                        <td>+91 9876543210</td>
                        <td>Butter Chicken, Naan</td>
                        <td>3 min ago</td>
                        <td><span class="service-status service-status-pending">Pending</span></td>
                        <td class="service-actions">
                            <ion-icon name="eye-outline" title="View Details"></ion-icon>
                            <ion-icon name="trash-outline" title="Delete"></ion-icon>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Table Bookings Table -->
            <table class="service-table" id="table-bookings-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Table Number</th>
                        <th>Booked For</th>
                        <th>Request Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>TB-001</td>
                        <td>Jane Smith</td>
                        <td>+91 8765432109</td>
                        <td>Table 5</td>
                        <td>Feb 10, 7:30 PM</td>
                        <td>2 hrs ago</td>
                        <td><span class="service-status service-status-confirmed">Confirmed</span></td>
                        <td class="service-actions">
                            <ion-icon name="eye-outline" title="View Details"></ion-icon>
                            <ion-icon name="trash-outline" title="Delete"></ion-icon>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Room Bookings Table -->
            <table class="service-table" id="room-bookings-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Room Number</th>
                        <th>Booked For</th>
                        <th>Request Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>RB-001</td>
                        <td>Alex Johnson</td>
                        <td>+91 7654321098</td>
                        <td>Room 201</td>
                        <td>Feb 15-18, 2024</td>
                        <td>1 day ago</td>
                        <td><span class="service-status service-status-completed">Completed</span></td>
                        <td class="service-actions">
                            <ion-icon name="eye-outline" title="View Details"></ion-icon>
                            <ion-icon name="trash-outline" title="Delete"></ion-icon>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="service-search-container">
            <input type="text" class="service-search-input" placeholder="Search by Order/Request ID">
            <button class="service-search-button">Search</button>
        </div>

        <div class="service-update-container">
            <select class="service-status-dropdown">
                <option value="">Change Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <textarea class="service-response-message" placeholder="Write a response message"></textarea>
            <button class="service-submit-update">Submit Update</button>
        </div>
    </div>
</div>
