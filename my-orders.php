<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user's food orders from orders table
$sql = "SELECT 
    o.id as order_id,
    o.order_type,
    o.item_id,
    COALESCE(f.food_name, o.item_name) as item_name,
    COALESCE(o.price, f.discount_price, f.price, 0) as price,
    COALESCE(o.quantity, 1) as quantity,
    o.status,
    o.payment_method,
    o.payment_status,
    o.booking_reference,
    COALESCE(o.created_at, NOW()) as order_date,
    o.notes,
    COALESCE(f.image_path, 'images/menu/demoFood.jpg') as image_url
FROM orders o
LEFT JOIN food_items f ON o.item_id = f.id AND o.order_type = 'food'
WHERE o.user_id = ? AND o.order_type = 'food'
ORDER BY o.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$stmt->close();

// Calculate stats
$total_orders = count($orders);
$pending_count = 0;
$completed_count = 0;
$total_spent = 0;

foreach ($orders as $order) {
    if (strtolower($order['status']) === 'pending') {
        $pending_count++;
    }
    if (strtolower($order['status']) === 'delivered' || strtolower($order['status']) === 'completed') {
        $completed_count++;
    }
    $total_spent += ($order['price'] * $order['quantity']);
}

include 'includes/header.php';
?>

<div class="orders-container">
    <div class="page-header">
        <h1><ion-icon name="cart"></ion-icon> My Orders</h1>
        <p>View all your food orders</p>
    </div>
    
    <?php 
    $success = isset($_GET['success']) ? $_GET['success'] : null;
    $error = isset($_GET['error']) ? $_GET['error'] : null;
    
    if ($success): ?>
    <div class="alert alert-success" style="margin: 20px 0; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724;">
        <strong>✓ Success!</strong>
        <?php 
        $success_messages = [
            'payment_complete' => 'Payment completed successfully! Your order has been placed.',
            'order_placed' => 'Order placed successfully!'
        ];
        echo isset($success_messages[$success]) ? $success_messages[$success] : 'Operation completed successfully!';
        ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="alert alert-danger" style="margin: 20px 0; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24;">
        <strong>✗ Error!</strong>
        <?php 
        $error_messages = [
            'no_payment_data' => 'Payment response could not be processed. Please try again.',
            'invalid_data_format' => 'Invalid payment data received.',
            'invalid_json' => 'Payment data format is invalid.',
            'incomplete_response' => 'Incomplete payment response received.',
            'signature_invalid' => 'Payment signature verification failed.',
            'payment_not_completed' => 'Payment was not completed successfully.',
            'payment_processing_failed' => 'An error occurred while processing your payment.'
        ];
        echo isset($error_messages[$error]) ? $error_messages[$error] : 'An error occurred: ' . htmlspecialchars($error);
        ?>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($orders)): ?>
    <div class="view-toggle">
        <button class="view-btn active" onclick="switchView('card')" id="cardViewBtn">
            <ion-icon name="grid"></ion-icon>
            Card View
        </button>
        <button class="view-btn" onclick="switchView('table')" id="tableViewBtn">
            <ion-icon name="list"></ion-icon>
            Table View
        </button>
    </div>
    <?php endif; ?>
    
    <div class="stats-row">
        <div class="stat-card">
            <ion-icon name="receipt" class="stat-icon"></ion-icon>
            <div class="stat-value"><?php echo $total_orders; ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <ion-icon name="hourglass" class="stat-icon"></ion-icon>
            <div class="stat-value"><?php echo $pending_count; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <ion-icon name="checkmark-done-circle" class="stat-icon"></ion-icon>
            <div class="stat-value"><?php echo $completed_count; ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <ion-icon name="cash" class="stat-icon"></ion-icon>
            <div class="stat-value">Rs. <?php echo number_format($total_spent, 2); ?></div>
            <div class="stat-label">Total Spent</div>
        </div>
    </div>
    
    <div class="filter-tabs">
        <div class="filter-tab active">
            <ion-icon name="apps"></ion-icon>
            All Orders
        </div>
        <div class="filter-tab">
            <ion-icon name="hourglass"></ion-icon>
            Pending
        </div>
        <div class="filter-tab">
            <ion-icon name="flame"></ion-icon>
            Preparing
        </div>
        <div class="filter-tab">
            <ion-icon name="checkmark-circle"></ion-icon>
            Ready
        </div>
        <div class="filter-tab">
            <ion-icon name="bicycle"></ion-icon>
            Delivered
        </div>
    </div>
    
    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <ion-icon name="fast-food-outline"></ion-icon>
            <h3>No Orders Yet</h3>
            <p>You haven't placed any food orders. Check out our delicious menu!</p>
            <a href="menu.php" class="btn-browse">
                <ion-icon name="restaurant"></ion-icon>
                Browse Menu
            </a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card" data-status="<?php echo strtolower($order['status']); ?>">
                    <div class="order-header">
                        <div class="order-id">Order #ORD<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></div>
                        <div class="order-date">
                            <ion-icon name="calendar"></ion-icon>
                            <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?>
                        </div>
                    </div>
                    
                    <div class="order-items">
                        <div class="order-item">
                            <?php if (!empty($order['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($order['image_url']); ?>" alt="<?php echo htmlspecialchars($order['item_name']); ?>" class="item-image">
                            <?php else: ?>
                                <div class="item-image" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 50px;">
                                    <ion-icon name="<?php echo $order['order_type'] === 'room' ? 'bed' : ($order['order_type'] === 'table' ? 'restaurant' : 'fast-food'); ?>"></ion-icon>
                                </div>
                            <?php endif; ?>
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($order['item_name']); ?></div>
                                <div class="item-quantity">Qty: <?php echo $order['quantity']; ?>x • RS <?php echo number_format($order['price'], 2); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-footer">
                        <div class="order-total">
                            <span class="total-label">Total:</span>
                            <span class="total-amount">RS <?php echo number_format($order['price'] * $order['quantity'], 2); ?></span>
                        </div>
                        
                        <div class="order-status-group">
                            <div class="order-status status-<?php echo strtolower($order['status']); ?>">
                                <ion-icon name="<?php 
                                    echo $order['status'] === 'pending' ? 'hourglass' : 
                                         ($order['status'] === 'confirmed' ? 'checkmark-circle' : 
                                         ($order['status'] === 'cancelled' ? 'close-circle' : 'flame')); 
                                ?>"></ion-icon>
                                <?php echo ucfirst($order['status']); ?>
                            </div>
                            
                            <div class="order-actions">
                                <button class="btn-action btn-primary" onclick='viewOrderDetails(<?php echo json_encode($order); ?>)'>
                                    <ion-icon name="eye"></ion-icon>
                                    Details
                                </button>
                                <?php if (strtolower($order['status']) === 'delivered'): ?>
                                    <button class="btn-action btn-success">
                                        <ion-icon name="repeat"></ion-icon>
                                        Reorder
                                    </button>
                                <?php elseif (strtolower($order['status']) === 'pending'): ?>
                                    <button class="btn-action btn-secondary" onclick="cancelOrder(<?php echo $order['order_id']; ?>, this)">
                                        <ion-icon name="close-circle"></ion-icon>
                                        Cancel
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Table View -->
        <div class="orders-table-view" style="display: none;">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr data-status="<?php echo strtolower($order['status']); ?>">
                            <td><strong>#ORD<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if (!empty($order['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($order['image_url']); ?>" alt="<?php echo htmlspecialchars($order['item_name']); ?>" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($order['item_name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo $order['quantity']; ?>x</td>
                            <td>RS <?php echo number_format($order['price'], 2); ?></td>
                            <td><strong>RS <?php echo number_format($order['price'] * $order['quantity'], 2); ?></strong></td>
                            <td>
                                <div class="order-status status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </div>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-action btn-primary" onclick='viewOrderDetails(<?php echo json_encode($order); ?>)' style="padding: 8px 12px; font-size: 12px;">
                                        <ion-icon name="eye"></ion-icon>
                                        Details
                                    </button>
                                    <?php if (strtolower($order['status']) === 'pending'): ?>
                                        <button class="btn-action btn-secondary" onclick="cancelOrder(<?php echo $order['order_id']; ?>, this)" style="padding: 8px 12px; font-size: 12px;">
                                            <ion-icon name="close-circle"></ion-icon>
                                            Cancel
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function switchView(view) {
    const cardView = document.querySelector('.orders-list');
    const tableView = document.querySelector('.orders-table-view');
    const cardBtn = document.getElementById('cardViewBtn');
    const tableBtn = document.getElementById('tableViewBtn');
    
    if (!cardView || !tableView || !cardBtn || !tableBtn) return;
    
    if (view === 'card') {
        cardView.style.display = 'flex';
        tableView.style.display = 'none';
        cardBtn.classList.add('active');
        tableBtn.classList.remove('active');
    } else {
        cardView.style.display = 'none';
        tableView.style.display = 'block';
        tableBtn.classList.add('active');
        cardBtn.classList.remove('active');
    }
}

function cancelOrder(orderId, btn) {
    if (!confirm('Are you sure you want to cancel this order?')) {
        return;
    }
    
    const originalText = btn.innerHTML;
    btn.innerHTML = '<ion-icon name="hourglass"></ion-icon> Cancelling...';
    btn.disabled = true;
    
    fetch('api/cancel-booking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'booking_id=' + orderId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ Order cancelled successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        alert('Failed to cancel order. Please try again.');
        btn.innerHTML = originalText;
        btn.disabled = false;
        console.error('Error:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const orderCards = document.querySelectorAll('.order-card');
    
    console.log('Filter tabs found:', filterTabs.length);
    console.log('Order cards found:', orderCards.length);
    
    filterTabs.forEach((tab, index) => {
        tab.addEventListener('click', function() {
            console.log('Filter clicked:', index);
            
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const filterStatus = index === 0 ? 'all' : 
                                index === 1 ? 'pending' : 
                                index === 2 ? 'preparing' :
                                index === 3 ? 'ready' :
                                'delivered';
            
            console.log('Filtering by status:', filterStatus);
            
            orderCards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                
                console.log('Card status:', cardStatus);
                
                if (filterStatus === 'all') {
                    card.style.display = 'flex';
                } else {
                    card.style.display = cardStatus === filterStatus ? 'flex' : 'none';
                }
            });
            
            // Filter table view
            const tableRows = document.querySelectorAll('.orders-table-view tbody tr');
            tableRows.forEach(row => {
                const rowStatus = row.getAttribute('data-status').toLowerCase();
                
                if (filterStatus === 'all') {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = rowStatus === filterStatus ? 'table-row' : 'none';
                }
            });
        });
    });
});

function viewOrderDetails(order) {
    const modal = document.getElementById('orderModal');
    document.getElementById('modalOrderId').textContent = 'ORD' + String(order.order_id).padStart(5, '0');
    document.getElementById('modalOrderDate').textContent = new Date(order.order_date).toLocaleString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
    document.getElementById('modalItemName').textContent = order.item_name;
    document.getElementById('modalQuantity').textContent = order.quantity;
    document.getElementById('modalPrice').textContent = 'RS ' + parseFloat(order.price).toFixed(2);
    document.getElementById('modalTotal').textContent = 'RS ' + (parseFloat(order.price) * parseInt(order.quantity)).toFixed(2);
    document.getElementById('modalStatus').textContent = order.status;
    document.getElementById('modalStatus').className = 'order-status status-' + order.status.toLowerCase();
    document.getElementById('modalNotes').textContent = order.notes || 'No special instructions';
    
    if (order.image_url) {
        document.getElementById('modalItemImage').innerHTML = `<img src="${order.image_url}" alt="${order.item_name}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px;">`;
    } else {
        document.getElementById('modalItemImage').innerHTML = `<div style="width: 100%; height: 200px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 60px;"><ion-icon name="fast-food"></ion-icon></div>`;
    }
    
    document.getElementById('printOrderData').value = JSON.stringify(order);
    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('orderModal').style.display = 'none';
}

function printOrder() {
    const orderData = JSON.parse(document.getElementById('printOrderData').value);
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Order #ORD${String(orderData.order_id).padStart(5, '0')}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; max-width: 800px; margin: 0 auto; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                .header h1 { margin: 0; color: #f5576c; }
                .info-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 10px; background: #f9f9f9; }
                .info-label { font-weight: bold; }
                .item-section { margin: 30px 0; border: 1px solid #ddd; padding: 20px; }
                .total-section { border-top: 2px solid #333; padding-top: 20px; margin-top: 20px; }
                .total-row { display: flex; justify-content: space-between; font-size: 20px; font-weight: bold; }
                @media print { button { display: none; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Hotel Annapurna</h1>
                <p>Order Receipt</p>
            </div>
            <div class="info-row">
                <div><span class="info-label">Order ID:</span> ORD${String(orderData.order_id).padStart(5, '0')}</div>
                <div><span class="info-label">Date:</span> ${new Date(orderData.order_date).toLocaleString()}</div>
            </div>
            <div class="info-row">
                <div><span class="info-label">Status:</span> ${orderData.status}</div>
            </div>
            <div class="item-section">
                <h3>Order Details</h3>
                <div class="info-row">
                    <span>${orderData.item_name}</span>
                    <span>x${orderData.quantity}</span>
                </div>
                <div class="info-row">
                    <span>Unit Price:</span>
                    <span>RS ${parseFloat(orderData.price).toFixed(2)}</span>
                </div>
                ${orderData.notes ? `<div class="info-row"><span class="info-label">Notes:</span> ${orderData.notes}</div>` : ''}
            </div>
            <div class="total-section">
                <div class="total-row">
                    <span>Total Amount:</span>
                    <span>RS ${(parseFloat(orderData.price) * parseInt(orderData.quantity)).toFixed(2)}</span>
                </div>
            </div>
            <div style="margin-top: 40px; text-align: center; color: #666;">
                <p>Thank you for your order!</p>
                <p>Hotel Annapurna - Your comfort is our priority</p>
            </div>
            <button onclick="window.print()" style="margin-top: 20px; padding: 10px 30px; background: #f5576c; color: white; border: none; border-radius: 5px; cursor: pointer;">Print Receipt</button>
        </body>
        </html>
    `);
    printWindow.document.close();
}

window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>

<!-- Order Details Modal -->
<div id="orderModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 20px; padding: 40px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
        <button onclick="closeModal()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 30px; cursor: pointer; color: #999;">&times;</button>
        
        <h2 style="margin: 0 0 30px 0; color: #333; display: flex; align-items: center; gap: 10px;">
            <ion-icon name="receipt" style="font-size: 32px; color: #f5576c;"></ion-icon>
            Order Details
        </h2>
        
        <div id="modalItemImage" style="margin-bottom: 20px;"></div>
        
        <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: #666;">Order ID:</span>
                <strong id="modalOrderId"></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: #666;">Date:</span>
                <strong id="modalOrderDate"></strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #666;">Status:</span>
                <div id="modalStatus" class="order-status"></div>
            </div>
        </div>
        
        <div style="border: 2px solid #f093fb; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
            <h3 style="margin: 0 0 15px 0; color: #333;">Item Information</h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: #666;">Item Name:</span>
                <strong id="modalItemName"></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: #666;">Quantity:</span>
                <strong id="modalQuantity"></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: #666;">Unit Price:</span>
                <strong id="modalPrice"></strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 2px solid #ddd; margin-top: 10px;">
                <span style="color: #666; font-size: 18px;">Total:</span>
                <strong id="modalTotal" style="font-size: 24px; color: #f5576c;"></strong>
            </div>
        </div>
        
        <div style="background: #fff3cd; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <div style="color: #856404; font-weight: 600; margin-bottom: 5px;">Special Instructions:</div>
            <div id="modalNotes" style="color: #856404;"></div>
        </div>
        
        <input type="hidden" id="printOrderData">
        
        <div style="display: flex; gap: 10px;">
            <button onclick="printOrder()" style="flex: 1; padding: 15px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <ion-icon name="print" style="font-size: 20px;"></ion-icon>
                Print Receipt
            </button>
            <button onclick="closeModal()" style="flex: 1; padding: 15px; background: #6c757d; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer;">
                Close
            </button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
