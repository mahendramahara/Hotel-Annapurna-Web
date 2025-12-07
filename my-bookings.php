<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user's bookings from orders table
$sql = "SELECT 
    o.id as booking_id,
    o.order_type as booking_type,
    o.item_id,
    CASE 
        WHEN o.order_type = 'room' THEN CONCAT(COALESCE(r.room_type, 'Room'), ' - Room ', COALESCE(r.room_no, o.item_id))
        WHEN o.order_type = 'table' THEN CONCAT(COALESCE(t.location, 'Table'), ' - Table ', COALESCE(t.table_no, o.item_id))
        ELSE o.item_name
    END as item_name,
    o.price,
    o.status,
    COALESCE(o.created_at, NOW()) as booking_date,
    o.notes,
    CASE 
        WHEN o.order_type = 'room' THEN COALESCE(r.image_path, 'images/rooms/demoRoom.jpg')
        WHEN o.order_type = 'table' THEN COALESCE(t.image_path, 'images/tables/demoTable.jpg')
        ELSE NULL
    END as image_url
FROM orders o
LEFT JOIN rooms r ON o.item_id = r.id AND o.order_type = 'room'
LEFT JOIN tables t ON o.item_id = t.id AND o.order_type = 'table'
WHERE o.user_id = ? AND o.order_type IN ('room', 'table')
ORDER BY o.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}
$stmt->close();

// Calculate stats
$total_bookings = count($bookings);
$room_bookings = 0;
$table_bookings = 0;
$pending_count = 0;
$confirmed_count = 0;

foreach ($bookings as $booking) {
    if (strtolower($booking['booking_type']) === 'room') {
        $room_bookings++;
    } elseif (strtolower($booking['booking_type']) === 'table') {
        $table_bookings++;
    }
    if (strtolower($booking['status']) === 'pending') {
        $pending_count++;
    }
    if (strtolower($booking['status']) === 'confirmed') {
        $confirmed_count++;
    }
}

include 'includes/header.php';
?>

<div class="bookings-container">
    <div class="page-header">
        <h1><ion-icon name="calendar"></ion-icon> My Bookings</h1>
        <p>View and manage all your room and table reservations</p>
    </div>
    
    <?php 
    $success = isset($_GET['success']) ? $_GET['success'] : null;
    $error = isset($_GET['error']) ? $_GET['error'] : null;
    
    if ($success): ?>
    <div class="alert alert-success" style="margin: 20px 0; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724;">
        <strong>✓ Success!</strong>
        <?php 
        $success_messages = [
            'payment_complete' => 'Payment completed successfully! Your booking has been confirmed.',
            'booking_confirmed' => 'Booking confirmed successfully!'
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
            'payment_processing_failed' => 'An error occurred while processing your payment.',
            'order_not_found' => 'Order could not be found.',
            'invalid_order_format' => 'Invalid order format.',
            'invalid_booking_id' => 'Invalid booking ID.',
            'payment_confirmation_failed' => 'Failed to confirm payment.'
        ];
        echo isset($error_messages[$error]) ? $error_messages[$error] : 'An error occurred: ' . htmlspecialchars($error);
        ?>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($bookings)): ?>
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
            <ion-icon name="calendar" class="stat-icon"></ion-icon>
            <div class="stat-value"><?php echo $total_bookings; ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <ion-icon name="bed" class="stat-icon"></ion-icon>
            <div class="stat-value"><?php echo $room_bookings; ?></div>
            <div class="stat-label">Rooms</div>
        </div>
        <div class="stat-card">
            <ion-icon name="restaurant" class="stat-icon"></ion-icon>
            <div class="stat-value"><?php echo $table_bookings; ?></div>
            <div class="stat-label">Tables</div>
        </div>
        <div class="stat-card">
            <ion-icon name="checkmark-done-circle" class="stat-icon"></ion-icon>
            <div class="stat-value"><?php echo $confirmed_count; ?></div>
            <div class="stat-label">Confirmed</div>
        </div>
    </div>
    
    <div class="filter-tabs">
        <div class="filter-tab active">
            <ion-icon name="apps"></ion-icon>
            All Bookings
        </div>
        <div class="filter-tab">
            <ion-icon name="bed"></ion-icon>
            Rooms
        </div>
        <div class="filter-tab">
            <ion-icon name="restaurant"></ion-icon>
            Tables
        </div>
        <div class="filter-tab">
            <ion-icon name="time"></ion-icon>
            Pending
        </div>
        <div class="filter-tab">
            <ion-icon name="checkmark-circle"></ion-icon>
            Confirmed
        </div>
    </div>
    
    <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <ion-icon name="calendar-outline"></ion-icon>
            <h3>No Bookings Yet</h3>
            <p>You haven't made any bookings. Start exploring our rooms and tables!</p>
            <a href="booking.php" class="btn-browse">
                <ion-icon name="add-circle"></ion-icon>
                Make a Booking
            </a>
        </div>
    <?php else: ?>
        <div class="bookings-grid">
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card" data-type="<?php echo strtolower($booking['booking_type']); ?>" data-status="<?php echo strtolower($booking['status']); ?>">
                    <?php if (!empty($booking['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="<?php echo htmlspecialchars($booking['item_name']); ?>" class="booking-image">
                    <?php else: ?>
                        <div class="booking-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 60px;">
                            <ion-icon name="<?php echo $booking['booking_type'] === 'room' ? 'bed' : ($booking['booking_type'] === 'table' ? 'restaurant' : 'fast-food'); ?>"></ion-icon>
                        </div>
                    <?php endif; ?>
                    
                    <div class="booking-content">
                        <span class="booking-type-badge">
                            <ion-icon name="<?php echo $booking['booking_type'] === 'room' ? 'bed' : ($booking['booking_type'] === 'table' ? 'restaurant' : 'fast-food'); ?>"></ion-icon>
                            <?php echo ucfirst($booking['booking_type']); ?> Booking
                        </span>
                        
                        <h3 class="booking-title">
                            <?php echo htmlspecialchars($booking['item_name']); ?>
                        </h3>
                        
                        <div class="booking-details">
                            <div class="booking-detail-row">
                                <ion-icon name="calendar"></ion-icon>
                                <span>Booked on: <?php echo date('M d, Y h:i A', strtotime($booking['booking_date'])); ?></span>
                            </div>
                            <div class="booking-detail-row">
                                <ion-icon name="person"></ion-icon>
                                <span>Guest: <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            </div>
                            <?php if (!empty($booking['notes'])): ?>
                            <div class="booking-detail-row">
                                <ion-icon name="information-circle"></ion-icon>
                                <span><?php echo htmlspecialchars($booking['notes']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="booking-footer">
                            <div class="booking-price">
                                RS <?php echo number_format($booking['price'], 2); ?>
                            </div>
                            <div class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </div>
                        </div>
                        
                        <div class="booking-actions">
                            <button class="btn-action btn-primary" onclick='viewBookingDetails(<?php echo json_encode($booking); ?>)'>
                                <ion-icon name="eye"></ion-icon>
                                View Details
                            </button>
                            <?php if (strtolower($booking['status']) === 'pending'): ?>
                            <button class="btn-action" style="background: #60a5fa; color: white;" onclick="checkEsewaStatus(<?php echo $booking['booking_id']; ?>, this)">
                                <ion-icon name="refresh"></ion-icon>
                                Check Payment
                            </button>
                            <?php endif; ?>
                            <?php if (strtolower($booking['status']) === 'pending' || strtolower($booking['status']) === 'confirmed'): ?>
                            <button class="btn-action btn-danger" onclick="cancelBooking(<?php echo $booking['booking_id']; ?>, this)">
                                <ion-icon name="close-circle"></ion-icon>
                                Cancel
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Table View -->
        <div class="bookings-table-view">
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Type</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr data-type="<?php echo strtolower($booking['booking_type']); ?>" data-status="<?php echo strtolower($booking['status']); ?>">
                            <td><strong>#BK<?php echo str_pad($booking['booking_id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                            <td>
                                <span class="booking-type-badge">
                                    <ion-icon name="<?php echo $booking['booking_type'] === 'room' ? 'bed' : 'restaurant'; ?>"></ion-icon>
                                    <?php echo ucfirst($booking['booking_type']); ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if (!empty($booking['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="<?php echo htmlspecialchars($booking['item_name']); ?>" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($booking['item_name']); ?></span>
                                </div>
                            </td>
                            <td><strong>RS <?php echo number_format($booking['price'], 2); ?></strong></td>
                            <td>
                                <div class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </div>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-action btn-primary" onclick='viewBookingDetails(<?php echo json_encode($booking); ?>)' style="padding: 6px 10px; font-size: 11px;">
                                        <ion-icon name="eye"></ion-icon>
                                        Details
                                    </button>
                                    <?php if (strtolower($booking['status']) === 'pending' || strtolower($booking['status']) === 'confirmed'): ?>
                                        <button class="btn-action btn-danger" onclick="cancelBooking(<?php echo $booking['booking_id']; ?>, this)" style="padding: 6px 10px; font-size: 11px;">
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
    const cardView = document.querySelector('.bookings-grid');
    const tableView = document.querySelector('.bookings-table-view');
    const cardBtn = document.getElementById('cardViewBtn');
    const tableBtn = document.getElementById('tableViewBtn');
    
    if (!cardView || !tableView || !cardBtn || !tableBtn) return;
    
    if (view === 'card') {
        cardView.style.display = 'grid';
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

document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    
    filterTabs.forEach((tab, index) => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const filterType = index === 0 ? 'all' : 
                              index === 1 ? 'room' : 
                              index === 2 ? 'table' :
                              index === 3 ? 'pending' :
                              'confirmed';
            
            filterBookings(filterType);
        });
    });
    
    function filterBookings(filterType) {
        const bookingCards = document.querySelectorAll('.booking-card');
        const tableRows = document.querySelectorAll('.bookings-table-view tbody tr');
        
        bookingCards.forEach(card => {
            const cardType = card.getAttribute('data-type');
            const cardStatus = card.getAttribute('data-status');
            let shouldShow = false;
            
            if (filterType === 'all') {
                shouldShow = true;
            } else if (filterType === 'pending' || filterType === 'confirmed') {
                shouldShow = (cardStatus === filterType);
            } else {
                shouldShow = (cardType === filterType);
            }
            
            card.style.display = shouldShow ? 'flex' : 'none';
        });
        
        tableRows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            const rowStatus = row.getAttribute('data-status');
            let shouldShow = false;
            
            if (filterType === 'all') {
                shouldShow = true;
            } else if (filterType === 'pending' || filterType === 'confirmed') {
                shouldShow = (rowStatus === filterType);
            } else {
                shouldShow = (rowType === filterType);
            }
            
            row.style.display = shouldShow ? 'table-row' : 'none';
        });
    }
});

function viewBookingDetails(booking) {
    const modal = document.getElementById('bookingModal');
    document.getElementById('modalBookingId').textContent = 'BK' + String(booking.booking_id).padStart(5, '0');
    document.getElementById('modalBookingDate').textContent = new Date(booking.booking_date).toLocaleString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
    document.getElementById('modalBookingType').textContent = booking.booking_type.toUpperCase();
    document.getElementById('modalItemName').textContent = booking.item_name;
    document.getElementById('modalPrice').textContent = 'RS ' + parseFloat(booking.price).toFixed(2);
    document.getElementById('modalStatus').textContent = booking.status;
    document.getElementById('modalStatus').className = 'booking-status status-' + booking.status.toLowerCase();
    document.getElementById('modalNotes').textContent = booking.notes || 'No special requests';
    
    if (booking.image_url) {
        document.getElementById('modalBookingImage').innerHTML = `<img src="${booking.image_url}" alt="${booking.item_name}" style="width: 100%; height: 250px; object-fit: cover; border-radius: 10px;">`;
    } else {
        const icon = booking.booking_type === 'room' ? 'bed' : (booking.booking_type === 'table' ? 'restaurant' : 'calendar');
        document.getElementById('modalBookingImage').innerHTML = `<div style="width: 100%; height: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 80px;"><ion-icon name="${icon}"></ion-icon></div>`;
    }
    
    modal.style.display = 'flex';
}

function closeBookingModal() {
    document.getElementById('bookingModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('bookingModal');
    if (event.target === modal) {
        closeBookingModal();
    }
}

function checkEsewaStatus(bookingId, btn) {
    const transactionUuid = prompt('Enter your eSewa transaction ID\\n(Format: booking_id-YYMMDD-HHMMSS):');
    
    if (!transactionUuid) return;
    
    const originalText = btn.innerHTML;
    btn.innerHTML = '<ion-icon name="hourglass"></ion-icon> Checking...';
    btn.disabled = true;
    
    fetch('api/esewa-status-check.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `booking_id=${bookingId}&transaction_uuid=${transactionUuid}`
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.success) {
            const statusMessages = {
                'COMPLETE': '✓ Payment Completed Successfully!\\nRef ID: ' + data.ref_id,
                'PENDING': 'Payment is still pending. Please complete the payment.',
                'FULL_REFUND': 'Payment has been fully refunded.',
                'PARTIAL_REFUND': 'Payment has been partially refunded.',
                'AMBIGUOUS': 'Payment is in ambiguous state. Please contact support.',
                'NOT_FOUND': 'Transaction not found or session expired.',
                'CANCELED': 'Transaction was canceled.'
            };
            
            alert(statusMessages[data.status] || data.message);
            
            if (data.status === 'COMPLETE') {
                location.reload();
            } else {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } else {
            alert('Error: ' + data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('Failed to check payment status. Please try again.');
        console.error('Error:', error);
    });
}

function cancelBooking(bookingId, btn) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<ion-icon name="hourglass"></ion-icon> Cancelling...';
        btn.disabled = true;
        fetch('api/cancel-booking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'booking_id=' + bookingId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Booking cancelled successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            alert('Failed to cancel booking. Please try again.');
            btn.innerHTML = originalText;
            btn.disabled = false;
            console.error('Error:', error);
        });
    }
}
</script>

<!-- Booking Details Modal -->
<div id="bookingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 20px; padding: 40px; max-width: 650px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
        <button onclick="closeBookingModal()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 30px; cursor: pointer; color: #999;">&times;</button>
        
        <h2 style="margin: 0 0 30px 0; color: #333; display: flex; align-items: center; gap: 10px;">
            <ion-icon name="calendar" style="font-size: 32px; color: #764ba2;"></ion-icon>
            Booking Details
        </h2>
        
        <div id="modalBookingImage" style="margin-bottom: 25px;"></div>
        
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 15px; margin-bottom: 25px; color: white;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <span style="opacity: 0.9;">Booking ID:</span>
                <strong id="modalBookingId" style="font-size: 18px;"></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <span style="opacity: 0.9;">Date:</span>
                <strong id="modalBookingDate"></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <span style="opacity: 0.9;">Type:</span>
                <strong id="modalBookingType"></strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="opacity: 0.9;">Status:</span>
                <div id="modalStatus" class="booking-status"></div>
            </div>
        </div>
        
        <div style="border: 2px solid #667eea; border-radius: 12px; padding: 25px; margin-bottom: 25px;">
            <h3 style="margin: 0 0 20px 0; color: #764ba2; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="information-circle"></ion-icon>
                Booking Information
            </h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                <span style="color: #666;">Item:</span>
                <strong id="modalItemName" style="color: #333;"></strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 15px; border-bottom: 2px solid #764ba2;">
                <span style="color: #666; font-size: 18px;">Amount:</span>
                <strong id="modalPrice" style="font-size: 26px; color: #764ba2;"></strong>
            </div>
        </div>
        
        <div style="background: #fef3c7; padding: 18px; border-radius: 10px; margin-bottom: 25px; border-left: 4px solid #f59e0b;">
            <div style="color: #92400e; font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                <ion-icon name="chatbox-ellipses"></ion-icon>
                Special Requests:
            </div>
            <div id="modalNotes" style="color: #78350f; line-height: 1.5;"></div>
        </div>
        
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button onclick="closeBookingModal()" style="padding: 16px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: transform 0.2s;">
                Close
            </button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>