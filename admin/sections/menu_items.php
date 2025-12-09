<?php
require_once('includes/auth-guard.php');
require_once('../config/db.php');

$veg_stmt = $conn->prepare("SELECT * FROM food_items WHERE category = 'veg' ORDER BY created_at DESC");
$veg_stmt->execute();
$veg_items = $veg_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$nonveg_stmt = $conn->prepare("SELECT * FROM food_items WHERE category = 'non-veg' ORDER BY created_at DESC");
$nonveg_stmt->execute();
$nonveg_items = $nonveg_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$special_stmt = $conn->prepare("SELECT * FROM food_items WHERE category = 'special' ORDER BY created_at DESC");
$special_stmt->execute();
$special_items = $special_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="menu-dashboard">
    <!-- Vegetarian Menu Section -->
    <section class="menu-section" id="vegetarian-menu">
        <div class="menu-section-title">
            <ion-icon name="leaf-outline"></ion-icon>
            <h2>Vegetarian Menu</h2>
        </div>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Discount Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sn = 1;
                foreach($veg_items as $item): 
                ?>
                <tr>
                    <td><?= $sn++ ?></td>
                    <td><div class="menu-image-wrapper"><img src="..\<?= htmlspecialchars($item['image_path'] ?? 'assets/images/default-food.jpg') ?>" alt="<?= htmlspecialchars($item['food_name']) ?>"></div></td>
                    <td><?= htmlspecialchars($item['food_name']) ?></td>
                    <td>RS <?= number_format($item['price'], 2) ?></td>
                    <td>RS <?= number_format($item['discount_price'] ?? $item['price'], 2) ?></td>
                    <td>
                        <div class="menu-action-icons">
                            <button class="action-btn edit-btn" onclick="editMenuItem(<?= $item['id'] ?>)" title="Edit"><ion-icon name="create-outline"></ion-icon></button>
                            <button class="action-btn delete-btn" onclick="deleteMenuItem(<?= $item['id'] ?>)" title="Delete"><ion-icon name="trash-outline"></ion-icon></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($veg_items)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;">No vegetarian items found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button class="menu-add-button" onclick="openAddModal('vegetarian')">
            <i class="fas fa-plus"></i> Add New Vegetarian Item
        </button>
    </section>

    <!-- Non-Vegetarian Menu Section -->
    <section class="menu-section" id="non-vegetarian-menu">
        <div class="menu-section-title">
            <ion-icon name="restaurant-outline"></ion-icon>
            <h2>Non-Vegetarian Menu</h2>
        </div>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Discount Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sn = 1;
                foreach($nonveg_items as $item): 
                ?>
                <tr>
                    <td><?= $sn++ ?></td>
                    <td><div class="menu-image-wrapper"><img src="..\<?= htmlspecialchars($item['image_path'] ?? 'assets/images/default-food.jpg') ?>" alt="<?= htmlspecialchars($item['food_name']) ?>"></div></td>
                    <td><?= htmlspecialchars($item['food_name']) ?></td>
                    <td>RS <?= number_format($item['price'], 2) ?></td>
                    <td>RS <?= number_format($item['discount_price'] ?? $item['price'], 2) ?></td>
                    <td>
                        <div class="menu-action-icons">
                            <button class="action-btn edit-btn" onclick="editMenuItem(<?= $item['id'] ?>)" title="Edit"><ion-icon name="create-outline"></ion-icon></button>
                            <button class="action-btn delete-btn" onclick="deleteMenuItem(<?= $item['id'] ?>)" title="Delete"><ion-icon name="trash-outline"></ion-icon></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($nonveg_items)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;">No non-vegetarian items found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button class="menu-add-button" onclick="openAddModal('non-vegetarian')">
            <i class="fas fa-plus"></i> Add New Non-Vegetarian Item
        </button>
    </section>

    <!-- Special Items Menu Section -->
    <section class="menu-section" id="special-items-menu">
        <div class="menu-section-title">
            <ion-icon name="star-outline"></ion-icon>
            <h2>Special Items</h2>
        </div>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Discount Price</th>
                    <th>Available Days</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sn = 1;
                foreach($special_items as $item): 
                ?>
                <tr>
                    <td><?= $sn++ ?></td>
                    <td><div class="menu-image-wrapper"><img src="..\<?= htmlspecialchars($item['image_path'] ?? 'assets/images/default-food.jpg') ?>" alt="<?= htmlspecialchars($item['food_name']) ?>"></div></td>
                    <td><?= htmlspecialchars($item['food_name']) ?></td>
                    <td>RS <?= number_format($item['price'], 2) ?></td>
                    <td>RS <?= number_format($item['discount_price'] ?? $item['price'], 2) ?></td>
                    <td>
                        <div class="available-days-display">
                        <?php
                        $days = $item['available_days'] ?? 'All Days';
                        $days = trim($days);
                        if ($days === 'All Days' || $days === 'All Day' || empty($days)) {
                            echo '<span class="day-badge all-days">All Days</span>';
                        } else {
                            $daysList = explode(',', $days);
                            $dayCount = count($daysList);
                            
                            if ($dayCount === 1) {
                                $fullDay = trim($daysList[0]);
                                echo '<span class="day-badge">' . htmlspecialchars($fullDay) . '</span>';
                            } elseif ($dayCount === 2) {
                                $dayShorts = array_map(function($d) {
                                    return strtoupper(substr(trim($d), 0, 3));
                                }, $daysList);
                                echo '<span class="day-badge">' . htmlspecialchars(implode(', ', $dayShorts)) . '</span>';
                            } else {
                                foreach ($daysList as $day) {
                                    $dayTrimmed = trim($day);
                                    if (!empty($dayTrimmed)) {
                                        $dayShort = strtoupper(substr($dayTrimmed, 0, 3));
                                        echo '<span class="day-badge">' . htmlspecialchars($dayShort) . '</span>';
                                    }
                                }
                            }
                        }
                        ?>
                        </div>
                    </td>
                    <td>
                        <div class="menu-action-icons">
                            <button class="action-btn edit-btn" onclick="editMenuItem(<?= $item['id'] ?>)" title="Edit"><ion-icon name="create-outline"></ion-icon></button>
                            <button class="action-btn delete-btn" onclick="deleteMenuItem(<?= $item['id'] ?>)" title="Delete"><ion-icon name="trash-outline"></ion-icon></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($special_items)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No special items found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button class="menu-add-button" onclick="openAddModal('special')">
            <i class="fas fa-plus"></i> Add New Special Item
        </button>
    </section>

    <!-- Add/Edit Modal -->
    <div id="menu-modal" class="menu-modal">
        <div class="menu-modal-content">
            <span class="menu-modal-close">&times;</span>
            <h2 id="modal-title">Add Menu Item</h2>
            <form id="menu-item-form" enctype="multipart/form-data">
                <input type="hidden" id="item-id" name="item-id">
                <input type="hidden" id="item-category" name="item-category">
                
                <div class="menu-form-group">
                    <label for="food-name">Food Name *</label>
                    <input type="text" id="food-name" name="food-name" required placeholder="Enter food name">
                </div>
                
                <div class="menu-form-group">
                    <label for="price">Price (RS) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required placeholder="Enter price in RS">
                </div>
                
                <div class="menu-form-group">
                    <label for="discount-price">Discount Price (RS)</label>
                    <input type="number" id="discount-price" name="discount-price" step="0.01" min="0" placeholder="Optional discount price in RS">
                </div>
                
                <div class="menu-form-group">
                    <label for="image-upload">Food Image</label>
                    <input type="file" id="image-upload" name="image-upload" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <small style="color:#666;">Accepted formats: JPG, PNG, WEBP (Max 5MB)</small>
                </div>
                
                <div id="special-day-group" class="menu-form-group" style="display:none;">
                    <label>Available Days *</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:10px;">
                        <label style="display:flex;align-items:center;gap:5px;">
                            <input type="checkbox" name="available-days[]" value="Monday"> Monday
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;">
                            <input type="checkbox" name="available-days[]" value="Tuesday"> Tuesday
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;">
                            <input type="checkbox" name="available-days[]" value="Wednesday"> Wednesday
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;">
                            <input type="checkbox" name="available-days[]" value="Thursday"> Thursday
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;">
                            <input type="checkbox" name="available-days[]" value="Friday"> Friday
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;">
                            <input type="checkbox" name="available-days[]" value="Saturday"> Saturday
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;">
                            <input type="checkbox" name="available-days[]" value="Sunday"> Sunday
                        </label>
                        <label style="display:flex;align-items:center;gap:5px;grid-column:span 2;">
                            <input type="checkbox" id="all-days-checkbox" onclick="toggleAllDays(this)"> All Days
                        </label>
                    </div>
                </div>
                
                <div class="menu-modal-actions">
                    <button type="button" class="menu-modal-button save" onclick="saveMenuItem()">
                        <i class="fas fa-save"></i> Save Item
                    </button>
                    <button type="button" class="menu-modal-button clear" onclick="clearForm()">
                        <i class="fas fa-eraser"></i> Clear Form
                    </button>
                    <button type="button" class="menu-modal-button" style="background:#6c757d;" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="menu-modal">
        <div class="menu-modal-content">
            <h2>Confirm Operation</h2>
            <p id="confirm-message">Are you sure you want to perform this action?</p>
            <div class="menu-modal-actions">
                <button class="menu-modal-button save" onclick="confirmAction()">Yes</button>
                <button class="menu-modal-button delete" onclick="closeConfirmModal()">No</button>
            </div>
        </div>
    </div>
</div>