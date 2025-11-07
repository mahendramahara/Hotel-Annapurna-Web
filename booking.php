<?php require_once('includes/header.php'); ?>
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
                <img src="/assets/luxury-room-wide.jpg" alt="Luxury Room Interior with Mountain View"
                    class="booking-option-image">
            </figure>
            <div class="booking-option-content">
                <h2 class="booking-option-title">Luxury Rooms</h2>
                <p class="booking-option-description">Indulge in our meticulously designed rooms offering panoramic
                    mountain views, premium amenities, and unparalleled comfort for an unforgettable stay.</p>
                <nav class="booking-option-nav">
                    <a href="#rooms" class="booking-option-button" aria-label="Explore Available Rooms">Explore
                        Rooms</a>
                </nav>
            </div>
        </article>

        <article class="booking-option">
            <figure class="booking-option-figure">
                <img src="/assets/restaurant-wide.jpg" alt="Fine Dining Restaurant Interior"
                    class="booking-option-image">
            </figure>
            <div class="booking-option-content">
                <h2 class="booking-option-title">Fine Dining</h2>
                <p class="booking-option-description">Savor extraordinary culinary experiences in our acclaimed
                    restaurant, featuring both authentic local delicacies and international cuisine.</p>
                <nav class="booking-option-nav">
                    <a href="#tables" class="booking-option-button" aria-label="Reserve Dining Table">Reserve
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
                <article class="booking-preview-item">
                    <figure class="booking-preview-figure">
                        <img src="/assets/deluxe-room-square.jpg" alt="Deluxe Mountain View Room Interior"
                            class="booking-preview-image">
                    </figure>
                    <div class="booking-preview-content">
                        <header>
                            <h3 class="booking-preview-name">Deluxe Mountain View</h3>
                            <p class="booking-preview-description">Spacious 45m² room with private balcony, stunning
                                mountain views, and modern luxury amenities</p>
                        </header>
                        <div class="booking-preview-price" aria-label="Room Price">
                            <span class="booking-preview-original" aria-label="Original Price">$250</span>
                            <span class="booking-preview-discounted" aria-label="Discounted Price">$199</span>
                        </div>
                        <p class="booking-preview-availability">Available Now</p>
                        <footer class="booking-preview-footer">
                            <a href="#book-room" class="booking-preview-button"
                                aria-label="Book Deluxe Mountain View Room">Book Now</a>
                        </footer>
                    </div>
                </article>

                <article class="booking-preview-item">
                    <figure class="booking-preview-figure">
                        <img src="/assets/suite-square.jpg" alt="Executive Suite Interior"
                            class="booking-preview-image">
                    </figure>
                    <div class="booking-preview-content">
                        <header>
                            <h3 class="booking-preview-name">Executive Suite</h3>
                            <p class="booking-preview-description">Luxurious 75m² suite with separate living area,
                                premium amenities, and panoramic views</p>
                        </header>
                        <div class="booking-preview-price" aria-label="Suite Price">
                            <span class="booking-preview-original" aria-label="Original Price">$400</span>
                            <span class="booking-preview-discounted" aria-label="Discounted Price">$340</span>
                        </div>
                        <p class="booking-preview-availability">2 Suites Left</p>
                        <footer class="booking-preview-footer">
                            <a href="#book-room" class="booking-preview-button"
                                aria-label="Book Executive Suite">Book Now</a>
                        </footer>
                    </div>
                </article>
            </div>
        </section>

        <!-- Dining Preview -->
        <section class="booking-preview-section" id="tables" aria-label="Dining Experiences">
            <h3 class="booking-preview-section-title">Dining Experiences</h3>
            <div class="booking-preview-container">
                <article class="booking-preview-item">
                    <figure class="booking-preview-figure">
                        <img src="/assets/window-dining-square.jpg" alt="Window Side Dining Area"
                            class="booking-preview-image">
                    </figure>
                    <div class="booking-preview-content">
                        <header>
                            <h3 class="booking-preview-name">Premium Window Side</h3>
                            <p class="booking-preview-description">Intimate dining space with panoramic valley
                                views, perfect for romantic dinners</p>
                        </header>
                        <div class="booking-preview-price" aria-label="Dining Price">
                            <span class="booking-preview-original" aria-label="Original Price">$100</span>
                            <span class="booking-preview-discounted" aria-label="Discounted Price">$75</span>
                        </div>
                        <p class="booking-preview-availability">Limited Tables Available</p>
                        <footer class="booking-preview-footer">
                            <a href="#book-table" class="booking-preview-button"
                                aria-label="Reserve Window Side Table">Reserve Now</a>
                        </footer>
                    </div>
                </article>

                <article class="booking-preview-item">
                    <figure class="booking-preview-figure">
                        <img src="/assets/private-dining-square.jpg" alt="Private Dining Room"
                            class="booking-preview-image">
                    </figure>
                    <div class="booking-preview-content">
                        <header>
                            <h3 class="booking-preview-name">Private Dining Suite</h3>
                            <p class="booking-preview-description">Exclusive private dining room with personalized
                                service and customizable menu</p>
                        </header>
                        <div class="booking-preview-price" aria-label="Suite Dining Price">
                            <span class="booking-preview-original" aria-label="Original Price">$200</span>
                            <span class="booking-preview-discounted" aria-label="Discounted Price">$150</span>
                        </div>
                        <p class="booking-preview-availability">Booking Available</p>
                        <footer class="booking-preview-footer">
                            <a href="#book-table" class="booking-preview-button"
                                aria-label="Reserve Private Dining Suite">Reserve Now</a>
                        </footer>
                    </div>
                </article>
            </div>
        </section>
    </section>

    <!-- Appreciation Section -->
    <aside class="booking-appreciation" aria-label="Thank You Message">
        <figure class="booking-appreciation-figure">
            <img src="/assets/luxury-experience.jpg" alt="Luxury Hotel Experience"
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
<?php require_once('includes/footer.php'); ?>