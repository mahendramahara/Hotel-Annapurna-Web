<?php 
require_once("includes/sidebar.php"); 
require_once('../config/db.php');

$_GET['blogs_page'] = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;

$customers_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'")->fetch_assoc()['total'];
$services_count = $conn->query("SELECT COUNT(*) as total FROM food_items")->fetch_assoc()['total'] + 
                  $conn->query("SELECT COUNT(*) as total FROM rooms")->fetch_assoc()['total'] + 
                  $conn->query("SELECT COUNT(*) as total FROM tables")->fetch_assoc()['total'];
$staff_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'staff'")->fetch_assoc()['total'];
$blogs_count = $conn->query("SELECT COUNT(*) as total FROM blogs")->fetch_assoc()['total'];

$recent_orders = $conn->query("SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name 
                               FROM orders o 
                               LEFT JOIN users u ON o.user_id = u.id 
                               ORDER BY o.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$recent_activities = $conn->query("SELECT al.*, CONCAT(u.first_name, ' ', u.last_name) as user_name 
                                   FROM activity_logs al 
                                   LEFT JOIN users u ON al.user_id = u.id 
                                   ORDER BY al.created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
?>

<section class="dashboard">
    <div class="container">
        <!-- Dashboard Section Wrapper -->
        <div class="dashboard-section">
        <div class="overview">
            <div class="title">
                <ion-icon name="speedometer"></ion-icon>
                <span class="text">Dashboard Overview</span>
            </div>
            <div class="boxes">
                <div class="box box1">
                    <ion-icon name="people-outline"></ion-icon>
                    <span class="text">Total Customers</span>
                    <span class="number"><?= $customers_count ?></span>
                </div>
                <div class="box box2">
                    <ion-icon name="restaurant-outline"></ion-icon>
                    <span class="text">Total Services</span>
                    <span class="number"><?= $services_count ?></span>
                </div>
                <div class="box box3">
                    <ion-icon name="people-circle-outline"></ion-icon>
                    <span class="text">Total Staff</span>
                    <span class="number"><?= $staff_count ?></span>
                </div>
                <div class="box box4">
                    <ion-icon name="newspaper-outline"></ion-icon>
                    <span class="text">Total Blogs</span>
                    <span class="number"><?= $blogs_count ?></span>
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
                        <?php if(!empty($recent_orders)): ?>
                            <?php foreach($recent_orders as $order): ?>
                            <tr>
                                <td>#ORD<?= str_pad($order['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></td>
                                <td><?= htmlspecialchars(ucfirst($order['order_type'])) ?></td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td><span class="status-badge status-<?= strtolower($order['status']) ?>">
                                        <ion-icon name="<?= $order['status'] == 'pending' ? 'time-outline' : ($order['status'] == 'confirmed' ? 'checkmark-circle-outline' : 'checkbox-outline') ?>"></ion-icon><?= htmlspecialchars(ucfirst($order['status'])) ?>
                                    </span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action view" title="View Details" onclick="viewOrder(<?= $order['id'] ?>)">
                                            <ion-icon name="eye-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action delete" title="Delete" onclick="deleteOrder(<?= $order['id'] ?>)">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">No recent service requests</td>
                            </tr>
                        <?php endif; ?>
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
                    <?php if(!empty($recent_activities)): ?>
                        <?php foreach($recent_activities as $activity): ?>
                        <div class="log-entry">
                            <div class="log-time">
                                <span><?= date('h:i A', strtotime($activity['created_at'])) ?></span>
                                <span class="date"><?= date('M d, Y', strtotime($activity['created_at'])) ?></span>
                            </div>
                            <div class="log-message">
                                <span class="highlight"><?= htmlspecialchars($activity['user_name'] ?? 'System') ?></span> 
                                <?= htmlspecialchars($activity['description']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="log-entry">
                            <div class="log-message" style="text-align:center;width:100%;">
                                No recent activities
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
        <!-- End Dashboard Section Wrapper -->

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
            <?php require_once("sections/coupons.php"); ?>
            <?php require_once("sections/profile.php"); ?>
    </div>
</section>

<script src="assets/js/script.js?v=<?= time() ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>