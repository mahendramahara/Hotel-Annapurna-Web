<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annapurna Hotel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/blog-page-styles.css">
    <link rel="stylesheet" href="assets/css/contact-page-styles.css">
    <link rel="stylesheet" href="assets/css/checkout-page-styles.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <header class="main-header">
        <section class="top-navbar">
            <div class="top-left">
                <p>42400, <span>Tinkune-Kathmandu, Nepal</span></p>
            </div>
            <div class="top-right">
                <div class="top-social">
                    <a class="nav-link" href="#"><ion-icon name="ellipse"></ion-icon></a>
                    <a class="nav-link" href="#"><ion-icon name="ellipse"></ion-icon></a>
                    <a class="nav-link" href="#"><ion-icon name="logo-facebook"></ion-icon></a>
                    <a class="nav-link" href="#"><ion-icon name="logo-instagram"></ion-icon></a>
                    <a class="nav-link" href="#"><ion-icon name="logo-twitter"></ion-icon></a>
                    <a class="nav-link" href="#"><ion-icon name="logo-pinterest"></ion-icon></a>
                    <a class="nav-link" href="#"><ion-icon name="logo-google"></ion-icon></a>
                    <a class="nav-link" href="checkout.php"><ion-icon name="cart"></ion-icon></a>
                    <a class="nav-link" href="login.php"><ion-icon name="person"></ion-icon></a>
                </div>
            </div>
        </section>

        <section class="top-menubar">
            <div class="top-logo">
                <a href="index.php">
                    <img src="assets/images/logo.png" alt="Hotel Annapurna Logo">
                </a>
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