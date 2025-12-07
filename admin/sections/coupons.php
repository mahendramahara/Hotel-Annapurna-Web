<?php
require_once('includes/auth-guard.php');
require_once('../config/db.php');

$stmt = $conn->prepare("SELECT * FROM coupons ORDER BY created_at DESC");
$stmt->execute();
$coupons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="coupon-section">
    <div class="coupon-header">
        <div class="coupon-title">
            <ion-icon name="pricetag-outline"></ion-icon>
            <h2>Coupons Management</h2>
        </div>
        <button class="coupon-add-btn" onclick="openCouponModal('add')">
            <ion-icon name="add-circle-outline"></ion-icon>
            Add New Coupon
        </button>
    </div>

    <div class="coupon-table-container">
        <table class="coupon-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Discount</th>
                    <th>Min Purchase</th>
                    <th>Max Discount</th>
                    <th>Usage</th>
                    <th>Valid From</th>
                    <th>Valid Until</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sn = 1;
                foreach($coupons as $coupon): 
                    $now = new DateTime();
                    $valid_from = new DateTime($coupon['valid_from']);
                    $valid_until = new DateTime($coupon['valid_until']);
                    
                    $is_expired = $now > $valid_until;
                    $is_scheduled = $now < $valid_from;
                    $display_status = $is_expired ? 'expired' : ($is_scheduled ? 'scheduled' : $coupon['status']);
                ?>
                <tr>
                    <td><?= $sn++ ?></td>
                    <td><strong class="coupon-code"><?= htmlspecialchars($coupon['code']) ?></strong></td>
                    <td><span class="coupon-type-badge coupon-type-<?= strtolower($coupon['discount_type']) ?>"><?= ucfirst(htmlspecialchars($coupon['discount_type'])) ?></span></td>
                    <td>
                        <?php if($coupon['discount_type'] === 'percentage'): ?>
                            <?= number_format($coupon['discount_value'], 0) ?>%
                        <?php else: ?>
                            Rs. <?= number_format($coupon['discount_value'], 0) ?>
                        <?php endif; ?>
                    </td>
                    <td>Rs. <?= number_format($coupon['min_purchase'], 0) ?></td>
                    <td>
                        <?php if($coupon['max_discount']): ?>
                            Rs. <?= number_format($coupon['max_discount'], 0) ?>
                        <?php else: ?>
                            <span style="color: #999;">No Limit</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($coupon['usage_limit']): ?>
                            <?= $coupon['used_count'] ?> / <?= $coupon['usage_limit'] ?>
                        <?php else: ?>
                            <?= $coupon['used_count'] ?> / <span style="color: #999;">∞</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('Y-m-d H:i', strtotime($coupon['valid_from'])) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($coupon['valid_until'])) ?></td>
                    <td><span class="coupon-status coupon-status-<?= $display_status ?>"><?= ucfirst($display_status) ?></span></td>
                    <td class="coupon-actions">
                        <button class="action-btn view-btn" onclick="viewCouponDetails(<?= $coupon['id'] ?>)" title="View Details">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                        <button class="action-btn edit-btn" onclick="openCouponModal('edit', <?= $coupon['id'] ?>)" title="Edit Coupon">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <?php if($coupon['status'] === 'active'): ?>
                        <button class="action-btn disable-btn" onclick="toggleCouponStatus(<?= $coupon['id'] ?>, 'inactive')" title="Deactivate">
                            <ion-icon name="pause-circle-outline"></ion-icon>
                        </button>
                        <?php else: ?>
                        <button class="action-btn enable-btn" onclick="toggleCouponStatus(<?= $coupon['id'] ?>, 'active')" title="Activate">
                            <ion-icon name="play-circle-outline"></ion-icon>
                        </button>
                        <?php endif; ?>
                        <button class="action-btn delete-btn" onclick="deleteCoupon(<?= $coupon['id'] ?>)" title="Delete Coupon">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($coupons)): ?>
                <tr>
                    <td colspan="11" style="text-align:center; padding: 2rem;">No coupons found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Add/Edit Coupon -->
<div class="coupon-modal" id="couponModal">
    <div class="coupon-modal-content">
        <button class="coupon-modal-close">
            <ion-icon name="close-outline"></ion-icon>
        </button>
        <h3 id="modalTitle">Add New Coupon</h3>
        <form id="couponForm">
            <input type="hidden" id="couponEditId">
            
            <div class="coupon-form-row">
                <div class="coupon-form-group">
                    <label for="couponCode">Coupon Code <span class="required">*</span></label>
                    <input type="text" id="couponCode" class="coupon-form-control" required maxlength="50" placeholder="e.g., WELCOME10" style="text-transform: uppercase;">
                </div>
                
                <div class="coupon-form-group">
                    <label for="discountType">Discount Type <span class="required">*</span></label>
                    <select id="discountType" class="coupon-form-control" required onchange="updateDiscountLabel()">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (Rs.)</option>
                    </select>
                </div>
            </div>

            <div class="coupon-form-row">
                <div class="coupon-form-group">
                    <label for="discountValue"><span id="discountLabel">Discount Percentage</span> <span class="required">*</span></label>
                    <input type="number" id="discountValue" class="coupon-form-control" required step="0.01" min="0" placeholder="Enter discount value">
                </div>
                
                <div class="coupon-form-group">
                    <label for="minPurchase">Minimum Purchase (Rs.)</label>
                    <input type="number" id="minPurchase" class="coupon-form-control" step="0.01" min="0" value="0" placeholder="0">
                </div>
            </div>

            <div class="coupon-form-row">
                <div class="coupon-form-group">
                    <label for="maxDiscount">Max Discount (Rs.)</label>
                    <input type="number" id="maxDiscount" class="coupon-form-control" step="0.01" min="0" placeholder="Leave empty for no limit">
                </div>
                
                <div class="coupon-form-group">
                    <label for="usageLimit">Usage Limit</label>
                    <input type="number" id="usageLimit" class="coupon-form-control" min="1" placeholder="Leave empty for unlimited">
                </div>
            </div>

            <div class="coupon-form-row">
                <div class="coupon-form-group">
                    <label for="validFrom">Valid From <span class="required">*</span></label>
                    <input type="datetime-local" id="validFrom" class="coupon-form-control" required>
                </div>
                
                <div class="coupon-form-group">
                    <label for="validUntil">Valid Until <span class="required">*</span></label>
                    <input type="datetime-local" id="validUntil" class="coupon-form-control" required>
                </div>
            </div>

            <div class="coupon-form-group">
                <label for="couponStatus">Status <span class="required">*</span></label>
                <select id="couponStatus" class="coupon-form-control" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="coupon-form-actions">
                <button type="submit" class="coupon-btn coupon-btn-primary">Save Coupon</button>
                <button type="button" class="coupon-btn coupon-btn-secondary" onclick="closeCouponModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View Coupon Details -->
<div class="coupon-modal" id="couponViewModal">
    <div class="coupon-modal-content">
        <button class="coupon-modal-close" onclick="closeCouponViewModal()">
            <ion-icon name="close-outline"></ion-icon>
        </button>
        <h3>Coupon Details</h3>
        <div id="couponViewContent" class="coupon-view-details">
            <!-- Dynamic content will be loaded here -->
        </div>
    </div>
</div>

<script>
function openCouponModal(mode, couponId = null) {
    const modal = document.getElementById('couponModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('couponForm');
    
    form.reset();
    document.getElementById('couponEditId').value = '';
    
    if (mode === 'add') {
        modalTitle.textContent = 'Add New Coupon';
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('validFrom').value = now.toISOString().slice(0, 16);
        
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 30);
        document.getElementById('validUntil').value = tomorrow.toISOString().slice(0, 16);
    } else if (mode === 'edit' && couponId) {
        modalTitle.textContent = 'Edit Coupon';
        fetch(`../api/admin-coupons.php?action=get&id=${couponId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const coupon = data.coupon;
                    document.getElementById('couponEditId').value = coupon.id;
                    document.getElementById('couponCode').value = coupon.code;
                    document.getElementById('discountType').value = coupon.discount_type;
                    document.getElementById('discountValue').value = coupon.discount_value;
                    document.getElementById('minPurchase').value = coupon.min_purchase;
                    document.getElementById('maxDiscount').value = coupon.max_discount || '';
                    document.getElementById('usageLimit').value = coupon.usage_limit || '';
                    document.getElementById('validFrom').value = coupon.valid_from.replace(' ', 'T').slice(0, 16);
                    document.getElementById('validUntil').value = coupon.valid_until.replace(' ', 'T').slice(0, 16);
                    document.getElementById('couponStatus').value = coupon.status;
                    updateDiscountLabel();
                }
            })
            .catch(error => console.error('Error loading coupon:', error));
    }
    
    modal.style.display = 'flex';
}

function closeCouponModal() {
    document.getElementById('couponModal').style.display = 'none';
}

function closeCouponViewModal() {
    document.getElementById('couponViewModal').style.display = 'none';
}

function updateDiscountLabel() {
    const type = document.getElementById('discountType').value;
    const label = document.getElementById('discountLabel');
    label.textContent = type === 'percentage' ? 'Discount Percentage (%)' : 'Discount Amount (Rs.)';
}

document.getElementById('couponForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('couponEditId').value;
    const formData = new FormData();
    
    formData.append('action', id ? 'update' : 'create');
    if (id) formData.append('id', id);
    formData.append('code', document.getElementById('couponCode').value.toUpperCase());
    formData.append('discount_type', document.getElementById('discountType').value);
    formData.append('discount_value', document.getElementById('discountValue').value);
    formData.append('min_purchase', document.getElementById('minPurchase').value);
    formData.append('max_discount', document.getElementById('maxDiscount').value);
    formData.append('usage_limit', document.getElementById('usageLimit').value);
    formData.append('valid_from', document.getElementById('validFrom').value.replace('T', ' ') + ':00');
    formData.append('valid_until', document.getElementById('validUntil').value.replace('T', ' ') + ':00');
    formData.append('status', document.getElementById('couponStatus').value);
    
    fetch('../api/admin-coupons.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeCouponModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the coupon');
    });
});

function viewCouponDetails(couponId) {
    fetch(`../api/admin-coupons.php?action=get&id=${couponId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const coupon = data.coupon;
                const discountDisplay = coupon.discount_type === 'percentage' 
                    ? `${coupon.discount_value}%` 
                    : `Rs. ${parseFloat(coupon.discount_value).toLocaleString()}`;
                
                const content = `
                    <div class="detail-row">
                        <span class="detail-label">Coupon Code:</span>
                        <span class="detail-value"><strong>${coupon.code}</strong></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Discount Type:</span>
                        <span class="detail-value">${coupon.discount_type.charAt(0).toUpperCase() + coupon.discount_type.slice(1)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Discount Value:</span>
                        <span class="detail-value">${discountDisplay}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Minimum Purchase:</span>
                        <span class="detail-value">Rs. ${parseFloat(coupon.min_purchase).toLocaleString()}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Maximum Discount:</span>
                        <span class="detail-value">${coupon.max_discount ? 'Rs. ' + parseFloat(coupon.max_discount).toLocaleString() : 'No Limit'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Usage:</span>
                        <span class="detail-value">${coupon.used_count} / ${coupon.usage_limit || '∞'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Valid From:</span>
                        <span class="detail-value">${new Date(coupon.valid_from).toLocaleString()}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Valid Until:</span>
                        <span class="detail-value">${new Date(coupon.valid_until).toLocaleString()}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value"><span class="coupon-status coupon-status-${coupon.status}">${coupon.status.charAt(0).toUpperCase() + coupon.status.slice(1)}</span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Created At:</span>
                        <span class="detail-value">${new Date(coupon.created_at).toLocaleString()}</span>
                    </div>
                `;
                
                document.getElementById('couponViewContent').innerHTML = content;
                document.getElementById('couponViewModal').style.display = 'flex';
            }
        })
        .catch(error => console.error('Error loading coupon details:', error));
}

function toggleCouponStatus(couponId, newStatus) {
    if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this coupon?`)) {
        const formData = new FormData();
        formData.append('action', 'toggleStatus');
        formData.append('id', couponId);
        formData.append('status', newStatus);
        
        fetch('../api/admin-coupons.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the coupon status');
        });
    }
}

function deleteCoupon(couponId) {
    if (confirm('Are you sure you want to delete this coupon? This action cannot be undone.')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', couponId);
        
        fetch('../api/admin-coupons.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the coupon');
        });
    }
}

document.querySelectorAll('.coupon-modal-close').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('.coupon-modal').style.display = 'none';
    });
});

window.addEventListener('click', function(e) {
    const modals = document.querySelectorAll('.coupon-modal');
    modals.forEach(modal => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>