<?php
require_once('includes/header.php');
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
        <div class="menu-item">
            <div class="image-container">
                <img src="https://www.w3schools.com/html/img_chania.jpg" alt="Pizza">
            </div>
            <h3>Pizza</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipiscing.</p>
            <p class="price">$55.0</p>
            <button class="order-now">Order Now</button>
        </div>

        <div class="menu-item">
            <div class="image-container">
                <img src="https://www.w3schools.com/html/img_chania.jpg" alt="Rice">
            </div>
            <h3>Rice</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipiscing.</p>
            <p class="price">$50.0</p>
            <button class="order-now">Order Now</button>
        </div>

        <div class="menu-item">
            <div class="image-container">
                <img src="https://www.w3schools.com/html/img_chania.jpg" alt="Green Salad">
            </div>
            <h3>Green Salad</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipiscing.</p>
            <p class="price">$45.0</p>
            <button class="order-now">Order Now</button>
        </div>

        <div class="menu-item">
            <div class="image-container">
                <img src="https://www.w3schools.com/html/img_chania.jpg" alt="Pasta">
            </div>
            <h3>Pasta</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipiscing.</p>
            <p class="price">$35.0</p>
            <button class="order-now">Order Now</button>
        </div>

        <div class="menu-item">
            <div class="image-container">
                <img src="https://www.w3schools.com/html/img_chania.jpg" alt="Pasta">
            </div>
            <h3>Pasta</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipiscing.</p>
            <p class="price">$35.0</p>
            <button class="order-now">Order Now</button>
        </div>

        <div class="menu-item">
            <div class="image-container">
                <img src="https://www.w3schools.com/html/img_chania.jpg" alt="Pasta">
            </div>
            <h3>Pasta</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipiscing.</p>
            <p class="price">$35.0</p>
            <button class="order-now">Order Now</button>
        </div>

        <div class="menu-item">
            <div class="image-container">
                <img src="https://www.w3schools.com/html/img_chania.jpg" alt="Pasta">
            </div>
            <h3>Pasta</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipiscing.</p>
            <p class="price">$35.0</p>
            <button class="order-now">Order Now</button>
        </div>

        <div class="menu-item">
            <div class="image-container">
                <img src="https://www.w3schools.com/html/img_chania.jpg" alt="Pasta">
            </div>
            <h3>Pasta</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipiscing.</p>
            <p class="price">$35.0</p>
            <button class="order-now">Order Now</button>
        </div>
    </div>
</section>

<section class="offer-container">
        <div class="section-title">
            <h2>Special Offers & Promotions</h2>
            <p>Take advantage of our exclusive deals and seasonal promotions designed to give you the best value for an unforgettable experience.</p>
        </div>
        <div class="offer-images">
            <div class="offer-images-collection">
                <div class="offerSlide"><img src="Images/Offer_Images/special1.jpg" alt="Image 1"></div>
                <div class="offerSlide"><img src="Images/Offer_Images/special2.jpg" alt="Image 2"></div>
                <div class="offerSlide"><img src="Images/Offer_Images/special3.jpg" alt="Image 3"></div>
                <div class="offerSlide"><img src="Images/Offer_Images/special4.jpg" alt="Image 4"></div>
                <div class="offerSlide"><img src="Images/Offer_Images/special5.jpg" alt="Image 5"></div>
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
                <img src="https://via.placeholder.com/400x300" alt="Luxurious Rooms">
            </div>
            <h2>Luxurious Rooms</h2>
            <p>Relax in our elegantly designed rooms featuring breathtaking views and all modern conveniences.</p>
            <button class="explore-btn">Explore Rooms</button>
        </div>

        <!-- Dining Section -->
        <div class="feature-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/400x300" alt="Exquisite Dining">
            </div>
            <h2>Exquisite Dining</h2>
            <p>Savor culinary delights crafted by our master chefs, from local flavors to international cuisines.</p>
            <button class="explore-btn">View Menu</button>
        </div>

        <!-- Amenities Section -->
        <div class="feature-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/400x300" alt="World-Class Amenities">
            </div>
            <h2>World-Class Amenities</h2>
            <p>Enjoy our spa, fitness center, infinity pool, and more for a truly indulgent stay.</p>
            <button class="explore-btn">See Amenities</button>
        </div>

        <!-- Testimonials Section -->
        <div class="feature-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/400x300" alt="Guest Testimonials">
            </div>
            <h2>Guest Testimonials</h2>
            <p>Hear from our happy guests and see why Hotel Annapurna is the top choice for travelers.</p>
            <button class="explore-btn">Read Reviews</button>
        </div>
    </div>
</section>

<section class="my-staffs-container">
    <div class="section-title">
        <h2>Meet Our Professional Team</h2>
        <p>Get to know the dedicated individuals who work tirelessly to ensure your stay exceeds expectations, bringing warmth and expertise to every interaction.</p>
    </div>
    <div class="staff-grid">
        <!-- Staff Card 1 -->
        <div class="staff-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/300x300" alt="Staff Name">
            </div>
            <div class="staff-info">
                <h2>John Doe</h2>
                <p class="role">Executive Chef</p>
            </div>
        </div>

        <!-- Staff Card 2 -->
        <div class="staff-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/300x300" alt="Staff Name">
            </div>
            <div class="staff-info">
                <h2>Jane Smith</h2>
                <p class="role">Hotel Manager</p>
            </div>
        </div>

        <!-- Staff Card 3 -->
        <div class="staff-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/300x300" alt="Staff Name">
            </div>
            <div class="staff-info">
                <h2>Robert Brown</h2>
                <p class="role">Front Desk Officer</p>
            </div>
        </div>

        <!-- Staff Card 3 -->
        <div class="staff-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/300x300" alt="Staff Name">
            </div>
            <div class="staff-info">
                <h2>Robert Brown</h2>
                <p class="role">Front Desk Officer</p>
            </div>
        </div>

        <!-- Staff Card 3 -->
        <div class="staff-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/300x300" alt="Staff Name">
            </div>
            <div class="staff-info">
                <h2>Robert Brown</h2>
                <p class="role">Front Desk Officer</p>
            </div>
        </div>

        <!-- Staff Card 3 -->
        <div class="staff-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/300x300" alt="Staff Name">
            </div>
            <div class="staff-info">
                <h2>Robert Brown</h2>
                <p class="role">Front Desk Officer</p>
            </div>
        </div>

        <!-- Staff Card 3 -->
        <div class="staff-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/300x300" alt="Staff Name">
            </div>
            <div class="staff-info">
                <h2>Robert Brown</h2>
                <p class="role">Front Desk Officer</p>
            </div>
        </div>

        <!-- Staff Card 3 -->
        <div class="staff-card">
            <div class="image-container">
                <img src="https://via.placeholder.com/300x300" alt="Staff Name">
            </div>
            <div class="staff-info">
                <h2>Robert Brown</h2>
                <p class="role">Front Desk Officer</p>
            </div>
        </div>
    </div>
</section>

<section class="customer-reviews-container">
    <div class="section-title">
        <h2>Guest Experiences & Reviews</h2>
        <p>Read what our valued guests have to say about their time with us, and see why Hotel Annapurna is the preferred choice for discerning travelers.</p>
    </div>
    <div class="reviews-grid" id="reviews-grid">
        <!-- Cards will be dynamically generated by JavaScript -->
    </div>
</section>

<?php require_once('includes/footer.php') ?>