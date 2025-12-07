<?php
require_once('includes/auth-guard.php');
require_once('../config/db.php');

$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders");
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$stmt = $conn->prepare("SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.email, u.contact,
    CASE 
        WHEN o.order_type = 'food' THEN f.image_path
        WHEN o.order_type = 'room' THEN r.image_path
        WHEN o.order_type = 'table' THEN t.image_path
        ELSE NULL
    END as image_url
FROM orders o 
LEFT JOIN users u ON o.user_id = u.id
LEFT JOIN food_items f ON o.item_id = f.id AND o.order_type = 'food'
LEFT JOIN rooms r ON o.item_id = r.id AND o.order_type = 'room'
LEFT JOIN tables t ON o.item_id = t.id AND o.order_type = 'table'
ORDER BY o.created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$food_orders = array_filter($orders, fn($o) => $o['order_type'] === 'food');
$table_orders = array_filter($orders, fn($o) => $o['order_type'] === 'table');
$room_orders = array_filter($orders, fn($o) => $o['order_type'] === 'room');
?>

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
            <table class="service-table active" id="food-orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Image</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Food Items</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Ordered Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($food_orders as $order): ?>
                    <tr>
                        <td>FO-<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td>
                            <?php if (!empty($order['image_url'])): ?>
                                <img src="../<?= htmlspecialchars($order['image_url']) ?>" alt="<?= htmlspecialchars($order['item_name']) ?>" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white;">
                                    <ion-icon name="fast-food" style="font-size: 24px;"></ion-icon>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['email']) ?></td>
                        <td><?= htmlspecialchars($order['contact']) ?></td>
                        <td><?= htmlspecialchars($order['item_name']) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td>RS <?= number_format($order['price'], 2) ?></td>
                        <td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><span class="service-status service-status-<?= strtolower($order['status']) ?>"><?= ucfirst($order['status']) ?></span></td>
                        <td class="service-actions">
                            <ion-icon name="eye-outline" title="View Details" onclick="viewOrder(<?= $order['id'] ?>)"></ion-icon>
                            <ion-icon name="create-outline" title="Edit Status" onclick="editOrderStatus(<?= $order['id'] ?>)"></ion-icon>
                            <ion-icon name="trash-outline" title="Delete" onclick="deleteOrder(<?= $order['id'] ?>)"></ion-icon>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($food_orders)): ?>
                    <tr>
                        <td colspan="11" style="text-align:center;">No food orders found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <table class="service-table" id="table-bookings-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Image</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Table Details</th>
                        <th>Price</th>
                        <th>Booking Date</th>
                        <th>Request Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($table_orders as $order): ?>
                    <?php 
                        $notes_data = json_decode($order['notes'], true) ?? [];
                        $booking_date = $notes_data['check_in'] ?? 'Same Day';
                    ?>
                    <tr>
                        <td>TB-<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td>
                            <?php if (!empty($order['image_url'])): ?>
                                <img src="../<?= htmlspecialchars($order['image_url']) ?>" alt="<?= htmlspecialchars($order['item_name']) ?>" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; border-radius: 8px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white;">
                                    <ion-icon name="restaurant" style="font-size: 24px;"></ion-icon>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['email']) ?></td>
                        <td><?= htmlspecialchars($order['contact']) ?></td>
                        <td><?= htmlspecialchars($order['item_name']) ?></td>
                        <td>RS <?= number_format($order['price'], 2) ?></td>
                        <td><?= $booking_date !== 'Same Day' ? date('M d, Y', strtotime($booking_date)) : 'Same Day' ?></td>
                        <td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><span class="service-status service-status-<?= strtolower($order['status']) ?>"><?= ucfirst($order['status']) ?></span></td>
                        <td class="service-actions">
                            <ion-icon name="eye-outline" title="View Details" onclick="viewOrder(<?= $order['id'] ?>)"></ion-icon>
                            <ion-icon name="create-outline" title="Edit Status" onclick="editOrderStatus(<?= $order['id'] ?>)"></ion-icon>
                            <ion-icon name="trash-outline" title="Delete" onclick="deleteOrder(<?= $order['id'] ?>)"></ion-icon>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($table_orders)): ?>
                    <tr>
                        <td colspan="11" style="text-align:center;">No table bookings found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <table class="service-table" id="room-bookings-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Image</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Room Details</th>
                        <th>Price</th>
                        <th>Check-in Date</th>
                        <th>Request Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($room_orders as $order): ?>
                    <?php 
                        $notes_data = json_decode($order['notes'], true) ?? [];
                        $check_in_date = $notes_data['check_in'] ?? 'Today';
                    ?>
                    <tr>
                        <td>RB-<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td>
                            <?php if (!empty($order['image_url'])): ?>
                                <img src="../<?= htmlspecialchars($order['image_url']) ?>" alt="<?= htmlspecialchars($order['item_name']) ?>" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; border-radius: 8px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white;">
                                    <ion-icon name="bed" style="font-size: 24px;"></ion-icon>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['email']) ?></td>
                        <td><?= htmlspecialchars($order['contact']) ?></td>
                        <td><?= htmlspecialchars($order['item_name']) ?></td>
                        <td>RS <?= number_format($order['price'], 2) ?></td>
                        <td><?= $check_in_date !== 'Today' ? date('M d, Y', strtotime($check_in_date)) : 'Today' ?></td>
                        <td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><span class="service-status service-status-<?= strtolower($order['status']) ?>"><?= ucfirst($order['status']) ?></span></td>
                        <td class="service-actions">
                            <ion-icon name="eye-outline" title="View Details" onclick="viewOrder(<?= $order['id'] ?>)"></ion-icon>
                            <ion-icon name="create-outline" title="Edit Status" onclick="editOrderStatus(<?= $order['id'] ?>)"></ion-icon>
                            <ion-icon name="trash-outline" title="Delete" onclick="deleteOrder(<?= $order['id'] ?>)"></ion-icon>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($room_orders)): ?>
                    <tr>
                        <td colspan="11" style="text-align:center;">No room bookings found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?page=requests&p=<?= $page-1 ?>" class="page-btn">Previous</a>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=requests&p=<?= $i ?>" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if($page < $total_pages): ?>
                <a href="?page=requests&p=<?= $page+1 ?>" class="page-btn">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('.service-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.service-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.service-table').forEach(t => t.classList.remove('active'));
        
        this.classList.add('active');
        const tabName = this.getAttribute('data-tab');
        document.getElementById(tabName + '-table').classList.add('active');
    });
});

function viewOrder(id) {
    fetch(`../api/admin-orders.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const order = data.order;
                const orderType = order.order_type.toUpperCase();
                alert(`📋 ${orderType} ORDER DETAILS\n\n` +
                      `Order ID: ${order.id}\n` +
                      `Customer: ${order.customer_name}\n` +
                      `Email: ${order.email || 'N/A'}\n` +
                      `Contact: ${order.contact}\n` +
                      `Item: ${order.item_name}\n` +
                      `Quantity: ${order.quantity}\n` +
                      `Price: RS ${parseFloat(order.price).toFixed(2)}\n` +
                      `Total: RS ${(order.price * order.quantity).toFixed(2)}\n` +
                      `Status: ${order.status.toUpperCase()}\n` +
                      `Booking Date: ${order.delivery_date || 'Same day'}\n` +
                      `Ordered: ${order.created_at}\n` +
                      `Notes: ${order.notes || 'None'}`);
            } else {
                alert('❌ Error: ' + (data.message || 'Could not fetch order details'));
            }
        })
        .catch(error => {
            alert('❌ Failed to fetch order details. Please try again.');
            console.error('Error:', error);
        });
}

function editOrderStatus(id) {
    const statusOptions = ['pending', 'confirmed', 'completed', 'cancelled'];
    const currentStatus = event.target.closest('tr').querySelector('.service-status').textContent.toLowerCase();
    
    let message = `Change order status:\n\nCurrent Status: ${currentStatus}\n\nSelect new status:\n`;
    statusOptions.forEach((status, index) => {
        message += `${index + 1}. ${status.charAt(0).toUpperCase() + status.slice(1)}\n`;
    });
    
    const choice = prompt(message + '\nEnter number (1-4) or status name:');
    if(!choice) return;
    
    let newStatus;
    if(choice >= 1 && choice <= 4) {
        newStatus = statusOptions[choice - 1];
    } else {
        newStatus = choice.toLowerCase().trim();
    }
    
    if(!statusOptions.includes(newStatus)) {
        alert('❌ Invalid status. Please use: pending, confirmed, completed, or cancelled');
        return;
    }
    
    if(newStatus === currentStatus) {
        alert('ℹ️ Status is already set to: ' + newStatus);
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('id', id);
    formData.append('status', newStatus);
    
    fetch('../api/admin-orders.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Failed to update status. Please try again.');
        console.error('Error:', error);
    });
}

function deleteOrder(id) {
    const orderRow = event.target.closest('tr');
    const orderInfo = orderRow.cells[3].textContent;
    
    if(!confirm(`⚠️ Are you sure you want to DELETE this order?\n\nOrder: ${orderInfo}\n\nThis action cannot be undone!`)) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('../api/admin-orders.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Failed to delete order. Please try again.');
        console.error('Error:', error);
    });
}
</script>
