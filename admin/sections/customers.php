<?php
require_once('includes/auth-guard.php');
require_once('../config/db.php');

parse_str(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY) ?? '', $query_params);
$page = isset($query_params['p']) ? (int)$query_params['p'] : 1;
if(isset($_GET['p'])) $page = (int)$_GET['p'];
$limit = 10;
$offset = ($page - 1) * $limit;

$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$stmt = $conn->prepare("SELECT u.*, COUNT(o.id) as order_count FROM users u LEFT JOIN orders o ON u.id = o.user_id WHERE u.role = 'customer' GROUP BY u.id ORDER BY u.created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$customers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

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
                    <?php 
                    $sn = $offset + 1;
                    foreach($customers as $customer): 
                    ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td><img src="../<?= htmlspecialchars($customer['profile_pic'] ?? 'assets/images/default-avatar.png') ?>" alt="Profile" style="width:40px;height:40px;border-radius:50%;object-fit:cover;"></td>
                        <td><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></td>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                        <td><?= htmlspecialchars($customer['contact']) ?></td>
                        <td><span class="status-badge status-<?= strtolower($customer['status']) ?>"><?= htmlspecialchars($customer['status']) ?></span></td>
                        <td><?= date('Y-m-d', strtotime($customer['created_at'])) ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($customer['updated_at'])) ?></td>
                        <td><?= $customer['order_count'] ?></td>
                        <td>
                            <div class="customers-actions">
                                <button class="action-btn view" onclick="viewCustomer(<?= $customer['id'] ?>)" title="View Details">
                                    <ion-icon name="eye-outline"></ion-icon>
                                </button>
                                <button class="action-btn edit" onclick="editCustomer(<?= $customer['id'] ?>)" title="Edit Customer">
                                    <ion-icon name="create-outline"></ion-icon>
                                </button>
                                <button class="action-btn delete" onclick="deleteCustomer(<?= $customer['id'] ?>)" title="Delete Customer">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($customers)): ?>
                    <tr>
                        <td colspan="10" style="text-align:center;">No customers found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?p=<?= $page-1 ?>#customers" class="page-btn">Previous</a>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?p=<?= $i ?>#customers" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if($page < $total_pages): ?>
                <a href="?p=<?= $page+1 ?>#customers" class="page-btn">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <button class="add-customer-btn" onclick="openAddCustomerModal()">
            <ion-icon name="add-outline"></ion-icon>
            Add New Customer
        </button>
    </div>

<!-- Add Customer Modal -->
<div id="addModal" class="customers-modal">
        <div class="customers-modal-content">
            <div class="modal-header-custom">
                <h2>Add New Customer</h2>
                <button type="button" class="close" onclick="closeAddModal()">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            <form id="addCustomerForm">
                <div class="customers-form-row">
                    <div class="customers-form-group">
                        <label>First Name</label>
                        <input type="text" id="addFirstName" required>
                    </div>
                    <div class="customers-form-group">
                        <label>Last Name</label>
                        <input type="text" id="addLastName" required>
                    </div>
                </div>
                <div class="customers-form-row">
                    <div class="customers-form-group">
                        <label>Email</label>
                        <input type="email" id="addEmail" required>
                    </div>
                    <div class="customers-form-group">
                        <label>Contact Number</label>
                        <input type="tel" id="addContact" required>
                    </div>
                </div>
                <div class="customers-form-row">
                    <div class="customers-form-group">
                        <label>Password</label>
                        <input type="password" id="addPassword" required minlength="6">
                    </div>
                    <div class="customers-form-group">
                        <label>Status</label>
                        <select id="addStatus">
                            <option value="verified">Verified</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="customers-modal-actions">
                    <button type="submit" class="customers-btn customers-btn-primary">Add Customer</button>
                    <button type="button" class="customers-btn customers-btn-secondary" onclick="closeAddModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

<!-- Edit Customer Modal -->
<div id="editModal" class="customers-modal">
        <div class="customers-modal-content">
            <div class="modal-header-custom">
                <h2>Edit Customer Details</h2>
                <button type="button" class="close" onclick="closeEditModal()">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            <form id="editCustomerForm">
                <input type="hidden" id="editId">
                <div class="customers-form-row">
                    <div class="customers-form-group">
                        <label>First Name</label>
                        <input type="text" id="editFirstName" required>
                    </div>
                    <div class="customers-form-group">
                        <label>Last Name</label>
                        <input type="text" id="editLastName" required>
                    </div>
                </div>
                <div class="customers-form-row">
                    <div class="customers-form-group">
                        <label>Email</label>
                        <input type="email" id="editEmail" required>
                    </div>
                    <div class="customers-form-group">
                        <label>Contact Number</label>
                        <input type="tel" id="editContact" required>
                    </div>
                </div>
                <div class="customers-form-row">
                    <div class="customers-form-group">
                        <label>Address</label>
                        <input type="text" id="editAddress" placeholder="Optional">
                    </div>
                    <div class="customers-form-group">
                        <label>Status</label>
                        <select id="editStatus">
                            <option value="verified">Verified</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="customers-modal-actions">
                    <button type="submit" class="customers-btn customers-btn-primary">Save Changes</button>
                    <button type="button" class="customers-btn customers-btn-secondary" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

<!-- View Customer Details Modal -->
<div id="viewModal" class="customers-modal customers-modal-view">
        <div class="customers-modal-content">
            <div class="view-modal-header">
                <h2>Customer Details</h2>
                <button class="close" onclick="closeViewModal()">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            <div class="view-modal-body">
                <div class="customer-profile-section">
                    <img id="viewProfilePic" src="" alt="Profile" class="view-profile-pic">
                    <div class="customer-info-main">
                        <h3 id="viewFullName"></h3>
                        <p id="viewEmail" class="view-email"></p>
                        <span id="viewStatusBadge" class="status-badge"></span>
                    </div>
                </div>
                <div class="customer-details-grid">
                    <div class="detail-item">
                        <label>Contact Number</label>
                        <p id="viewContact"></p>
                    </div>
                    <div class="detail-item">
                        <label>Member Since</label>
                        <p id="viewCreatedAt"></p>
                    </div>
                    <div class="detail-item">
                        <label>Last Login</label>
                        <p id="viewLastLogin"></p>
                    </div>
                    <div class="detail-item">
                        <label>Total Orders</label>
                        <p id="viewOrderCount" class="order-count"></p>
                    </div>
                    <div class="detail-item full-width">
                        <label>Address</label>
                        <p id="viewAddress"></p>
                    </div>
                </div>
            </div>
            <div class="view-modal-footer">
                <button class="customers-btn customers-btn-primary" onclick="editFromView()">
                    <ion-icon name="create-outline"></ion-icon>
                    Edit Customer
                </button>
                <button class="customers-btn customers-btn-secondary" onclick="closeViewModal()">Close</button>
            </div>
        </div>
    </div>

<script>
// Add Customer Form Submit
document.getElementById('addCustomerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('first_name', document.getElementById('addFirstName').value);
    formData.append('last_name', document.getElementById('addLastName').value);
    formData.append('email', document.getElementById('addEmail').value);
    formData.append('contact', document.getElementById('addContact').value);
    formData.append('password', document.getElementById('addPassword').value);
    formData.append('status', document.getElementById('addStatus').value);
    formData.append('role', 'customer');
    
    fetch('../api/admin-users.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message || 'Customer added successfully!');
            closeAddModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        alert('Error adding customer');
        console.error(err);
    });
});

// Edit Customer Form Submit
document.getElementById('editCustomerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('id', document.getElementById('editId').value);
    formData.append('first_name', document.getElementById('editFirstName').value);
    formData.append('last_name', document.getElementById('editLastName').value);
    formData.append('email', document.getElementById('editEmail').value);
    formData.append('contact', document.getElementById('editContact').value);
    formData.append('address', document.getElementById('editAddress').value);
    formData.append('status', document.getElementById('editStatus').value);
    
    fetch('../api/admin-users.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message || 'Customer updated successfully!');
            closeEditModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});
</script>