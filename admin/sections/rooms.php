<?php
require_once('includes/auth-guard.php');
require_once('../config/db.php');

parse_str(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY) ?? '', $query_params);
$page = isset($query_params['p']) ? (int)$query_params['p'] : 1;
if(isset($_GET['p'])) $page = (int)$_GET['p'];
$limit = 10;
$offset = ($page - 1) * $limit;

$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM rooms");
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$stmt = $conn->prepare("SELECT * FROM rooms ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$rooms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="room-section">
    <div class="room-header">
        <div class="room-title">
            <ion-icon name="bed-outline"></ion-icon>
            <h2>Room Management</h2>
        </div>
        <button class="room-add-btn" onclick="openRoomModal('add')">
            <ion-icon name="add-circle-outline"></ion-icon>
            Add New Room
        </button>
    </div>

    <div class="room-table-container">
        <table class="room-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Image</th>
                    <th>Room No</th>
                    <th>Type</th>
                    <th>Beds</th>
                    <th>Status</th>
                    <th>Price (RS)</th>
                    <th>Today (RS)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sn = $offset + 1;
                foreach($rooms as $room): 
                ?>
                <tr>
                    <td><?= $sn++ ?></td>
                    <td>
                        <div class="room-image-wrapper">
                            <img src="../<?= htmlspecialchars($room['image_path'] ?? 'assets/images/default-room.jpg') ?>" alt="Room <?= $room['room_no'] ?>" class="room-image">
                        </div>
                    </td>
                    <td><strong><?= htmlspecialchars($room['room_no']) ?></strong></td>
                    <td><span class="room-type-badge room-type-<?= strtolower($room['room_type']) ?>"><?= ucfirst(htmlspecialchars($room['room_type'])) ?></span></td>
                    <td><?= htmlspecialchars($room['total_beds']) ?> × <?= ucfirst(htmlspecialchars($room['bed_size'])) ?></td>
                    <td><span class="room-status room-status-<?= strtolower($room['status']) ?>"><?= ucfirst(htmlspecialchars($room['status'])) ?></span></td>
                    <td><?= number_format($room['price'], 0) ?></td>
                    <td><?= $room['price_today'] ? '<strong>' . number_format($room['price_today'], 0) . '</strong>' : number_format($room['price'], 0) ?></td>
                    <td class="room-actions">
                        <button class="action-btn view-btn" onclick="viewRoomDetails(<?= $room['id'] ?>)" title="View Details">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                        <button class="action-btn edit-btn" onclick="openRoomModal('edit', <?= $room['id'] ?>)" title="Edit Room">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteRoom(<?= $room['id'] ?>)" title="Delete Room">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($rooms)): ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding: 2rem;">No rooms found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($total_pages > 1): ?>
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?p=<?= $page-1 ?>#rooms" class="page-btn">Previous</a>
        <?php endif; ?>
        
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?p=<?= $i ?>#rooms" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <?php if($page < $total_pages): ?>
            <a href="?p=<?= $page+1 ?>#rooms" class="page-btn">Next</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal for Add/Edit Room -->
<div class="room-modal" id="roomModal">
    <div class="room-modal-content">
        <button class="room-modal-close">
            <ion-icon name="close-outline"></ion-icon>
        </button>
        <h3 id="modalTitle">Add New Room</h3>
        <form id="roomForm">
            <input type="hidden" id="roomEditId">
            <div class="room-form-group">
                <label for="roomImage">Room Image</label>
                <input type="file" id="roomImage" class="room-form-control" accept="image/*">
            </div>
            <div class="room-form-group">
                <label for="roomNumber">Room Number</label>
                <input type="text" id="roomNumber" class="room-form-control" required>
            </div>
            <div class="room-form-group">
                <label for="roomType">Room Type</label>
                <select id="roomType" class="room-form-control" required>
                    <option value="single">Single</option>
                    <option value="double">Double</option>
                    <option value="deluxe">Deluxe</option>
                    <option value="suite">Suite</option>
                </select>
            </div>
            <div class="room-form-group">
                <label for="totalBeds">Total Beds</label>
                <input type="number" id="totalBeds" class="room-form-control" required>
            </div>
            <div class="room-form-group">
                <label for="bedSize">Bed Size</label>
                <select id="bedSize" class="room-form-control" required>
                    <option value="single">Single</option>
                    <option value="double">Double</option>
                    <option value="queen">Queen</option>
                    <option value="king">King</option>
                </select>
            </div>
            <div class="room-form-group">
                <label for="roomPrice">Standard Price (RS)</label>
                <input type="number" id="roomPrice" class="room-form-control" required step="0.01" min="0" placeholder="Enter price in RS">
            </div>
            <div class="room-form-group">
                <label for="roomStatus">Status</label>
                <select id="roomStatus" class="room-form-control" required>
                    <option value="available">Available</option>
                    <option value="booked">Booked</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="occupied">Occupied</option>
                </select>
            </div>
            <div class="room-form-group">
                <label for="todayPrice">Today's Price (RS)</label>
                <input type="number" id="todayPrice" class="room-form-control" required step="0.01" min="0" placeholder="Enter today's price in RS">
            </div>
            <div class="room-form-group">
                <label for="amenities">Amenities</label>
                <input type="text" id="amenities" class="room-form-control" placeholder="WiFi, AC, TV, etc.">
            </div>
            <div class="room-form-group">
                <label for="shortDescription">Description</label>
                <textarea id="shortDescription" class="room-form-control" rows="3" placeholder="Brief description of the room..."></textarea>
            </div>
            <div class="room-form-actions">
                <button type="submit" class="room-btn room-btn-primary">Save Room</button>
                <button type="button" class="room-btn room-btn-secondary" onclick="closeRoomModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View Room Details -->
<div class="room-modal" id="roomViewModal">
    <div class="room-modal-content room-view-modal-content">
        <button class="room-modal-close" onclick="closeRoomViewModal()">
            <ion-icon name="close-outline"></ion-icon>
        </button>
        <h3 class="modal-title-view">
            <ion-icon name="information-circle-outline"></ion-icon>
            Room Details
        </h3>
        
        <div class="room-view-layout">
            <div class="room-view-image-wrapper">
                <img id="viewRoomImage" src="" alt="Room Image">
            </div>
            
            <div class="room-info-grid">
                    <div class="room-info-item">
                        <div class="info-label">
                            <ion-icon name="keypad-outline"></ion-icon>
                            Room Number
                        </div>
                        <div class="info-value" id="viewRoomNumber"></div>
                    </div>
                    
                    <div class="room-info-item">
                        <div class="info-label">
                            <ion-icon name="albums-outline"></ion-icon>
                            Room Type
                        </div>
                        <div class="info-value" id="viewRoomType"></div>
                    </div>
                    
                    <div class="room-info-item">
                        <div class="info-label">
                            <ion-icon name="bed-outline"></ion-icon>
                            Total Beds
                        </div>
                        <div class="info-value" id="viewTotalBeds"></div>
                    </div>
                    
                    <div class="room-info-item">
                        <div class="info-label">
                            <ion-icon name="resize-outline"></ion-icon>
                            Bed Size
                        </div>
                        <div class="info-value" id="viewBedSize"></div>
                    </div>
                    
                    <div class="room-info-item">
                        <div class="info-label">
                            <ion-icon name="checkmark-circle-outline"></ion-icon>
                            Status
                        </div>
                        <div class="info-value" id="viewStatus"></div>
                    </div>
                    
                    <div class="room-info-item">
                        <div class="info-label">
                            <ion-icon name="cash-outline"></ion-icon>
                            Standard Price
                        </div>
                        <div class="info-value" id="viewPrice"></div>
                    </div>
                    
                    <div class="room-info-item">
                        <div class="info-label">
                            <ion-icon name="pricetag-outline"></ion-icon>
                            Today's Price
                        </div>
                        <div class="info-value" id="viewTodayPrice"></div>
                    </div>
                    
                    <div class="room-info-item">
                        <div class="info-label">
                            <ion-icon name="star-outline"></ion-icon>
                            Amenities
                        </div>
                        <div class="info-value" id="viewAmenities"></div>
                    </div>
                </div>
                
                <div class="room-info-description">
                    <div class="info-label">
                        <ion-icon name="document-text-outline"></ion-icon>
                        Description
                    </div>
                    <div class="info-value" id="viewDescription"></div>
                </div>
                
                <div class="room-info-timestamps">
                    <div class="timestamp-item">
                        <ion-icon name="calendar-outline"></ion-icon>
                        <span>Created: <strong id="viewCreatedAt"></strong></span>
                    </div>
                    <div class="timestamp-item">
                        <ion-icon name="time-outline"></ion-icon>
                        <span>Updated: <strong id="viewUpdatedAt"></strong></span>
                    </div>
                </div>
        </div>
    </div>
</div>