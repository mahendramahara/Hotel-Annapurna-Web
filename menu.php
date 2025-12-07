<?php 
require_once('includes/header.php');
require_once('config/db.php');

$limit = 5;
$veg_current_page = isset($_GET['veg_page']) ? $_GET['veg_page'] : 1;
$nonveg_current_page = isset($_GET['nonveg_page']) ? $_GET['nonveg_page'] : 1;
$special_current_page = isset($_GET['special_page']) ? $_GET['special_page'] : 1;

$veg_limit = $limit * $veg_current_page;
$nonveg_limit = $limit * $nonveg_current_page;
$special_limit = $limit * $special_current_page;

$stmt_veg = $conn->prepare("SELECT * FROM food_items WHERE category = 'veg' LIMIT ?");
$stmt_veg->bind_param("i", $veg_limit);
$stmt_veg->execute();
$veg_result = $stmt_veg->get_result();

$stmt_nonveg = $conn->prepare("SELECT * FROM food_items WHERE category = 'non-veg' LIMIT ?");
$stmt_nonveg->bind_param("i", $nonveg_limit);
$stmt_nonveg->execute();
$nonveg_result = $stmt_nonveg->get_result();

$stmt_special = $conn->prepare("SELECT * FROM food_items WHERE category = 'special' LIMIT ?");
$stmt_special->bind_param("i", $special_limit);
$stmt_special->execute();
$special_result = $stmt_special->get_result();

$veg_count_result = $conn->query("SELECT COUNT(*) as total FROM food_items WHERE category = 'veg'");
$nonveg_count_result = $conn->query("SELECT COUNT(*) as total FROM food_items WHERE category = 'non-veg'");
$special_count_result = $conn->query("SELECT COUNT(*) as total FROM food_items WHERE category = 'special'");

$veg_total = $veg_count_result->fetch_assoc()['total'];
$nonveg_total = $nonveg_count_result->fetch_assoc()['total'];
$special_total = $special_count_result->fetch_assoc()['total'];
?>

<div class="menu-container">
    <header class="menu-header">
        <h1>Hotel Annapurna</h1>
        <p>Discover our curated selection of delightful dishes</p>
    </header>

    <!-- Veg Items Section -->
    <div class="menu-section">
        <h2 class="menu-section-title">Vegetarian Delights</h2>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Select</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Special Price</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while($row = mysqli_fetch_assoc($veg_result)): 
                    $discount_price = $row['discount_price'] ? $row['discount_price'] : $row['price'];
                ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><input type="checkbox" class="menu-checkbox" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['food_name']); ?>" data-price="<?php echo $row['price']; ?>" data-discount="<?php echo $discount_price; ?>" data-image="<?php echo htmlspecialchars($row['image_path']); ?>"></td>
                    <td><img src="<?php echo !empty($row['image_path']) ? htmlspecialchars($row['image_path']) : 'https://via.placeholder.com/100x100?text=No+Image'; ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>"></td>
                    <td class="menu-item-name"><?php echo htmlspecialchars($row['food_name']); ?></td>
                    <td class="price-column">RS <?php echo number_format($row['price'], 2); ?></td>
                    <td class="discount-column">RS <?php echo number_format($discount_price, 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="see-more-container">
            <?php if($veg_total > $veg_current_page * $limit): ?>
            <a href="?veg_page=<?php echo $veg_current_page + 1; ?>&nonveg_page=<?php echo $nonveg_current_page; ?>&special_page=<?php echo $special_current_page; ?>" class="see-more-btn">See More Vegetarian Items</a>
            <?php endif; ?>
            <?php if($veg_current_page > 1): ?>
            <a href="?veg_page=<?php echo $veg_current_page - 1; ?>&nonveg_page=<?php echo $nonveg_current_page; ?>&special_page=<?php echo $special_current_page; ?>" class="see-less-btn">See Less</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Non-Veg Items Section -->
    <div class="menu-section">
        <h2 class="menu-section-title">Non-Vegetarian Specialties</h2>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Select</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Special Price</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while($row = mysqli_fetch_assoc($nonveg_result)): 
                    $discount_price = $row['discount_price'] ? $row['discount_price'] : $row['price'];
                ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><input type="checkbox" class="menu-checkbox" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['food_name']); ?>" data-price="<?php echo $row['price']; ?>" data-discount="<?php echo $discount_price; ?>" data-image="<?php echo htmlspecialchars($row['image_path']); ?>"></td>
                    <td><img src="<?php echo !empty($row['image_path']) ? htmlspecialchars($row['image_path']) : 'https://via.placeholder.com/100x100?text=No+Image'; ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>"></td>
                    <td class="menu-item-name"><?php echo htmlspecialchars($row['food_name']); ?></td>
                    <td class="price-column">RS <?php echo number_format($row['price'], 2); ?></td>
                    <td class="discount-column">RS <?php echo number_format($discount_price, 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="see-more-container">
            <?php if($nonveg_total > $nonveg_current_page * $limit): ?>
            <a href="?veg_page=<?php echo $veg_current_page; ?>&nonveg_page=<?php echo $nonveg_current_page + 1; ?>&special_page=<?php echo $special_current_page; ?>" class="see-more-btn">See More Non-Vegetarian Items</a>
            <?php endif; ?>
            <?php if($nonveg_current_page > 1): ?>
            <a href="?veg_page=<?php echo $veg_current_page; ?>&nonveg_page=<?php echo $nonveg_current_page - 1; ?>&special_page=<?php echo $special_current_page; ?>" class="see-less-btn">See Less</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Special Items Section -->
    <div class="menu-section todays-special">
        <h2 class="menu-section-title">Chef's Special</h2>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Select</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Special Price</th>
                    <th>Available On</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while($row = mysqli_fetch_assoc($special_result)): 
                    $discount_price = $row['discount_price'] ? $row['discount_price'] : $row['price'];
                ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><input type="checkbox" class="menu-checkbox" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['food_name']); ?>" data-price="<?php echo $row['price']; ?>" data-discount="<?php echo $discount_price; ?>" data-image="<?php echo htmlspecialchars($row['image_path']); ?>"></td>
                    <td><img src="<?php echo !empty($row['image_path']) ? htmlspecialchars($row['image_path']) : 'https://via.placeholder.com/100x100?text=No+Image'; ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>"></td>
                    <td class="menu-item-name"><?php echo htmlspecialchars($row['food_name']); ?></td>
                    <td class="price-column">RS <?php echo number_format($row['price'], 2); ?></td>
                    <td class="discount-column">RS <?php echo number_format($discount_price, 2); ?></td>
                    <td>
                        <?php 
                        $days = !empty($row['available_days']) ? $row['available_days'] : 'All Days';
                        $allDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        $availableDays = array_map('trim', explode(',', $days));
                        
                        if (strtolower($days) === 'all days' || strtolower($days) === 'everyday') {
                            echo '<div style="display: flex; gap: 3px; flex-wrap: wrap;">';
                            foreach ($allDays as $day) {
                                echo '<span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">' . $day . '</span>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div style="display: flex; gap: 3px; flex-wrap: wrap;">';
                            foreach ($allDays as $day) {
                                $isAvailable = false;
                                foreach ($availableDays as $availDay) {
                                    if (stripos($availDay, substr($day, 0, 3)) !== false || stripos($day, $availDay) !== false) {
                                        $isAvailable = true;
                                        break;
                                    }
                                }
                                if ($isAvailable) {
                                    echo '<span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">' . $day . '</span>';
                                } else {
                                    echo '<span style="background: #e0e0e0; color: #999; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">' . $day . '</span>';
                                }
                            }
                            echo '</div>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="see-more-container">
            <?php if($special_total > $special_current_page * $limit): ?>
            <a href="?veg_page=<?php echo $veg_current_page; ?>&nonveg_page=<?php echo $nonveg_current_page; ?>&special_page=<?php echo $special_current_page + 1; ?>" class="see-more-btn">See More Special Items</a>
            <?php endif; ?>
            <?php if($special_current_page > 1): ?>
            <a href="?veg_page=<?php echo $veg_current_page; ?>&nonveg_page=<?php echo $nonveg_current_page; ?>&special_page=<?php echo $special_current_page - 1; ?>" class="see-less-btn">See Less</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="selected-items-container">
        <div class="selected-items-header">
            <h2>Selected Items</h2>
        </div>
        <table class="menu-table" id="menu-selected-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Special Price</th>
                    <th>You Save</th>
                </tr>
            </thead>
            <tbody>
                <!-- Selected items will appear here -->
            </tbody>
            <tfoot>
                <tr class="menu-total-row">
                    <td colspan="2" style="text-align: right; font-weight: bold;">Total</td>
                    <td id="menu-total-price" style="font-weight: bold;">RS 0.00</td>
                    <td id="menu-total-discount" style="font-weight: bold;">RS 0.00</td>
                    <td id="menu-total-savings" style="font-weight: bold;">RS 0.00</td>
                </tr>
            </tfoot>
        </table>
        <div class="menu-buttons">
            <button class="menu-button menu-button-cart" id="menu-add-cart">Add to Cart</button>
            <button class="menu-button menu-button-buy" id="menu-buy-now">Buy Now</button>
        </div>
    </div>
</div>
<script>
    const menuCheckboxes = document.querySelectorAll('.menu-checkbox');
    const menuSelectedTable = document.querySelector('#menu-selected-table tbody');
    const menuTotalPrice = document.getElementById('menu-total-price');
    const menuTotalDiscount = document.getElementById('menu-total-discount');
    const menuTotalSavings = document.getElementById('menu-total-savings');
    let menuSelectedItems = [];

    function updateMenuTotals() {
        const totals = menuSelectedItems.reduce((acc, item) => ({
            price: acc.price + (parseFloat(item.price) || 0),
            discount: acc.discount + (parseFloat(item.discount_price) || parseFloat(item.price) || 0)
        }), { price: 0, discount: 0 });

        const savings = totals.price - totals.discount;

        menuTotalPrice.textContent = `RS ${totals.price.toFixed(2)}`;
        menuTotalDiscount.textContent = `RS ${totals.discount.toFixed(2)}`;
        menuTotalSavings.textContent = `RS ${savings.toFixed(2)}`;
    }

    function updateMenuSelectedTable() {
        menuSelectedTable.innerHTML = '';
        menuSelectedItems.forEach(item => {
            const price = parseFloat(item.price) || 0;
            const discount = parseFloat(item.discount_price) || price;
            const savings = (price - discount).toFixed(2);
            const imageUrl = item.image_url || item.image || 'images/menu/demoFood.jpg';
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><img src="${imageUrl}" alt="${item.food_name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></td>
                <td class="menu-item-name">${item.food_name || 'Unknown Item'}</td>
                <td class="price-column">RS ${price.toFixed(2)}</td>
                <td class="discount-column">RS ${discount.toFixed(2)}</td>
                <td class="menu-price">RS ${savings}</td>
            `;
            menuSelectedTable.appendChild(row);
        });
        updateMenuTotals();
    }

    menuCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', (e) => {
            const checkbox = e.target;
            const itemData = {
                id: parseInt(checkbox.dataset.id),
                food_name: checkbox.dataset.name,
                price: parseFloat(checkbox.dataset.price),
                discount_price: parseFloat(checkbox.dataset.discount),
                image_url: checkbox.dataset.image || checkbox.closest('tr').querySelector('img')?.src || 'https://via.placeholder.com/100x100?text=No+Image'
            };

            if (checkbox.checked) {
                menuSelectedItems.push(itemData);
            } else {
                menuSelectedItems = menuSelectedItems.filter(item => item.id !== itemData.id);
            }

            updateMenuSelectedTable();
        });
    });

    document.getElementById('menu-add-cart').addEventListener('click', () => {
        if (menuSelectedItems.length === 0) {
            alert('Please select items to add to cart!');
            return;
        }

        const cart = JSON.parse(localStorage.getItem('hotelCart') || '{"food":[],"rooms":[],"tables":[]}');
        
        menuSelectedItems.forEach(item => {
            const existing = cart.food.find(f => f.id === item.id);
            if (existing) {
                existing.quantity = (existing.quantity || 1) + 1;
            } else {
                cart.food.push({ 
                    id: item.id,
                    name: item.food_name, 
                    price: item.price,
                    image: item.image_url || 'images/menu/demoFood.jpg',
                    quantity: 1, 
                    type: 'food' 
                });
            }
        });

        localStorage.setItem('hotelCart', JSON.stringify(cart));
        alert(`✓ ${menuSelectedItems.length} item(s) added to cart!`);
        window.location.href = 'cart.php';
    });

    document.getElementById('menu-buy-now').addEventListener('click', () => {
        if (menuSelectedItems.length === 0) {
            alert('Please select items to purchase!');
            return;
        }

        const cart = JSON.parse(localStorage.getItem('hotelCart') || '{"foods":[],"rooms":[],"tables":[]}');
        
        menuSelectedItems.forEach(item => {
            const existing = cart.foods.find(f => f.id === item.id);
            if (existing) {
                existing.quantity = (existing.quantity || 1) + 1;
            } else {
                cart.foods.push({ ...item, quantity: 1, type: 'food' });
            }
        });

        localStorage.setItem('hotelCart', JSON.stringify(cart));
        window.location.href = 'cart.php';
    });
</script>
<?php require_once('includes/footer.php') ?>