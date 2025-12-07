<?php
require_once('includes/auth-guard.php');
require_once('../config/db.php');

parse_str(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY) ?? '', $query_params);
$page = isset($query_params['p']) ? (int)$query_params['p'] : 1;
if(isset($_GET['p'])) $page = (int)$_GET['p'];
$limit = 10;
$offset = ($page - 1) * $limit;

$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'staff'");
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'staff' ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$staffs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="staff-container">
    <div class="staff-header">
        <div class="staff-title">
            <ion-icon name="people-outline"></ion-icon>
            <h2>Staff Management</h2>
        </div>
        <button class="staff-add-button" onclick="openStaffModal()">
            <ion-icon name="add-outline"></ion-icon>
            Add New Staff
        </button>
    </div>

    <div class="staff-table-container">
        <table class="staff-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Profile</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Salary</th>
                    <th>Address</th>
                    <th>Joined Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="staffTableBody">
                <?php 
                $sn = $offset + 1;
                foreach($staffs as $staff): 
                ?>
                <tr>
                    <td><?= $sn++ ?></td>
                    <td><img src="../<?= htmlspecialchars($staff['profile_pic'] ?? 'assets/images/default-avatar.png') ?>" alt="Staff" class="staff-profile-img" style="width:40px;height:40px;border-radius:50%;object-fit:cover;"></td>
                    <td><?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?></td>
                    <td><?= htmlspecialchars($staff['email']) ?></td>
                    <td><?= htmlspecialchars($staff['contact']) ?></td>
                    <td>RS <?= number_format($staff['salary'] ?? 0, 2) ?></td>
                    <td><?= htmlspecialchars($staff['address'] ?? 'N/A') ?></td>
                    <td><?= date('Y-m-d', strtotime($staff['created_at'])) ?></td>
                    <td><span class="staff-status <?= strtolower($staff['status']) ?>"><?= htmlspecialchars($staff['status']) ?></span></td>
                    <td class="staff-actions">
                        <button class="staff-action-btn view" onclick="viewStaffDetails(<?= $staff['id'] ?>)" title="View Details">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                        <button class="staff-action-btn edit" onclick="editStaff(<?= $staff['id'] ?>)" title="Edit Staff">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button class="staff-action-btn delete" onclick="deleteStaff(<?= $staff['id'] ?>)" title="Delete Staff">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($staffs)): ?>
                <tr>
                    <td colspan="10" style="text-align:center;">No staff members found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?p=<?= $page-1 ?>#staffs" class="page-btn">Previous</a>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?p=<?= $i ?>#staffs" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if($page < $total_pages): ?>
                <a href="?p=<?= $page+1 ?>#staffs" class="page-btn">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="staffModal" class="staff-modal">
    <div class="staff-modal-content">
        <span class="staff-close-btn" onclick="closeStaffModal()">&times;</span>
        <h2 id="staffModalTitle">Add New Staff</h2>

        <form id="staffForm" enctype="multipart/form-data">
            <input type="hidden" id="staffId" name="id">
            <div class="staff-form-group">
                <label for="firstName">First Name*</label>
                <input type="text" id="firstName" name="first_name" required>
            </div>

            <div class="staff-form-group">
                <label for="lastName">Last Name*</label>
                <input type="text" id="lastName" name="last_name" required>
            </div>

            <div class="staff-form-row">
                <div class="staff-form-group">
                    <label for="staffEmail">Email*</label>
                    <input type="email" id="staffEmail" name="email" required>
                </div>

                <div class="staff-form-group">
                    <label for="staffContact">Contact*</label>
                    <input type="text" id="staffContact" name="contact" required>
                </div>
            </div>
            
            <div class="staff-form-group">
                <label for="staffAddress">Address</label>
                <textarea id="staffAddress" name="address" rows="2"></textarea>
            </div>
            
            <div class="staff-form-group" id="passwordGroup">
                <label for="staffPassword">Password*</label>
                <input type="password" id="staffPassword" name="password">
            </div>

            <div class="staff-form-row">
                <div class="staff-form-group">
                    <label for="staffStatus">Status*</label>
                    <select id="staffStatus" name="status" required>
                        <option value="verified">Verified</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                
                <div class="staff-form-group">
                    <label for="staffSalary">Salary (RS)</label>
                    <input type="number" id="staffSalary" name="salary" step="0.01" min="0" placeholder="Enter salary in RS">
                </div>
            </div>

            <div class="staff-form-group">
                <label for="profilePic">Profile Image</label>
                <input type="file" id="profilePic" name="profile_pic" accept="image/*" onchange="previewProfilePic(this)">
                <div id="profilePicPreview" style="margin-top:10px;display:none;">
                    <img id="profilePicImg" src="" alt="Preview" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:2px solid #ddd;">
                </div>
            </div>

            <div class="staff-form-actions">
                <button type="submit" class="staff-btn staff-btn-primary">Save Staff</button>
                <button type="button" class="staff-btn staff-btn-secondary" onclick="closeStaffModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Staff View Details Modal -->
<div id="staffViewModal" class="staff-modal">
    <div class="staff-modal-content" style="max-width: 600px;">
        <span class="staff-close-btn" onclick="closeStaffViewModal()">&times;</span>
        <h2>Staff Details</h2>
        
        <div id="staffViewContent" style="padding: 20px 0;">
            <div style="text-align: center; margin-bottom: 20px;">
                <img id="viewStaffProfilePic" src="" alt="Staff Profile" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #ddd;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <strong style="color: #555;">First Name:</strong>
                    <p id="viewFirstName" style="margin: 5px 0; color: #333;"></p>
                </div>
                <div>
                    <strong style="color: #555;">Last Name:</strong>
                    <p id="viewLastName" style="margin: 5px 0; color: #333;"></p>
                </div>
                <div>
                    <strong style="color: #555;">Email:</strong>
                    <p id="viewEmail" style="margin: 5px 0; color: #333;"></p>
                </div>
                <div>
                    <strong style="color: #555;">Contact:</strong>
                    <p id="viewContact" style="margin: 5px 0; color: #333;"></p>
                </div>
                <div>
                    <strong style="color: #555;">Status:</strong>
                    <p id="viewStatus" style="margin: 5px 0;"></p>
                </div>
                <div>
                    <strong style="color: #555;">Salary:</strong>
                    <p id="viewSalary" style="margin: 5px 0; color: #333;"></p>
                </div>
                <div>
                    <strong style="color: #555;">Role:</strong>
                    <p id="viewRole" style="margin: 5px 0; color: #333;"></p>
                </div>
                <div>
                    <strong style="color: #555;">Joined Date:</strong>
                    <p id="viewJoinedDate" style="margin: 5px 0; color: #333;"></p>
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong style="color: #555;">Address:</strong>
                <p id="viewAddress" style="margin: 5px 0; color: #333; line-height: 1.6;"></p>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 25px;">
                <button class="staff-btn staff-btn-primary" onclick="editFromView()" style="padding: 10px 20px;">
                    <ion-icon name="create-outline" style="vertical-align: middle;"></ion-icon>
                    Edit Staff
                </button>
                <button class="staff-btn staff-btn-secondary" onclick="closeStaffViewModal()" style="padding: 10px 20px;">Close</button>
            </div>
        </div>
    </div>
</div>


