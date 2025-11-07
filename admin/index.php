<?php require_once("includes/sidebar.php"); ?>

<section class="dashboard">
    <div class="container">
        <div class="overview">
            <div class="title">
                <ion-icon name="speedometer"></ion-icon>
                <span class="text">Dashboard Overview</span>
            </div>
            <div class="boxes">
                <div class="box box1">
                    <ion-icon name="people-outline"></ion-icon>
                    <span class="text">Total Customers</span>
                    <span class="number">2845</span>
                </div>
                <div class="box box2">
                    <ion-icon name="restaurant-outline"></ion-icon>
                    <span class="text">Total Services</span>
                    <span class="number">156</span>
                </div>
                <div class="box box3">
                    <ion-icon name="people-circle-outline"></ion-icon>
                    <span class="text">Total Staff</span>
                    <span class="number">45</span>
                </div>
                <div class="box box4">
                    <ion-icon name="newspaper-outline"></ion-icon>
                    <span class="text">Total Blogs</span>
                    <span class="number">28</span>
                </div>
            </div>
        </div>

        <!-- Service Requests Section -->
        <div class="data-table requestsTable">
            <div class="title">
                <ion-icon name="calendar-outline"></ion-icon>
                <span class="text">Recent Service Requests</span>
            </div>
            <div class="table-design">
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Customer Name</th>
                            <th>Service Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#REQ001</td>
                            <td>John Doe</td>
                            <td>Table Booking</td>
                            <td>Feb 04, 2025</td>
                            <td><span class="status-badge status-pending">
                                    <ion-icon name="time-outline"></ion-icon>Pending
                                </span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action view" title="View Details">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </button>
                                    <button class="btn-action delete" title="Delete">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activity Log Section -->
        <div class="data-table activity-log">
            <div class="title">
                <ion-icon name="time-outline"></ion-icon>
                <span class="text">Recent Activities</span>
            </div>
            <div class="table-design">
                <div class="log-entries">
                    <div class="log-entry">
                        <div class="log-time">
                            <span>10:45 AM</span>
                            <span class="date">Feb 04, 2025</span>
                        </div>
                        <div class="log-message">
                            <span class="highlight">John Doe</span> created a new table booking request <span class="highlight">#REQ001</span>
                        </div>
                    </div>
                    <div class="log-entry">
                        <div class="log-time">
                            <span>10:30 AM</span>
                            <span class="date">Feb 04, 2025</span>
                        </div>
                        <div class="log-message">
                            <span class="highlight">Admin</span> confirmed room booking request <span class="highlight">#REQ002</span>
                        </div>
                    </div>
                </div>
                <button class="view-more-btn">
                    View More Activities
                    <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>
            </div>
        </div>

        <!-- Additional sections can be added following the same pattern -->
         <!-- Request Section -->
          <?php require_once("sections/requests.php"); ?>
          <?php require_once("sections/menu_items.php"); ?>
            <?php require_once("sections/tables.php"); ?>
            <?php require_once("sections/rooms.php"); ?>
            <?php require_once("sections/staffs.php"); ?>
            <?php require_once("sections/blogs.php"); ?>
            <?php require_once("sections/customers.php"); ?>
            <?php require_once("sections/reviews.php"); ?>
            <?php require_once("sections/contacts.php"); ?>
    </div>
</section>

<script src="assets/js/script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>