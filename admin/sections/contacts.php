<?php
require_once('includes/auth-guard.php');
require_once('../config/db.php');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM contact_requests");
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$stmt = $conn->prepare("SELECT * FROM contact_requests ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$contacts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="contacts-data-table">
            <div class="title">
                <ion-icon name="mail-unread-outline" class="title-icon"></ion-icon>
                <span class="text">Customer Contact Requests</span>
            </div>
            
            <div class="contacts-container">
                <table class="contacts-table">
                    <thead>
                        <tr>
                            <th>SN (#)</th>
                            <th>Name</th>
                            <th>Email ID</th>
                            <th>Subject</th>
                            <th>Sent Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="contacts-table-body">
                        <?php 
                        $sn = $offset + 1;
                        foreach($contacts as $contact): 
                        ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                            <td><?= htmlspecialchars($contact['name']) ?></td>
                            <td><?= htmlspecialchars($contact['email']) ?></td>
                            <td><?= htmlspecialchars($contact['subject'] ?? 'N/A') ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($contact['created_at'])) ?></td>
                            <td><span class="status-badge status-<?= strtolower($contact['status']) ?>"><?= htmlspecialchars($contact['status']) ?></span></td>
                            <td>
                                <div class="action-buttons-wrapper">
                                    <button class="action-btn view" onclick="viewContact(<?= $contact['id'] ?>)" title="View Details">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </button>
                                    <button class="action-btn reply" onclick="replyContact(<?= $contact['id'] ?>)" title="Reply">
                                        <ion-icon name="mail-outline"></ion-icon>
                                    </button>
                                    <button class="action-btn edit" onclick="editContactStatus(<?= $contact['id'] ?>)" title="Change Status">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </button>
                                    <button class="action-btn delete" onclick="deleteContact(<?= $contact['id'] ?>)" title="Delete">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($contacts)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">No contact requests found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if($total_pages > 1): ?>
                <div class="pagination">
                    <?php if($page > 1): ?>
                        <a href="?page=contacts&p=<?= $page-1 ?>" class="page-btn">Previous</a>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=contacts&p=<?= $i ?>" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                        <a href="?page=contacts&p=<?= $page+1 ?>" class="page-btn">Next</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="contacts-view-modal" class="contacts-modal">
            <div class="contacts-modal-content">
                <span class="contacts-close-btn" onclick="closeContactModal('contacts-view-modal')">&times;</span>
                <h2><ion-icon name="document-text-outline"></ion-icon> Contact Request Details</h2>
                <div id="contacts-view-details">
                    <div class="detail-row">
                        <strong><ion-icon name="person-outline"></ion-icon> Name:</strong>
                        <span id="view-name"></span>
                    </div>
                    <div class="detail-row">
                        <strong><ion-icon name="mail-outline"></ion-icon> Email:</strong>
                        <span id="view-email"></span>
                    </div>
                    <div class="detail-row">
                        <strong><ion-icon name="pricetag-outline"></ion-icon> Subject:</strong>
                        <span id="view-subject"></span>
                    </div>
                    <div class="detail-row full-width">
                        <strong><ion-icon name="chatbox-ellipses-outline"></ion-icon> Message:</strong>
                        <div id="view-message" class="message-content"></div>
                    </div>
                    <div class="detail-row">
                        <strong><ion-icon name="time-outline"></ion-icon> Sent Date:</strong>
                        <span id="view-date"></span>
                    </div>
                    <div class="detail-row">
                        <strong><ion-icon name="flag-outline"></ion-icon> Status:</strong>
                        <span id="view-status" class="status-badge"></span>
                    </div>
                </div>
            </div>
        </div>

        <div id="contacts-edit-modal" class="contacts-modal">
            <div class="contacts-modal-content">
                <span class="contacts-close-btn" onclick="closeContactModal('contacts-edit-modal')">&times;</span>
                <h2>Update Request Status</h2>
                <form id="contacts-edit-form">
                    <input type="hidden" id="edit-request-id">
                    <div class="form-group">
                        <label for="edit-status">Status:</label>
                        <select id="edit-status" required>
                            <option value="pending">Pending</option>
                            <option value="in-progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Save</button>
                        <button type="button" class="btn-cancel" onclick="closeContactModal('contacts-edit-modal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="contacts-respond-modal" class="contacts-modal">
            <div class="contacts-modal-content">
                <span class="contacts-close-btn" onclick="closeContactModal('contacts-respond-modal')">&times;</span>
                <h2><ion-icon name="mail-outline"></ion-icon> Reply to Contact Request</h2>
                <form id="contacts-respond-form">
                    <input type="hidden" id="respond-request-id">
                    <div class="reply-info-box">
                        <div class="info-item">
                            <ion-icon name="person-outline"></ion-icon>
                            <strong>To:</strong> <span id="respond-name"></span>
                        </div>
                        <div class="info-item">
                            <ion-icon name="mail-outline"></ion-icon>
                            <strong>Email:</strong> <span id="respond-email-display"></span>
                        </div>
                        <div class="info-item">
                            <ion-icon name="chatbox-outline"></ion-icon>
                            <strong>Their Message:</strong>
                            <div id="respond-original-message" class="original-message"></div>
                        </div>
                    </div>
                    <input type="hidden" id="respond-email">
                    <div class="form-group">
                        <label for="respond-subject"><ion-icon name="pricetag-outline"></ion-icon> Subject:</label>
                        <input type="text" id="respond-subject" required placeholder="Enter reply subject">
                    </div>
                    <div class="form-group">
                        <label for="respond-message"><ion-icon name="create-outline"></ion-icon> Your Response:</label>
                        <textarea id="respond-message" required placeholder="Type your response here..." rows="8"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-send">
                            <ion-icon name="send-outline"></ion-icon> Send Email Reply
                        </button>
                        <button type="button" class="btn-cancel" onclick="closeContactModal('contacts-respond-modal')">
                            <ion-icon name="close-outline"></ion-icon> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

<script>
function viewContact(id) {
    fetch(`../api/admin-contacts.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('view-name').textContent = data.contact.name;
                document.getElementById('view-email').textContent = data.contact.email;
                document.getElementById('view-subject').textContent = data.contact.subject || 'N/A';
                document.getElementById('view-message').textContent = data.contact.message;
                document.getElementById('view-date').textContent = new Date(data.contact.created_at).toLocaleString();
                
                const statusBadge = document.getElementById('view-status');
                statusBadge.textContent = data.contact.status;
                statusBadge.className = 'status-badge status-' + data.contact.status.toLowerCase();
                
                document.getElementById('contacts-view-modal').style.display = 'block';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to fetch contact details');
        });
}

function replyContact(id) {
    fetch(`../api/admin-contacts.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('respond-request-id').value = data.contact.id;
                document.getElementById('respond-name').textContent = data.contact.name;
                document.getElementById('respond-email').value = data.contact.email;
                document.getElementById('respond-email-display').textContent = data.contact.email;
                document.getElementById('respond-original-message').textContent = data.contact.message;
                document.getElementById('respond-subject').value = 'Re: ' + (data.contact.subject || 'Your Contact Request');
                document.getElementById('respond-message').value = '';
                document.getElementById('contacts-respond-modal').style.display = 'block';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to fetch contact details');
        });
}

function editContactStatus(id) {
    fetch(`../api/admin-contacts.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('edit-request-id').value = data.contact.id;
                document.getElementById('edit-status').value = data.contact.status;
                document.getElementById('contacts-edit-modal').style.display = 'block';
            }
        });
}

function closeContactModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

document.getElementById('contacts-edit-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('id', document.getElementById('edit-request-id').value);
    formData.append('status', document.getElementById('edit-status').value);
    
    fetch('../api/admin-contacts.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            closeContactModal('contacts-edit-modal');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update status');
    });
});

document.getElementById('contacts-respond-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('.btn-send');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<ion-icon name="hourglass-outline"></ion-icon> Sending...';
    
    const formData = new FormData();
    formData.append('action', 'send_reply');
    formData.append('id', document.getElementById('respond-request-id').value);
    formData.append('email', document.getElementById('respond-email').value);
    formData.append('subject', document.getElementById('respond-subject').value);
    formData.append('message', document.getElementById('respond-message').value);
    
    fetch('../api/admin-contacts.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            closeContactModal('contacts-respond-modal');
            location.reload();
        } else {
            alert('Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send email reply');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

function deleteContact(id) {
    if(!confirm('Are you sure you want to delete this contact request?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('../api/admin-contacts.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

window.onclick = function(event) {
    if (event.target.classList.contains('contacts-modal')) {
        event.target.style.display = 'none';
    }
}
</script>