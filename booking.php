<?php 
require_once('includes/header.php');
require_once('config/db.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

$rooms_result = $conn->query("SELECT * FROM rooms WHERE status IN ('available', 'booked') ORDER BY price ASC LIMIT 2");
$tables_result = $conn->query("SELECT * FROM tables WHERE booking_status IN ('available', 'booked') ORDER BY price_main ASC LIMIT 2");

$rooms = [];
$tables = [];

while($row = mysqli_fetch_assoc($rooms_result)) {
    $rooms[] = $row;
}

while($row = mysqli_fetch_assoc($tables_result)) {
    $tables[] = $row;
}
?>
<main class="booking-main">
    <!-- Hero Section -->
    <header class="booking-hero">
        <div class="booking-hero-content">
            <h1 class="booking-hero-title">Luxury Awaits at Hotel Annapurna</h1>
            <p class="booking-hero-subtitle">Experience world-class hospitality with our carefully curated rooms and
                exquisite dining options, designed to make every moment unforgettable.</p>
        </div>
        <div class="booking-hero-overlay"></div>
    </header>

    <!-- Main Booking Options -->
    <section class="booking-options" aria-label="Main Booking Options">
        <article class="booking-option">
            <figure class="booking-option-figure">
                <img src="./assets/images/booking_images/Luxury-Rooms.jpg" alt="Luxury Room Interior with Mountain View"
                    class="booking-option-image">
            </figure>
            <div class="booking-option-content">
                <h2 class="booking-option-title">Luxury Rooms</h2>
                <p class="booking-option-description">Indulge in our meticulously designed rooms offering panoramic
                    mountain views, premium amenities, and unparalleled comfort for an unforgettable stay.</p>
                <nav class="booking-option-nav">
                    <a href="rooms.php" class="booking-option-button" aria-label="Explore Available Rooms">Explore
                        Rooms</a>
                </nav>
            </div>
        </article>

        <article class="booking-option">
            <figure class="booking-option-figure">
                <img src="./assets/images/booking_images/Fine-Dining.jpg" alt="Fine Dining Restaurant Interior"
                    class="booking-option-image">
            </figure>
            <div class="booking-option-content">
                <h2 class="booking-option-title">Fine Dining</h2>
                <p class="booking-option-description">Savor extraordinary culinary experiences in our acclaimed
                    restaurant, featuring both authentic local delicacies and international cuisine.</p>
                <nav class="booking-option-nav">
                    <a href="tables.php" class="booking-option-button" aria-label="Reserve Dining Table">Reserve
                        Table</a>
                </nav>
            </div>
        </article>
    </section>

    <!-- Featured Offerings Section -->
    <section class="booking-preview" id="rooms" aria-label="Featured Offerings">
        <h2 class="booking-preview-title">Featured Offerings</h2>

        <!-- Rooms Preview -->
        <section class="booking-preview-section" aria-label="Luxury Rooms and Suites">
            <h3 class="booking-preview-section-title">Luxury Rooms & Suites</h3>
            <div class="booking-preview-container">
                <?php foreach($rooms as $room): 
                    $price_today = $room['price_today'] ? $room['price_today'] : $room['price'];
                    $image_url = !empty($room['image_path']) ? $room['image_path'] : 'https://via.placeholder.com/400x300?text=Room';
                ?>
                <article class="booking-preview-item">
                    <figure class="booking-preview-figure">
                        <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($room['room_type']); ?> Room - <?php echo htmlspecialchars($room['room_no']); ?>" class="booking-preview-image">
                    </figure>
                    <div class="booking-preview-content">
                        <header>
                            <h3 class="booking-preview-name"><?php echo htmlspecialchars($room['room_type']); ?> - Room <?php echo htmlspecialchars($room['room_no']); ?></h3>
                            <p class="booking-preview-description"><?php echo htmlspecialchars($room['short_description']); ?></p>
                        </header>
                        <div class="booking-preview-price" aria-label="Room Price">
                            <span class="booking-preview-original" aria-label="Original Price">RS <?php echo number_format($room['price'], 2); ?></span>
                            <span class="booking-preview-discounted" aria-label="Discounted Price">RS <?php echo number_format($price_today, 2); ?></span>
                        </div>
                        <p class="booking-preview-availability"><?php echo ucfirst($room['status']); ?></p>
                        <footer class="booking-preview-footer">
                            <button onclick="reserveRoom(<?php echo htmlspecialchars(json_encode($room)); ?>)" class="booking-preview-button" aria-label="Book <?php echo htmlspecialchars($room['room_type']); ?> Room">Reserve Now</button>
                        </footer>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Dining Preview -->
        <section class="booking-preview-section" id="tables" aria-label="Dining Experiences">
            <h3 class="booking-preview-section-title">Dining Experiences</h3>
            <div class="booking-preview-container">
                <?php foreach($tables as $table): 
                    $price_today = $table['price_today'] ? $table['price_today'] : $table['price_main'];
                    $image_url = !empty($table['image_path']) ? $table['image_path'] : 'https://via.placeholder.com/400x300?text=Table';
                ?>
                <article class="booking-preview-item">
                    <figure class="booking-preview-figure">
                        <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($table['location']); ?> Dining Table - <?php echo htmlspecialchars($table['table_no']); ?>" class="booking-preview-image">
                    </figure>
                    <div class="booking-preview-content">
                        <header>
                            <h3 class="booking-preview-name"><?php echo htmlspecialchars($table['location']); ?> - Table <?php echo htmlspecialchars($table['table_no']); ?></h3>
                            <p class="booking-preview-description"><?php echo htmlspecialchars($table['short_description']); ?></p>
                        </header>
                        <div class="booking-preview-price" aria-label="Table Price">
                            <span class="booking-preview-original" aria-label="Original Price">RS <?php echo number_format($table['price_main'], 2); ?></span>
                            <span class="booking-preview-discounted" aria-label="Discounted Price">RS <?php echo number_format($price_today, 2); ?></span>
                        </div>
                        <p class="booking-preview-availability">Seats: <?php echo $table['total_chairs']; ?> | <?php echo ucfirst($table['booking_status']); ?></p>
                        <footer class="booking-preview-footer">
                            <button onclick="reserveTable(<?php echo htmlspecialchars(json_encode($table)); ?>)" class="booking-preview-button" aria-label="Reserve Table <?php echo htmlspecialchars($table['table_no']); ?>">Reserve Now</button>
                        </footer>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
    </section>

    <!-- Appreciation Section -->
    <aside class="booking-appreciation" aria-label="Thank You Message">
        <figure class="booking-appreciation-figure">
            <img src="./assets/images/booking_images/Attachment.jpg" alt="Luxury Hotel Experience"
                class="booking-appreciation-image">
        </figure>
        <div class="booking-appreciation-content">
            <h2 class="booking-appreciation-title">Thank You for Choosing Us</h2>
            <p class="booking-appreciation-text">At Hotel Annapurna, we're committed to creating extraordinary
                experiences that exceed your expectations. Our dedicated team of hospitality professionals is ready
                to personalize every aspect of your stay, ensuring moments that become cherished memories.</p>
            <p class="booking-appreciation-text">From our world-class amenities to our impeccable service, every
                detail is crafted with your comfort in mind. We look forward to welcoming you and making your stay
                truly exceptional.</p>
        </div>
    </aside>
</main>

<script>
const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

function reserveRoom(room) {
    if (!isLoggedIn) {
        alert('Please login to continue with your reservation');
        window.location.href = 'login.php';
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
                alert(data.message || 'Failed to create reservation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

function reserveTable(table) {
    if (!isLoggedIn) {
        alert('Please login to continue with your reservation');
        window.location.href = 'login.php';
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
                alert(data.message || 'Failed to create reservation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}
</script>

<?php require_once('includes/footer.php'); ?>