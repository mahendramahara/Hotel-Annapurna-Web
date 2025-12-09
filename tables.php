<?php 
require_once('includes/header.php');
require_once('config/db.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

$limit = 9;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$total_limit = $limit * $page;

$stmt_tables = $conn->prepare("SELECT * FROM tables ORDER BY created_at DESC LIMIT ?");
$stmt_tables->bind_param("i", $total_limit);
$stmt_tables->execute();
$tables_result = $stmt_tables->get_result();

$tables_count_result = $conn->query("SELECT COUNT(*) as total FROM tables");

$total_tables = mysqli_fetch_assoc($tables_count_result)['total'];

$tables = [];
while($row = mysqli_fetch_assoc($tables_result)) {
    $tables[] = $row;
}
?>
<main class="tables-container">
    <header class="tables-hero">
        <div class="tables-hero-overlay"></div>
        <div class="tables-hero-content">
            <h1>Dining Experiences</h1>
            <p>Reserve your perfect dining spot in our elegant and luxurious restaurant spaces.</p>
        </div>
    </header>

    <section class="tables-grid">
        <?php foreach($tables as $table): 
            $price_today = $table['price_today'] ? $table['price_today'] : $table['price_main'];
            $image_url = !empty($table['image_path']) ? $table['image_path'] : 'https://via.placeholder.com/400x300?text=Table';
        ?>
        <article class="tables-card">
            <div class="tables-card-image-wrapper">
                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($table['location']); ?> - Table <?php echo htmlspecialchars($table['table_no']); ?>" class="tables-image">
                <div class="tables-card-overlay"></div>
                <span class="tables-status-badge <?php echo $table['booking_status']; ?>"><?php echo ucfirst($table['booking_status']); ?></span>
            </div>
            <div class="tables-content">
                <div class="tables-header">
                    <h2 class="tables-title"><?php echo htmlspecialchars($table['location']); ?> - Table <?php echo htmlspecialchars($table['table_no']); ?></h2>
                    <span class="tables-location-badge"><?php echo ucfirst($table['location']); ?></span>
                </div>
                <p class="tables-description"><?php echo htmlspecialchars($table['short_description']); ?></p>
                
                <div class="tables-info">
                    <div class="tables-info-item">
                        <i class="fas fa-chair"></i>
                        <span><?php echo $table['total_chairs']; ?> Seat<?php echo $table['total_chairs'] > 1 ? 's' : ''; ?></span>
                    </div>
                    <div class="tables-info-item">
                        <i class="fas fa-map-pin"></i>
                        <span><?php echo ucfirst($table['location']); ?></span>
                    </div>
                </div>

                <div class="tables-features">
                    <span class="tables-feature"><i class="fas fa-star"></i> Premium Service</span>
                    <span class="tables-feature"><i class="fas fa-wine-glass-alt"></i> Full Bar</span>
                    <span class="tables-feature"><i class="fas fa-utensils"></i> Fine Dining</span>
                </div>

                <div class="tables-pricing">
                    <div class="tables-price-item">
                        <span class="tables-price-label">Regular Price</span>
                        <span class="tables-price-original">RS <?php echo number_format($table['price_main'], 2); ?></span>
                    </div>
                    <div class="tables-price-item">
                        <span class="tables-price-label">Today's Price</span>
                        <span class="tables-price-current">RS <?php echo number_format($price_today, 2); ?></span>
                    </div>
                </div>

                <div class="tables-savings">
                    You Save: RS <?php echo number_format($table['price_main'] - $price_today, 2); ?>
                </div>

                <footer class="tables-card-footer">
                    <button onclick="addTableToCart(<?php echo htmlspecialchars(json_encode($table)); ?>)" class="tables-reserve-btn">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button onclick="reserveTable(<?php echo htmlspecialchars(json_encode($table)); ?>)" class="tables-explore-btn">
                        <i class="fas fa-calendar-check"></i> Reserve Now
                    </button>
                </footer>
            </div>
        </article>
        <?php endforeach; ?>
    </section>

    <div class="tables-pagination">
        <?php if($total_tables > $page * $limit): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="tables-see-more-btn">See More Tables</a>
        <?php endif; ?>
        <?php if($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="tables-see-less-btn">See Less</a>
        <?php endif; ?>
    </div>
</main>

<script>
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

    function addTableToCart(table) {
        // Check table availability
        if (table.booking_status !== 'available') {
            alert('⚠️ This table is not available for cart.\n\nCurrent Status: ' + table.booking_status.toUpperCase());
            return;
        }

        const cartManager = {
            loadCart: function() {
                const stored = localStorage.getItem('hotelCart');
                return stored ? JSON.parse(stored) : { foods: [], rooms: [], tables: [] };
            },
            saveCart: function(cart) {
                localStorage.setItem('hotelCart', JSON.stringify(cart));
            }
        };

        const cart = cartManager.loadCart();
        const reservationDate = new Date().toISOString().split('T')[0];
        
        const tableData = {
            ...table,
            date: reservationDate,
            quantity: 1
        };

        const existing = cart.tables.find(t => t.id === table.id && t.date === reservationDate);
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.tables.push(tableData);
        }

        cartManager.saveCart(cart);
        alert(`✓ Table ${table.table_no} added to cart!`);
        window.location.href = 'cart.php';
        window.location.href = 'cart.php';
    }

    function reserveTable(table) {
        if (!isLoggedIn) {
            alert('Please login to continue with your reservation');
            window.location.href = 'login.php';
            return;
        }
        
        // Check table availability
        if (table.booking_status !== 'available') {
            alert('⚠️ This table is not available for booking.\n\nCurrent Status: ' + table.booking_status.toUpperCase());
            return;
        }
        
        const price = table.price_today || table.price_main;
        const message = `Are you sure you want to reserve ${table.location} - Table ${table.table_no} (${table.total_chairs} seats) for RS ${parseFloat(price).toFixed(2)}?`;
        
        if (confirm(message)) {
            const formData = new FormData();
            formData.append('item_type', 'table');
            formData.append('item_id', table.id);
            formData.append('item_data', JSON.stringify(table));
            formData.append('price', price);
            
            fetch('api/create-booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'payment.php?booking_id=' + data.booking_id;
                } else {
                    alert('❌ ' + (data.message || 'Failed to create reservation'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ An error occurred. Please try again.');
            });
        }
    }
</script>

<?php require_once('includes/footer.php'); ?>
