<?php
// Check if user is logged in via session or cookie
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    // Check for remember me cookie
    if (isset($_COOKIE['user_auth'])) {
        require_once __DIR__ . '/../config/db.php';
        
        $cookie_data = base64_decode($_COOKIE['user_auth']);
        list($user_id, $email) = explode('|', $cookie_data);
        
        // Verify user exists and get details
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ? AND email = ? AND status = 'verified'");
        $stmt->bind_param("is", $user_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_first_name'] = $user['first_name'];
            $_SESSION['user_last_name'] = $user['last_name'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['logged_in'] = true;
        }
    }
}

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$userFirstName = ($isLoggedIn && isset($_SESSION['user_first_name'])) ? $_SESSION['user_first_name'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annapurna Hotel</title>
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Page-Specific Stylesheets -->
    <link rel="stylesheet" href="assets/css/blog.css">
    <link rel="stylesheet" href="assets/css/booking-form.css">
    <link rel="stylesheet" href="assets/css/booking.css">
    <link rel="stylesheet" href="assets/css/contact.css">
    <link rel="stylesheet" href="assets/css/menu.css">
    <link rel="stylesheet" href="assets/css/rooms-tables.css">
    <link rel="stylesheet" href="assets/css/cart.css">
    <link rel="stylesheet" href="assets/css/blog-read.css">
    <link rel="stylesheet" href="assets/css/payment.css">
    <link rel="stylesheet" href="assets/css/my-bookings.css">
    <link rel="stylesheet" href="assets/css/my-orders.css">
    <!-- Responsive Design -->
    <link rel="stylesheet" href="assets/css/responsive.css">
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .user-welcome {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px;
            background: rgba(24, 145, 209, 0.1);
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            color: #1891d1;
        }
        .user-welcome ion-icon {
            font-size: 18px;
        }
        .no-account-text {
            font-size: 13px;
            color: #666;
        }
        .no-account-text a {
            color: #1891d1;
            text-decoration: none;
            font-weight: 600;
        }
        .no-account-text a:hover {
            text-decoration: underline;
        }
        .top-social {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: nowrap;
        }
        .user-dropdown {
            position: relative;
            display: inline-flex;
        }
        .user-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            border-radius: 12px;
            min-width: 200px;
            z-index: 1000;
            margin-top: 8px;
            overflow: hidden;
            animation: dropdownFade 0.3s ease;
            padding-top: 10px;
        }
        .user-dropdown-menu::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 0;
            right: 0;
            height: 10px;
            background: transparent;
        }
        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .user-dropdown-menu.show {
            display: block;
        }
        .user-dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 14px;
        }
        .user-dropdown-menu a:hover {
            background: rgba(255, 255, 255, 0.15);
            padding-left: 24px;
        }
        .user-dropdown-menu ion-icon {
            font-size: 20px;
        }
        .user-dropdown-menu .logout-link {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            color: #ff6b6b !important;
        }
        .user-dropdown-menu .logout-link:hover {
            background: rgba(255, 107, 107, 0.15);
            color: #ff4757 !important;
        }
        .user-initial-badge {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .user-initial-badge:hover {
            transform: scale(1.1);
        }
        .cart-icon-wrapper {
            position: relative;
            display: inline-block;
        }
        .cart-count-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            width: 12px;
            height: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    </style>
</head>

<body>
    <header class="main-header">
        <section class="top-navbar">
            <div class="top-left">
                <p>42400, <span>Tinkune-Kathmandu, Nepal</span></p>
            </div>
            <div class="top-center">
                <?php if ($isLoggedIn): ?>
                    <div class="user-welcome">
                        <ion-icon name="checkmark-circle"></ion-icon>
                        <span>Welcome, <?php echo htmlspecialchars($userFirstName); ?>!</span>
                    </div>
                <?php else: ?>
                    <div class="no-account-text">
                        No account? <a href="register.php">Create one</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="top-right">
                <div class="top-social">
                    <a class="nav-link" href="#"><ion-icon name="logo-facebook"></ion-icon></a>
                    <a class="nav-link" href="#"><ion-icon name="logo-instagram"></ion-icon></a>
                    <a class="nav-link" href="#"><ion-icon name="logo-twitter"></ion-icon></a>
                    <a class="nav-link cart-icon-wrapper" href="cart.php">
                        <ion-icon name="cart"></ion-icon>
                        <span class="cart-count-badge" id="cartCountBadge" style="display: none;">0</span>
                    </a>
                    
                    <?php if ($isLoggedIn): ?>
                        <div class="user-dropdown">
                            <a class="nav-link" href="#" style="display: flex; align-items: center;" onclick="toggleUserDropdown(event)">
                                <div class="user-initial-badge"><?php echo htmlspecialchars(substr($userFirstName, 0, 1)); ?></div>
                            </a>
                            <div class="user-dropdown-menu" id="userDropdownMenu">
                                <a href="profile.php">
                                    <ion-icon name="person"></ion-icon>
                                    My Profile
                                </a>
                                <a href="my-bookings.php">
                                    <ion-icon name="calendar"></ion-icon>
                                    My Bookings
                                </a>
                                <a href="my-orders.php">
                                    <ion-icon name="receipt"></ion-icon>
                                    My Orders
                                </a>
                                <a href="logout.php" class="logout-link">
                                    <ion-icon name="log-out"></ion-icon>
                                    Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a class="nav-link" href="login.php"><ion-icon name="person"></ion-icon></a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="top-menubar">
            <div class="top-logo">
                <a href="index.php">
                    <img src="assets/images/logo.png" alt="Hotel Annapurna Logo">
                </a>
            </div>
            <div class="menu-toggle" aria-label="Toggle navigation" role="button" tabindex="0">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="top-menuitems">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="booking.php">Booking</a></li>
                    <li><a href="blogs.php">Blogs</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <ion-icon name="search" class="search-icon" onclick="toggleSearch()"></ion-icon>
            </div>
            <div class="search-container">
                <input type="text" placeholder="Search...">
                <button type="button">Search</button>
            </div>
        </section>
    </header>

    <script>
        function updateCartBadge() {
            const badge = document.getElementById('cartCountBadge');
            if (!badge) return;

            const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
            let totalItems = 0;

            if (isLoggedIn) {
                fetch('api/cart-handler.php?action=get')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.cart) {
                            totalItems = data.cart.foods.length + data.cart.rooms.length + data.cart.tables.length;
                            if (totalItems > 0) {
                                badge.textContent = totalItems > 9 ? '9+' : totalItems;
                                badge.style.display = 'flex';
                            } else {
                                badge.style.display = 'none';
                            }
                        }
                    })
                    .catch(() => {
                        badge.style.display = 'none';
                    });
            } else {
                const cart = JSON.parse(localStorage.getItem('hotelCart') || '{"foods":[],"rooms":[],"tables":[]}');
                totalItems = cart.foods.length + cart.rooms.length + cart.tables.length;
                if (totalItems > 0) {
                    badge.textContent = totalItems > 9 ? '9+' : totalItems;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', updateCartBadge);
        window.addEventListener('storage', updateCartBadge);

        if (typeof window.cartUpdated === 'undefined') {
            window.cartUpdated = new Event('cartUpdated');
            window.addEventListener('cartUpdated', updateCartBadge);
        }

        function toggleUserDropdown(event) {
            event.preventDefault();
            event.stopPropagation();
            const menu = document.getElementById('userDropdownMenu');
            menu.classList.toggle('show');
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.user-dropdown');
            const menu = document.getElementById('userDropdownMenu');
            if (menu && dropdown && !dropdown.contains(event.target)) {
                menu.classList.remove('show');
            }
        });

        function toggleSearch() {
            if (window.innerWidth <= 900) return;
            const searchContainer = document.querySelector('.search-container');
            const searchIcon = document.querySelector('.search-icon');
            if (!searchContainer || !searchIcon) return;
            const isActive = !searchContainer.classList.contains('active');
            searchContainer.classList.toggle('active', isActive);
            searchIcon.classList.toggle('hidden', isActive);
            if (isActive) {
                const input = searchContainer.querySelector('input');
                if (input) input.focus();
            }
        }

        const menuToggle = document.querySelector('.menu-toggle');
        const topMenu = document.querySelector('.top-menuitems');

        function setMenuState(isOpen) {
            if (!topMenu || !menuToggle) return;
            topMenu.classList.toggle('open', isOpen);
            menuToggle.classList.toggle('active', isOpen);
            if (!isOpen) {
                const searchIcon = topMenu.querySelector('.search-icon');
                const searchContainer = document.querySelector('.search-container');
                if (searchIcon) searchIcon.classList.remove('hidden');
                if (searchContainer) searchContainer.classList.remove('active');
            }
        }

        if (menuToggle && topMenu) {
            menuToggle.addEventListener('click', function() {
                const isOpen = topMenu.classList.contains('open');
                setMenuState(!isOpen);
            });

            menuToggle.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    const isOpen = topMenu.classList.contains('open');
                    setMenuState(!isOpen);
                }
            });
        }
    </script>