<?php require_once('includes/header.php');  ?>

<main class="contact-container">
        <header class="contact-hero">
            <h1>Get in Touch</h1>
            <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </header>

        <section class="contact-main">
            <form class="contact-form">
                <div class="contact-form-group">
                    <label class="contact-label" for="name">Name</label>
                    <i class="fas fa-user form-icon"></i>
                    <input type="text" id="name" class="contact-input" required>
                </div>
                <div class="contact-form-group">
                    <label class="contact-label" for="email">Email</label>
                    <i class="fas fa-envelope form-icon"></i>
                    <input type="email" id="email" class="contact-input" required>
                </div>
                <div class="contact-form-group">
                    <label class="contact-label" for="subject">Subject</label>
                    <i class="fas fa-tag form-icon"></i>
                    <input type="text" id="subject" class="contact-input" required>
                </div>
                <div class="contact-form-group">
                    <label class="contact-label" for="message">Message</label>
                    <i class="fas fa-comment form-icon"></i>
                    <textarea id="message" class="contact-textarea" required></textarea>
                </div>
                <button type="submit" class="contact-submit">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>

            <div class="contact-map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3533.0305641307093!2d85.3462!3d27.6795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb19a7c6cd4d6d%3A0x1f21c24515293b62!2sKoteshwor%2C%20Kathmandu%2044600!5e0!3m2!1sen!2snp!4v1234567890" allowfullscreen="" loading="lazy" style="border:0; width:100%; height:100%;"></iframe>
            </div>
        </section>

        <section class="contact-info">
            <div class="contact-info-item">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Address</h3>
                <p>446 Koteshwor<br>Kathmandu, Nepal</p>
            </div>
            <div class="contact-info-item">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p>contact@annapurna.com</p>
            </div>
            <div class="contact-info-item">
                <i class="fas fa-phone-alt"></i>
                <h3>Phone</h3>
                <p>+977 1-4444444<br>+977 1-5555555</p>
            </div>
        </section>

        <section class="contact-team">
            <article class="contact-team-member">
                <div class="contact-team-image">
                    <img src="/api/placeholder/120/120" alt="Team Member">
                </div>
                <div class="contact-team-info">
                    <h3 class="contact-team-name">John Doe</h3>
                    <p class="contact-team-position">General Manager</p>
                    <p class="contact-team-phone">
                        <i class="fas fa-phone"></i>
                        +977 98-1234567
                    </p>
                </div>
            </article>

            <article class="contact-team-member">
                <div class="contact-team-image">
                    <img src="/api/placeholder/120/120" alt="Team Member">
                </div>
                <div class="contact-team-info">
                    <h3 class="contact-team-name">Jane Smith</h3>
                    <p class="contact-team-position">Customer Relations</p>
                    <p class="contact-team-phone">
                        <i class="fas fa-phone"></i>
                        +977 98-7654321
                    </p>
                </div>
            </article>

            <article class="contact-team-member">
                <div class="contact-team-image">
                    <img src="/api/placeholder/120/120" alt="Team Member">
                </div>
                <div class="contact-team-info">
                    <h3 class="contact-team-name">Mike Johnson</h3>
                    <p class="contact-team-position">Events Manager</p>
                    <p class="contact-team-phone">
                        <i class="fas fa-phone"></i>
                        +977 98-1122334
                    </p>
                </div>
            </article>
        </section>
    </main>
    
<?php require_once('includes/footer.php');  ?>