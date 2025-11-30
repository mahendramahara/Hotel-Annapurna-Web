<?php require_once('includes/header.php'); ?>

<main class="contact-container">
    <header class="contact-hero">
        <h1>Get in Touch</h1>
        <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </header>

    <div id="alert-message" class="alert-message" style="display: none;"></div>

    <section class="contact-main">
        <form class="contact-form" id="contactForm">
            <div class="contact-form-group">
                <label class="contact-label" for="name">Full Name <span class="required">*</span></label>
                <div class="input-wrapper">
                    <ion-icon name="person-outline" class="form-icon"></ion-icon>
                    <input type="text" id="name" name="name" class="contact-input" placeholder="Your full name" required minlength="2">
                </div>
                <span class="error-message" id="name-error"></span>
            </div>
            <div class="contact-form-group">
                <label class="contact-label" for="email">Email Address <span class="required">*</span></label>
                <div class="input-wrapper">
                    <ion-icon name="mail-outline" class="form-icon"></ion-icon>
                    <input type="email" id="email" name="email" class="contact-input" placeholder="your.email@example.com" required>
                </div>
                <span class="error-message" id="email-error"></span>
            </div>
            <div class="contact-form-group">
                <label class="contact-label" for="subject">Subject <span class="required">*</span></label>
                <div class="input-wrapper">
                    <ion-icon name="pricetag-outline" class="form-icon"></ion-icon>
                    <input type="text" id="subject" name="subject" class="contact-input" placeholder="Message subject" required minlength="3">
                </div>
                <span class="error-message" id="subject-error"></span>
            </div>
            <div class="contact-form-group">
                <label class="contact-label" for="message">Message <span class="required">*</span></label>
                <div class="input-wrapper">
                    <ion-icon name="chatbox-outline" class="form-icon"></ion-icon>
                    <textarea id="message" name="message" class="contact-textarea" placeholder="Your message here..." required minlength="10"></textarea>
                </div>
                <span class="error-message" id="message-error"></span>
            </div>
            <button type="submit" class="contact-submit">
                <ion-icon name="send-outline"></ion-icon>
                <span>Send Message</span>
            </button>
        </form>

        <div class="contact-map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3533.0305641307093!2d85.3462!3d27.6795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb19a7c6cd4d6d%3A0x1f21c24515293b62!2sKoteshwor%2C%20Kathmandu%2044600!5e0!3m2!1sen!2snp!4v1234567890" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </section>

    <section class="contact-info">
        <div class="contact-info-item">
            <ion-icon name="location"></ion-icon>
            <h3>Address</h3>
            <p>446 Koteshwor<br>Kathmandu, Nepal 44600</p>
        </div>
        <div class="contact-info-item">
            <ion-icon name="mail"></ion-icon>
            <h3>Email</h3>
            <p><a href="mailto:contact@annapurna.com">contact@annapurna.com</a></p>
        </div>
        <div class="contact-info-item">
            <ion-icon name="call"></ion-icon>
            <h3>Phone</h3>
            <p><a href="tel:+977-1-4444444">+977 1-4444444</a><br><a href="tel:+977-1-5555555">+977 1-5555555</a></p>
        </div>
    </section>

    <section class="contact-team">
        <article class="contact-team-member">
            <div class="contact-team-image">
                <img src="/api/placeholder/120/120" alt="John Doe - General Manager">
            </div>
            <h3 class="contact-team-name">John Doe</h3>
            <p class="contact-team-position">General Manager</p>
            <p class="contact-team-phone">
                <ion-icon name="call"></ion-icon>
                <a href="tel:+977-98-1234567">+977 98-1234567</a>
            </p>
        </article>

        <article class="contact-team-member">
            <div class="contact-team-image">
                <img src="/api/placeholder/120/120" alt="Jane Smith - Customer Relations">
            </div>
            <h3 class="contact-team-name">Jane Smith</h3>
            <p class="contact-team-position">Customer Relations</p>
            <p class="contact-team-phone">
                <ion-icon name="call"></ion-icon>
                <a href="tel:+977-98-7654321">+977 98-7654321</a>
            </p>
        </article>

        <article class="contact-team-member">
            <div class="contact-team-image">
                <img src="/api/placeholder/120/120" alt="Mike Johnson - Events Manager">
            </div>
            <h3 class="contact-team-name">Mike Johnson</h3>
            <p class="contact-team-position">Events Manager</p>
            <p class="contact-team-phone">
                <ion-icon name="call"></ion-icon>
                <a href="tel:+977-98-1122334">+977 98-1122334</a>
            </p>
        </article>
    </section>
</main>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    document.querySelectorAll('.contact-input, .contact-textarea').forEach(el => el.classList.remove('input-error'));
    
    // Get form data
    const formData = new FormData(this);
    const submitBtn = this.querySelector('.contact-submit');
    const originalBtnText = submitBtn.innerHTML;
    
    // Validate
    let isValid = true;
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    
    if (name.length < 2) {
        showError('name', 'Name must be at least 2 characters');
        isValid = false;
    }
    
    if (!isValidEmail(email)) {
        showError('email', 'Please enter a valid email address');
        isValid = false;
    }
    
    if (subject.length < 3) {
        showError('subject', 'Subject must be at least 3 characters');
        isValid = false;
    }
    
    if (message.length < 10) {
        showError('message', 'Message must be at least 10 characters');
        isValid = false;
    }
    
    if (!isValid) return;
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<ion-icon name="hourglass-outline"></ion-icon><span>Sending...</span>';
    
    // Send form data
    fetch('api/contact-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            this.reset();
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(err => {
        showAlert('An error occurred. Please try again later.', 'error');
        console.error(err);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});

function showError(fieldId, message) {
    const errorEl = document.getElementById(fieldId + '-error');
    const inputEl = document.getElementById(fieldId);
    errorEl.textContent = message;
    inputEl.classList.add('input-error');
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showAlert(message, type) {
    const alertEl = document.getElementById('alert-message');
    alertEl.textContent = message;
    alertEl.className = 'alert-message alert-' + type;
    alertEl.style.display = 'block';
    
    setTimeout(() => {
        alertEl.style.display = 'none';
    }, 5000);
    
    // Scroll to alert
    alertEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>

<?php require_once('includes/footer.php'); ?>