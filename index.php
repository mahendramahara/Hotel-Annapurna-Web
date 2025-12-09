<?php
require_once('includes/header.php');
require_once('config/db.php');

// Fetch food items (limit to 8 for homepage)
$stmt_food = $conn->prepare("SELECT * FROM food_items ORDER BY created_at DESC LIMIT 8");
$stmt_food->execute();
$food_items = $stmt_food->get_result();

// Fetch staff members (limit to 8 for homepage)
$stmt_staff = $conn->prepare("SELECT first_name, last_name, role, profile_pic FROM users WHERE role = 'staff' LIMIT 8");
$stmt_staff->execute();
$staff_members = $stmt_staff->get_result();
?>

<section class="home-images-container">
    <div class="slider-container">
        <div class="slider">
            <div class="slide"><img src="assets/images/home_slider/img1.jpg" alt="Luxurious Room"></div>
            <div class="slide"><img src="assets/images/home_slider/img2.jpg" alt="Fine Dining"></div>
            <div class="slide"><img src="assets/images/home_slider/img3.jpg" alt="Relaxing Spa"></div>
            <div class="slide"><img src="assets/images/home_slider/img4.jpg" alt="Event Hall"></div>
            <div class="slide"><img src="assets/images/home_slider/img5.jpg" alt="Beautiful View"></div>
        </div>

        <button class="slider-arrow slider-arrow-left" onclick="prevSlide()">‹</button>
        <button class="slider-arrow slider-arrow-right" onclick="nextSlide()">›</button>

        <div class="slide-indicators"></div>
    </div>
</section>

<section class="service-option-container">
    <!-- Section Title -->
    <div class="section-title">
        <h2>Explore Our Premium Services</h2>
        <p>
            Experience the finest hospitality with our world-class accommodations, dining options, and culinary delights, all designed to make your stay memorable.
        </p>
    </div>

    <div class="grid">
        <!-- Room Booking Section -->
        <div class="grid-item">
            <div class="image-container">
                <img src="assets/images/service_option/luxury-room.jpg" alt="Luxury Room" class="grid-image">
            </div>
            <div class="content">
                <h3>Luxurious Rooms</h3>
                <p>Experience unparalleled comfort and elegance in our beautifully designed rooms.</p>
                <div class="button-group">
                    <button class="button" onclick="viewRooms('Pages/Rooms.html')">View Rooms <ion-icon name="bed-outline"></ion-icon></button>
                    <button class="button" onclick="bookRoom()">Book Now <ion-icon name="arrow-forward-outline"></ion-icon></button>
                </div>
            </div>
        </div>

        <!-- Table Booking Section -->
        <div class="grid-item">
            <div class="image-container">
                <img src="assets/images/service_option/dining-table.jpg" alt="Dining Table" class="grid-image">
            </div>
            <div class="content">
                <h3>Elegant Dining</h3>
                <p>Reserve a table to enjoy a fine dining experience with exquisite cuisines.</p>
                <div class="button-group">
                    <button class="button" onclick="viewTable('Pages/Table.html')">View Tables <ion-icon name="restaurant-outline"></ion-icon></button>
                    <button class="button" onclick="bookTable()">Book Now <ion-icon name="arrow-forward-outline"></ion-icon></button>
                </div>
            </div>
        </div>

        <!-- Food Ordering Section -->
        <div class="grid-item">
            <div class="image-container">
                <img src="assets/images/service_option/food-order.jpg" alt="Food Ordering" class="grid-image">
            </div>
            <div class="content">
                <h3>Delicious Food</h3>
                <p>Order food from our exclusive menu, delivered to your room or table.</p>
                <div class="button-group">
                    <button class="button" onclick="viewMenu('Pages/Menu.html')">View Menu <ion-icon name="fast-food-outline"></ion-icon></button>
                    <button class="button" onclick="orderFood()">Order Now <ion-icon name="arrow-forward-outline"></ion-icon></button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="food-items-container">
    <div class="section-title">
        <h2>Savor Our Culinary Excellence</h2>
        <p>Discover a world of flavors through our carefully curated menu, featuring both local specialties and international favorites prepared by our expert chefs.</p>
    </div>

    <div class="menu-grid">
        <?php 
        if ($food_items->num_rows > 0) {
            while($item = $food_items->fetch_assoc()) {
                $image_url = !empty($item['image_path']) ? $item['image_path'] : 'images/menu/demoFood.jpg';
                $description = !empty($item['short_description']) ? substr($item['short_description'], 0, 60) . '...' : 'Delicious food item from our kitchen';
        ?>
        <div class="menu-item">
            <div class="image-container">
                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>">
            </div>
            <h3><?php echo htmlspecialchars($item['food_name']); ?></h3>
            <p><?php echo htmlspecialchars($description); ?></p>
            <p class="price">RS <?php echo number_format($item['price'], 2); ?></p>
            <button class="order-now" onclick="addToCart('food', <?php echo $item['id']; ?>, {name: '<?php echo addslashes($item['food_name']); ?>', price: <?php echo $item['price']; ?>, image: '<?php echo addslashes($image_url); ?>'})">Order Now</button>
        </div>
        <?php 
            }
        } else {
            echo '<p style="grid-column: 1/-1; text-align: center;">No food items available at the moment.</p>';
        }
        ?>
    </div>
</section>

<section class="offer-container">
        <div class="section-title">
            <h2>Special Offers & Promotions</h2>
            <p style="color:blanchedalmond">Take advantage of our exclusive deals and seasonal promotions designed to give you the best value for an unforgettable experience.</p>
        </div>
        <div class="offer-images">
            <div class="offer-images-collection">
                <div class="offerSlide"><img src="assets/images/offer_images/special1.jpg" alt="Image 1"></div>
                <div class="offerSlide"><img src="assets/images/offer_images/special2.jpg" alt="Image 2"></div>
                <div class="offerSlide"><img src="assets/images/offer_images/special3.jpg" alt="Image 3"></div>
                <div class="offerSlide"><img src="assets/images/offer_images/special4.jpg" alt="Image 4"></div>
                <div class="offerSlide"><img src="assets/images/offer_images/special5.jpg" alt="Image 5"></div>
            </div>
            <button class="offerSlider-arrow left" onclick="prevOfferSlide()">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </button>
            <button class="offerSlider-arrow right" onclick="nextOfferSlide()">
                <ion-icon name="chevron-forward-outline"></ion-icon>
            </button>
            <div class="offerSlider-indicators"></div>
        </div>
    </section>

<section class="hotel-features">
    <div class="section-title">
        <h2>Experience Luxury at Its Finest</h2>
        <p>Immerse yourself in an environment where comfort meets elegance, with amenities and services that cater to your every need.</p>
    </div>

    <div class="features-grid">
        <!-- Room Section -->
        <div class="feature-card">
            <div class="image-container">
                <img src="./assets/images/featured_images/Featured1.jpg" alt="Luxurious Rooms">
            </div>
            <h2>Luxurious Rooms</h2>
            <p>Relax in our elegantly designed rooms featuring breathtaking views and all modern conveniences.</p>
            <button class="explore-btn" onclick="goToRooms()">Explore Rooms</button>
        </div>

        <!-- Dining Section -->
        <div class="feature-card">
            <div class="image-container">
                <img src="./assets/images/featured_images/Featured2.jpg" alt="Exquisite Dining">
            </div>
            <h2>Exquisite Dining</h2>
            <p>Savor culinary delights crafted by our master chefs, from local flavors to international cuisines.</p>
            <button class="explore-btn" onclick="goToMenu()">View Menu</button>
        </div>

        <!-- Amenities Section -->
        <div class="feature-card">
            <div class="image-container">
                <img src="./assets/images/featured_images/Featured3.jpg" alt="World-Class Amenities">
            </div>
            <h2>World-Class Amenities</h2>
            <p>Enjoy our spa, fitness center, infinity pool, and more for a truly indulgent stay.</p>
            <button class="explore-btn" onclick="goToAmenities()">See Amenities</button>
        </div>

        <!-- Testimonials Section -->
        <div class="feature-card">
            <div class="image-container">
                <img src="./assets/images/featured_images/Featured4.jpg" alt="Guest Testimonials">
            </div>
            <h2>Guest Testimonials</h2>
            <p>Hear from our happy guests and see why Hotel Annapurna is the top choice for travelers.</p>
            <button class="explore-btn" onclick="goToReviews()">Read Reviews</button>
        </div>
    </div>
</section>

<section class="my-staffs-container">
    <div class="section-title">
        <h2>Meet Our Professional Team</h2>
        <p>Get to know the dedicated individuals who work tirelessly to ensure your stay exceeds expectations, bringing warmth and expertise to every interaction.</p>
    </div>
    <div class="staff-grid">
        <?php 
        if ($staff_members->num_rows > 0) {
            while($staff = $staff_members->fetch_assoc()) {
                $profile_pic = !empty($staff['profile_pic']) ? $staff['profile_pic'] : 'https://via.placeholder.com/300x300?text=Staff';
                $full_name = $staff['first_name'] . ' ' . $staff['last_name'];
                $role = ucfirst($staff['role']);
        ?>
        <div class="staff-card">
            <div class="image-container">
                <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="<?php echo htmlspecialchars($full_name); ?>">
            </div>
            <div class="staff-info">
                <h2><?php echo htmlspecialchars($full_name); ?></h2>
                <p class="role"><?php echo htmlspecialchars($role); ?></p>
            </div>
        </div>
        <?php 
            }
        } else {
            echo '<p style="grid-column: 1/-1; text-align: center;">No staff members available to display.</p>';
        }
        ?>
    </div>
</section>

<section class="customer-reviews-container">
    <div class="section-title">
        <h2>Guest Experiences & Reviews</h2>
        <p>Read what our valued guests have to say about their time with us, and see why Hotel Annapurna is the preferred choice for discerning travelers.</p>
    </div>

    <?php 
        $reviews = [
            [
                "name" => "Sita Sharma",
                "image" => "./assets/images/guest_images/Guest1.jpeg",
                "rating" => 5,
                "review" => "Amazing hospitality and delicious food. Felt like home."
            ],
            [
                "name" => "Aarav Thapa",
                "image" => "./assets/images/guest_images/Guest2.jpg",
                "rating" => 4,
                "review" => "Calm environment, friendly staff, and clean rooms. Great stay overall."
            ],
            [
                "name" => "Prasant Adhikari",
                "image" => "./assets/images/guest_images/Guest3.jpg",
                "rating" => 5,
                "review" => "Loved the ambiance and the service was exceptional."
            ],
            [
                "name" => "Rohit Bista",
                "image" => "./assets/images/guest_images/Guest4.jpg",
                "rating" => 3,
                "review" => "Good experience but check-in could have been faster."
            ]
        ];
    ?>

    <div class="reviews-grid">
        <?php foreach ($reviews as $r): ?>
            <div class="review-card">
                <div class="reviewer-image">
                    <img src="<?= $r['image']; ?>" alt="<?= $r['name']; ?>">
                </div>

                <div class="review-content">
                    <div class="review-stars">
                        <?php 
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $r['rating'] 
                                    ? '<i class="fa-solid fa-star"></i>' 
                                    : '<i class="fa-regular fa-star"></i>';
                            }
                        ?>
                    </div>

                    <p class="review-text">"<?= $r['review']; ?>"</p>
                    <h3 class="reviewer-name"><?= $r['name']; ?></h3>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>


<script>
// Add to cart functionality
function addToCart(itemType, itemId, itemData) {
    const STORAGE_KEY = 'hotelCart';
    let cart = JSON.parse(localStorage.getItem(STORAGE_KEY)) || { food: [], rooms: [], tables: [] };
    
    // Check if item already exists in cart
    const existingItem = cart[itemType].find(item => item.id == itemId);
    
    if (existingItem) {
        existingItem.quantity = (existingItem.quantity || 1) + 1;
    } else {
        cart[itemType].push({
            id: itemId,
            name: itemData.name,
            price: itemData.price,
            image: itemData.image,
            quantity: 1
        });
    }
    
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    
    // Show success message and redirect to cart
    alert('Item added to cart successfully!');
    window.location.href = 'cart.php';
}

function goToRooms() {
    window.location.href = 'rooms.php';
}

function goToMenu() {
    window.location.href = 'menu.php';
}

function goToAmenities() {
    window.location.href = 'contact.php';
}

function goToReviews() {
    window.location.href = 'blogs.php';
}
</script>

<?php require_once('includes/footer.php') ?>