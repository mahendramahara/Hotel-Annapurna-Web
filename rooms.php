<?php 
require_once('includes/header.php');
require_once('config/db.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

$limit = 9;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$total_limit = $limit * $page;

$stmt_rooms = $conn->prepare("SELECT * FROM rooms ORDER BY created_at DESC LIMIT ?");
$stmt_rooms->bind_param("i", $total_limit);
$stmt_rooms->execute();
$rooms_result = $stmt_rooms->get_result();

$rooms_count_result = $conn->query("SELECT COUNT(*) as total FROM rooms");

$total_rooms = mysqli_fetch_assoc($rooms_count_result)['total'];

$rooms = [];
while($row = mysqli_fetch_assoc($rooms_result)) {
    $rooms[] = $row;
}
?>
<main class="rooms-container">
    <header class="rooms-hero">
        <div class="rooms-hero-overlay"></div>
        <div class="rooms-hero-content">
            <h1>Luxury Rooms & Suites</h1>
            <p>Experience unparalleled comfort in our beautifully designed rooms with stunning views and premium amenities.</p>
        </div>
    </header>

    <section class="rooms-grid">
        <?php foreach($rooms as $room): 
            $price_today = $room['price_today'] ? $room['price_today'] : $room['price'];
            $image_url = !empty($room['image_path']) ? $room['image_path'] : 'https://via.placeholder.com/400x300?text=Room';
            $amenities_list = !empty($room['amenities']) ? explode(',', $room['amenities']) : [];
        ?>
        <article class="rooms-card">
            <div class="rooms-card-image-wrapper">
                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($room['room_type']); ?> - Room <?php echo htmlspecialchars($room['room_no']); ?>" class="rooms-image">
                <div class="rooms-card-overlay"></div>
                <span class="rooms-status-badge <?php echo $room['status']; ?>"><?php echo ucfirst($room['status']); ?></span>
            </div>
            <div class="rooms-content">
                <div class="rooms-header">
                    <h2 class="rooms-title"><?php echo htmlspecialchars($room['room_type']); ?> - Room <?php echo htmlspecialchars($room['room_no']); ?></h2>
                    <span class="rooms-type-badge"><?php echo ucfirst($room['room_type']); ?></span>
                </div>
                <p class="rooms-description"><?php echo htmlspecialchars($room['short_description']); ?></p>
                
                <div class="rooms-info">
                    <div class="rooms-info-item">
                        <i class="fas fa-bed"></i>
                        <span><?php echo $room['total_beds']; ?> Bed<?php echo $room['total_beds'] > 1 ? 's' : ''; ?></span>
                    </div>
                    <div class="rooms-info-item">
                        <i class="fas fa-ruler-combined"></i>
                        <span><?php echo $room['bed_size']; ?> Size</span>
                    </div>
                </div>

                <div class="rooms-amenities">
                    <?php foreach(array_slice($amenities_list, 0, 3) as $amenity): ?>
                    <span class="rooms-amenity"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars(trim($amenity)); ?></span>
                    <?php endforeach; ?>
                </div>

                <div class="rooms-pricing">
                    <div class="rooms-price-item">
                        <span class="rooms-price-label">Regular Price</span>
                        <span class="rooms-price-original">RS <?php echo number_format($room['price'], 2); ?></span>
                    </div>
                    <div class="rooms-price-item">
                        <span class="rooms-price-label">Today's Price</span>
                        <span class="rooms-price-current">RS <?php echo number_format($price_today, 2); ?></span>
                    </div>
                </div>

                <div class="rooms-savings">
                    You Save: RS <?php echo number_format($room['price'] - $price_today, 2); ?>
                </div>

                <footer class="rooms-card-footer">
                    <button onclick="addRoomToCart(<?php echo htmlspecialchars(json_encode($room)); ?>)" class="rooms-book-btn">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button onclick="reserveRoom(<?php echo htmlspecialchars(json_encode($room)); ?>)" class="rooms-explore-btn">
                        <i class="fas fa-calendar-check"></i> Reserve Now
                    </button>
                </footer>
            </div>
        </article>
        <?php endforeach; ?>
    </section>

    <div class="rooms-pagination">
        <?php if($total_rooms > $page * $limit): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="rooms-see-more-btn">See More Rooms</a>
        <?php endif; ?>
        <?php if($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="rooms-see-less-btn">See Less</a>
        <?php endif; ?>
    </div>
</main>

<script>
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

    function addRoomToCart(room) {
        // Check room availability
        if (room.status !== 'available') {
            alert('⚠️ This room is not available for cart.\n\nCurrent Status: ' + room.status.toUpperCase());
            return;
        }

        const cartManager = {
            loadCart: function() {
                const stored = localStorage.getItem('hotelCart');
                return stored ? JSON.parse(stored) : { food: [], rooms: [], tables: [] };
            },
            saveCart: function(cart) {
                localStorage.setItem('hotelCart', JSON.stringify(cart));
            }
        };

        const cart = cartManager.loadCart();
        const checkInDate = new Date().toISOString().split('T')[0];
        const checkOutDate = new Date(Date.now() + 86400000).toISOString().split('T')[0];
        
        const roomData = {
            id: room.id,
            name: room.room_type + ' - Room ' + room.room_no,
            price: room.price_today || room.price,
            image: room.image_path || 'images/rooms/demoRoom.jpg',
            checkIn: checkInDate,
            checkOut: checkOutDate,
            nights: 1
        };

        const existing = cart.rooms.find(r => r.id === room.id && r.checkIn === checkInDate);
        if (existing) {
            existing.nights += 1;
        } else {
            cart.rooms.push(roomData);
        }

        cartManager.saveCart(cart);
        alert(`✓ ${room.room_type} added to cart!`);
        window.location.href = 'cart.php';
    }

    function reserveRoom(room) {
        if (!isLoggedIn) {
            alert('Please login to continue with your reservation');
            window.location.href = 'login.php';
            return;
        }
        
        // Check room availability
        if (room.status !== 'available') {
            alert('⚠️ This room is not available for booking.\n\nCurrent Status: ' + room.status.toUpperCase());
            return;
        }
        
        const price = room.price_today || room.price;
        const message = `Are you sure you want to reserve ${room.room_type} - Room ${room.room_no} for RS ${parseFloat(price).toFixed(2)}?`;
        
        if (confirm(message)) {
            const formData = new FormData();
            formData.append('item_type', 'room');
            formData.append('item_id', room.id);
            formData.append('item_data', JSON.stringify(room));
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
