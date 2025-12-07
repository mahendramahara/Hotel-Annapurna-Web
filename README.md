# 🏨 Hotel Annapurna - Room Booking, Food Ordering & Table Reservation System

## 📖 Project Introduction

**Hotel Annapurna** is a comprehensive **hotel management and booking system** built with **Core PHP, HTML, CSS, and JavaScript** (no frameworks, no heavy dependencies except Fetch API). This system is designed to reduce the complexity of in-counter menu ordering and provide a seamless digital experience for both hotel staff and customers.

### 🎯 Problem Solved

In traditional hotels, customers face several challenges:
- ❌ Long queues at the counter for food ordering
- ❌ Difficulty browsing and selecting from printed menus
- ❌ Time-consuming manual booking processes
- ❌ No real-time order tracking
- ❌ Complicated room/table reservation procedures
- ❌ No order history or order management for customers

**Hotel Annapurna solves these problems** by providing:
✅ **Quick online food ordering** - Browse menu anytime, order online
✅ **Instant room booking** - Check availability and book rooms 24/7
✅ **Easy table reservation** - Reserve dining tables for special occasions
✅ **Real-time order tracking** - Know exactly when your order is ready
✅ **Unified cart system** - Combine food, room, and table bookings in one checkout
✅ **Multiple payment options** - Cash, eSewa, Stripe integration
✅ **Admin control** - Complete management dashboard for staff

---

## 🚀 Key Features

### For Customers
- 🍽️ **Online Food Ordering** - Browse menu, add to cart, place orders
- 🛏️ **Room Booking** - View available rooms, check amenities, reserve instantly
- 🪑 **Table Reservation** - Reserve dining tables with capacity selection
- 💳 **Multiple Payment Methods** - Cash at Counter, eSewa, Stripe
- 📦 **Shopping Cart** - Multi-item cart (food + rooms + tables)
- 📝 **Order History** - Track all your orders and bookings
- 👤 **User Profile** - Manage account info, view past orders
- 📚 **Blog & News** - Read hotel updates, like/comment/share posts
- 🎟️ **Coupon Codes** - Apply discount codes at checkout
- 📧 **Email Notifications** - Get order confirmations and updates

### For Admin/Staff
- 📊 **Dashboard** - Real-time statistics and analytics
- 🍽️ **Food Management** - Add/Edit/Delete menu items with categories
- 🛏️ **Room Management** - Control room inventory, pricing, availability
- 🪑 **Table Management** - Manage dining tables and bookings
- 📋 **Order Management** - View, update, and track all orders
- 👥 **Customer Management** - Manage customer accounts and roles
- 💰 **Payment Tracking** - Monitor payment status (Pending/Paid/Failed)
- 📸 **Image Management** - Upload and manage product images
- 📧 **Contact Management** - Handle customer inquiries
- 📝 **Blog Management** - Create, edit, delete blog posts
- 💼 **Staff Management** - Manage staff members and roles
- 🎟️ **Coupon Management** - Create and manage discount codes

### Technical Features
- ✅ **Prepared Statements** - Protection against SQL injection
- ✅ **Role-Based Access Control** - Admin, Staff, Customer roles
- ✅ **Secure Authentication** - Password hashing with verification
- ✅ **Email Notifications** - PHPMailer integration
- ✅ **Payment Gateway Integration** - eSewa & Stripe
- ✅ **Session Management** - Secure user sessions
- ✅ **Responsive Design** - Works on mobile, tablet, desktop
- ✅ **Activity Logging** - Track all user activities
- ✅ **OTP Verification** - Email-based registration verification
- ✅ **Password Reset** - Secure password recovery

---

## 📋 Prerequisites

Before you begin, ensure you have the following:

- **XAMPP** (Apache + MySQL + PHP 7.4+)
  - Download: https://www.apachefriends.org/
  
- **Text Editor/IDE** - VS Code, Sublime Text, or similar
- **Git** (optional) - For version control
- **Google Account** (optional) - For Gmail SMTP setup
- **Modern Web Browser** - Chrome, Firefox, Edge, Safari

---

## 🚀 Step-by-Step Local Setup Guide

### ✅ Step 1: Start XAMPP Services

1. **Open XAMPP Control Panel**
2. **Click START** for:
   - ✅ Apache
   - ✅ MySQL
3. Wait for **green indicators** showing both are running

```
Status:
✓ Apache: Running (Port 80)
✓ MySQL: Running (Port 3306)
```

---

### ✅ Step 2: Download/Clone Project Files

1. Navigate to XAMPP htdocs folder:
   ```
   C:\xampp\htdocs\
   ```

2. **Option A: Clone using Git**
   ```bash
   git clone https://github.com/mahendramahara/Hotel-Annapurna-Web.git
   ```

3. **Option B: Download Manually**
   - Download project ZIP file
   - Extract to `C:\xampp\htdocs\Hotel-Annapurna-Web`

Your project should be at:
```
C:\xampp\htdocs\Hotel-Annapurna-Web\
```

---

### ✅ Step 3: Configure Database Connection

**Open file:** `config/db.php`

**Update these credentials:**
```php
<?php
$host = "localhost";        // Database host (localhost for local)
$username = "root";         // Default XAMPP username
$password = "";             // Leave empty for default XAMPP
$database = "hotel_annapurna"; // Database name

// Connection
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

**Default XAMPP Credentials:**
- Host: `localhost`
- Username: `root`
- Password: `` (empty)

✅ **Save the file**

---

### ✅ Step 4: Create Database Tables

1. Open your browser and navigate to:
   ```
   http://localhost/Hotel-Annapurna-Web/database_setup.php
   ```

2. You should see:
   ```
   🗄️ Hotel Annapurna - Database Setup
   ```

3. The script will automatically create all tables:
   - ✅ Users table
   - ✅ Food Items table
   - ✅ Rooms table
   - ✅ Dining Tables table
   - ✅ Orders table
   - ✅ Blogs table
   - ✅ Coupons table
   - ✅ Contact Requests table
   - ✅ Activity Logs table
   - ✅ And more...

4. You should see:
   ```
   ✅ Database Setup Completed Successfully!
   ```

**If tables already exist:** They will be skipped, which is fine.

---

### ✅ Step 5: Seed Demo Data (Optional)

To populate the database with sample data for testing:

1. Navigate to:
   ```
   http://localhost/Hotel-Annapurna-Web/seed_database.php
   ```

2. This will add:
   - 📸 Sample rooms, food items, tables
   - 🎟️ Test coupon codes
   - 📝 Sample blog posts
   - 👥 Demo user accounts

**Sample Login Credentials** (if seeded):
- Email: `customer@example.com`
- Password: `password123`

---

### ✅ Step 6: Configure SMTP Email Service

Email is needed for:
- Registration verification
- Password reset emails
- Order confirmations
- Contact form responses

---

#### **Option A: Gmail SMTP (Recommended)**

**Prerequisites:**
1. Have a Google account
2. **Enable 2-Factor Authentication:**
   - Go to: https://myaccount.google.com/security
   - Enable 2-Step Verification

3. **Generate App Password:**
   - Go to: https://myaccount.google.com/apppasswords
   - Select: Mail → Windows Computer
   - Google generates a 16-character password
   - **Copy this password** (not your actual Gmail password)

**Configure in Code:**

Open: `config/setup_mailer.php`

Find and update:
```php
$mail->Host       = 'smtp.gmail.com';           // Gmail SMTP server
$mail->SMTPAuth   = true;
$mail->Username   = 'your-email@gmail.com';    // Your Gmail address
$mail->Password   = 'your-app-password';       // 16-char password from above
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 587;

// Set sender
$mail->setFrom('your-email@gmail.com', 'Hotel Annapurna');
```

✅ **Save the file**

---

#### **Option B: Mailtrap.io (For Testing)**

**Setup Mailtrap:**
1. Go to: https://mailtrap.io
2. Create free account
3. Create new inbox
4. Copy your SMTP credentials

**Configure in Code:**

Open: `config/setup_mailer.php`

Find and update:
```php
$mail->Host       = 'smtp.mailtrap.io';        // Mailtrap SMTP
$mail->SMTPAuth   = true;
$mail->Username   = 'your-mailtrap-user';    // From Mailtrap dashboard
$mail->Password   = 'your-mailtrap-password'; // From Mailtrap dashboard
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 2525;
```

✅ **Save the file**

---

### ✅ Step 7: Configure eSewa Payment Gateway

eSewa is a payment method for testing payments in Nepal.

**Update File Paths:**

Open: `includes/esewa-helper.php`

Verify/Update the base URL:
```php
$base_url = "http://localhost/Hotel-Annapurna-Web";
```

Ensure callback URLs match:
```php
$success_url = $base_url . "/esewa-success.php";
$failure_url = $base_url . "/esewa-failure.php";
```

Also check: `api/esewa-status-check.php`
```php
// Verify URLs match your domain
```

✅ **Save all files**

**eSewa Test Credentials:**
- MERCHANT CODE: (already configured in code)
- Test credentials for sandbox: Use test account in eSewa documentation

---

### ✅ Step 8: Access the Application

#### 🌐 Main Website (Customer Side):
```
http://localhost/Hotel-Annapurna-Web/
```

#### 🔐 Admin Dashboard (Staff/Admin):
```
http://localhost/Hotel-Annapurna-Web/admin/
```

---

## 👤 Creating Admin Account

### Quick Method: Direct Database Update

1. **Open phpMyAdmin:**
   ```
   http://localhost/phpmyadmin
   ```

2. **Select Database:**
   - Click on `hotel_annapurna` in left panel

3. **Edit User:**
   - Click on `users` table
   - Find your user account
   - Click **Edit** (pencil icon)
   - Change `role` column from `customer` to `admin`
   - Click **Save**

4. **Login to Admin:**
   ```
   URL: http://localhost/Hotel-Annapurna-Web/admin/
   Email: (your registered email)
   Password: (your password)
   ```

---

## 🧪 Testing the Application

### Customer Test Flow:

1. **Register/Login:**
   - Go to: `/register.php`
   - Create new account
   - Verify email (or check Mailtrap inbox)

2. **Browse Items:**
   - Menu → Add food items to cart
   - Rooms → Add rooms to cart
   - Tables → Add tables to cart

3. **Checkout:**
   - View Cart → Proceed to Checkout
   - Select Payment Method:
     - 💵 **Cash** - Pay at counter (instant)
     - 📱 **eSewa** - Test payment gateway
     - 💳 **Stripe** - Test card: 4242 4242 4242 4242

4. **Track Order:**
   - Profile → My Orders (for food)
   - Profile → My Bookings (for rooms/tables)

### Admin Test Flow:

1. **Login to Admin:**
   - URL: `/admin/`
   - Use admin credentials

2. **Explore Sections:**
   - 📊 Dashboard - Overview stats
   - 🍽️ Food Items - Manage menu
   - 🛏️ Rooms - Manage rooms
   - 🪑 Tables - Manage tables
   - 📋 Service Requests - View all orders
   - 👥 Customers - Manage users
   - 💰 Coupons - Create discounts
   - 📝 Blogs - Create posts
   - 💼 Staffs - Manage staff

---

## 📁 Project Structure

```
Hotel-Annapurna-Web/
│
├── admin/                          # Admin Dashboard
│   ├── index.php                  # Admin home page
│   ├── login.php                  # Admin login
│   ├── logout.php                 # Admin logout
│   ├── includes/
│   │   └── auth-guard.php        # Authentication check
│   ├── sections/                  # Admin management sections
│   │   ├── blogs.php             # Blog management
│   │   ├── contacts.php          # Contact management
│   │   ├── coupons.php           # Coupon management
│   │   ├── customers.php         # Customer management
│   │   ├── menu_items.php        # Food item management
│   │   ├── profile.php           # Admin profile
│   │   ├── requests.php          # Order management
│   │   ├── reviews.php           # Review management
│   │   ├── rooms.php             # Room management
│   │   ├── staffs.php            # Staff management
│   │   └── tables.php            # Table management
│   └── assets/                    # Admin styles and scripts
│       ├── css/
│       ├── js/
│       └── images/
│
├── api/                            # Backend API Endpoints
│   ├── admin-blogs.php            # Blog API
│   ├── admin-contacts.php         # Contact API
│   ├── admin-coupons.php          # Coupon API
│   ├── admin-dashboard.php        # Dashboard stats
│   ├── admin-orders.php           # Order management API
│   ├── admin-users.php            # User management API
│   ├── blog-interactions.php      # Blog likes/comments API
│   ├── cart-handler.php           # Shopping cart API
│   ├── contact-handler.php        # Contact form API
│   ├── create-booking.php         # Create room/table booking
│   ├── create-cart-order.php      # Cart checkout API
│   ├── esewa-status-check.php    # eSewa payment verification
│   ├── menu-handler.php           # Food item API
│   ├── order-handler.php          # Order processing API
│   ├── profile-handler.php        # User profile API
│   ├── room-handler.php           # Room management API
│   ├── table-handler.php          # Table management API
│   └── validate-coupon.php        # Coupon validation API
│
├── config/                         # Configuration Files
│   ├── db.php                     # Database connection
│   └── setup_mailer.php           # Email configuration
│
├── includes/                       # Shared Components
│   ├── activity-logger.php        # Logging utility
│   ├── esewa-helper.php           # eSewa payment helper
│   ├── footer.php                 # Footer component
│   └── header.php                 # Header/navigation
│
├── seeders/                        # Database Seeders
│   ├── blogs_seeder.php           # Sample blogs
│   ├── coupons_seeder.php         # Sample coupons
│   ├── food_items_seeder.php      # Sample food items
│   ├── rooms_seeder.php           # Sample rooms
│   ├── tables_seeder.php          # Sample tables
│   └── users_seeder.php           # Sample users
│
├── assets/                         # Frontend Assets
│   ├── css/                       # Stylesheets
│   │   ├── style.css              # Main styles
│   │   ├── responsive.css         # Mobile responsive
│   │   ├── menu.css
│   │   ├── rooms-tables.css
│   │   ├── booking.css
│   │   ├── cart.css
│   │   ├── payment.css
│   │   └── ...
│   ├── js/                        # JavaScript files
│   │   ├── script.js              # Main script
│   │   ├── forgetpwd.js
│   │   └── ...
│   └── images/                    # Static images
│       ├── home_slider/
│       ├── offer_images/
│       └── service_option/
│
├── images/                         # Uploaded Content
│   ├── blogs/                     # Blog images
│   ├── food/                      # Food item images
│   ├── menus/                     # Menu images
│   ├── profiles/                  # User profile pictures
│   ├── rooms/                     # Room images
│   └── tables/                    # Table images
│
├── uploads/                        # User Uploads
│   └── profiles/                  # User profile uploads
│
├── database_setup.php              # Create database tables
├── seed_database.php               # Populate demo data
├── index.php                       # Home page
├── rooms.php                       # Browse rooms
├── menu.php                        # Browse food items
├── tables.php                      # Browse tables
├── blogs.php                       # Browse blogs
├── blog-read.php                   # Read blog details
├── cart.php                        # Shopping cart
├── payment.php                     # Payment processing
├── booking.php                     # Booking details
├── contact.php                     # Contact form
├── register.php                    # User registration
├── register-handler.php            # Registration processing
├── login.php                       # User login
├── login-handler.php               # Login processing
├── logout.php                      # User logout
├── profile.php                     # User profile
├── my-orders.php                   # Order history
├── my-bookings.php                 # Booking history
├── forget-password.php             # Password reset request
├── reset-password.php              # Password reset form
├── verify-register.php             # Email verification
├── esewa-success.php               # eSewa success callback
├── esewa-failure.php               # eSewa failure callback
├── check_blogs.php                 # Blog checking utility
├── README.md                       # This file
└── composer.json                   # Dependencies (PHPMailer)
```

---

## 🔐 Security Considerations

### Important Security Reminders:

1. **Never Commit Sensitive Data:**
   - Database passwords
   - Email passwords
   - API keys
   - Payment credentials

2. **Before Going to Production:**
   - Change default admin password
   - Use HTTPS instead of HTTP
   - Enable firewall rules
   - Regular database backups
   - Implement rate limiting
   - Add CSRF token validation
   - Update all credentials

3. **Database Security:**
   - Regular backups
   - Restrict database access
   - Use strong passwords
   - Minimal user privileges

4. **Code Security:**
   - All queries use prepared statements ✅
   - Input validation on all forms ✅
   - Output escaping implemented ✅
   - SQL injection protection ✅

---

## 🐛 Troubleshooting

### ❌ Issue: "Connection refused" / Database Error

**Solutions:**
1. Ensure MySQL is running in XAMPP Control Panel
2. Verify correct credentials in `config/db.php`
3. Check database name: `hotel_annapurna`
4. Run `database_setup.php` again
5. Check MySQL port (default: 3306)

```bash
# Test MySQL connection
mysql -u root -h localhost
```

---

### ❌ Issue: "Email not sending" / SMTP Error

**Solutions:**
1. Verify SMTP credentials in `config/setup_mailer.php`
2. For Gmail:
   - Enable 2-Factor Authentication
   - Use App Password (16-char), not actual password
3. For Mailtrap:
   - Check credentials in dashboard
   - Verify port: 2525
4. Test with simpler SMTP first (Mailtrap)
5. Check firewall/antivirus blocking SMTP ports

**Test Email Script:**
```php
// Create test in config/setup_mailer.php
$mail->addAddress('your-test@email.com');
$mail->Subject = 'Test Email';
$mail->Body = 'Test message';
if(!$mail->send()) {
    echo 'Error: ' . $mail->ErrorInfo;
}
```

---

### ❌ Issue: "eSewa Payment Error" / URL Mismatch

**Solutions:**
1. Check base URL in `includes/esewa-helper.php`:
   ```php
   $base_url = "http://localhost/Hotel-Annapurna-Web";
   ```
2. Ensure URLs match your domain exactly
3. Clear browser cache
4. Test with eSewa test account
5. Verify callback URLs are accessible

---

### ❌ Issue: "404 Not Found" / Page Not Found

**Solutions:**
1. Verify file exists in correct location
2. Check URL path matches folder structure
3. Ensure Apache is running
4. Clear browser cache
5. Test: `http://localhost/phpmyadmin` to verify server

---

### ❌ Issue: "Images Not Uploading" / Upload Fails

**Solutions:**
1. Create required folders:
   ```
   images/rooms/
   images/food/
   images/tables/
   images/blogs/
   images/profiles/
   uploads/profiles/
   ```

2. Set proper permissions:
   - Right-click folder → Properties → Security
   - Grant Full Control or 777 permissions
   - Or via terminal: `chmod -R 755 images/ uploads/`

3. Check PHP upload limits in `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```

---

## 🎟️ Sample Coupon Codes (After Seeding)

After running `seed_database.php`, use these codes:

| Code | Discount | Min Purchase | Details |
|------|----------|--------------|---------|
| WELCOME10 | 10% off | RS 500 | Welcome discount |
| SAVE20 | 20% off | RS 1000 | Limited time |
| FREESHIP | RS 500 off | RS 2000 | Delivery discount |

---

## 💳 Payment Gateway Test Credentials

### eSewa Test Account

Use these credentials for testing eSewa payments:

| Field | Value |
|-------|-------|
| **Mobile Number** | 9806800001 (or 9806800002, 9806800003, 9806800004, 9806800005) |
| **Password** | Nepal@123 |
| **OTP** | 123456 |
| **Test URL** | https://developer.esewa.com.np/pages/Epay#credentials&urls |

**How to Test:**
1. Go to checkout and select eSewa payment
2. You'll be redirected to eSewa sandbox
3. Login with phone: `9806800001` and password: `Nepal@123`
4. Enter OTP: `123456`
5. Confirm payment

---

### Stripe Test Cards

Use these test card numbers for testing Stripe payments:

| Card Type | Card Number | Expiry | CVC |
|-----------|------------|--------|-----|
| **Visa** | 4242 4242 4242 4242 | 12/27 | 123 |
| **Test Success** | 4242 4242 4242 4242 | Any future date | Any 3 digits |
| **Test Declined** | 4000 0000 0000 0002 | Any future date | Any 3 digits |
| **Test Documentation** | https://docs.stripe.com/testing?testing-method=card-numbers | - | - |

**How to Test:**
1. Go to checkout and select Stripe (Credit/Debit Card)
2. Enter card number: `4242 4242 4242 4242`
3. Expiry: `12/27` (or any future date)
4. CVC: `123` (or any 3 digits)
5. Complete payment

---

## 📚 Database Schema

### Core Tables:

**users**
- id, first_name, last_name, email, contact, password
- profile_pic, address, role, status, salary
- created_at, updated_at

**food_items**
- id, category, food_name, price, discount_price
- image_path, available_days, short_description
- created_at, updated_at

**rooms**
- id, room_no, room_type, total_beds, bed_size
- status, price, price_today, image_path, amenities
- created_at, updated_at

**tables**
- id, table_no, total_chairs, booking_status
- price_main, price_today, location, image_path
- created_at, updated_at

**orders**
- id, user_id, order_type, item_id, item_name
- quantity, price, payment_method, payment_status
- booking_reference, status, notes
- created_at, updated_at

**blogs**
- id, title, category, content, featured_image
- author_id, views, status
- created_at, updated_at

**coupons**
- id, code, discount_type, discount_value
- min_purchase, max_discount, usage_limit
- valid_from, valid_until, status

---

## 💻 Technology Stack

| Technology | Details |
|-----------|---------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL/MariaDB |
| **Frontend** | HTML5, CSS3, JavaScript (Vanilla) |
| **Email** | PHPMailer |
| **Payment** | eSewa API, Stripe |
| **APIs** | RESTful endpoints, Fetch API |
| **Security** | Prepared Statements, Password Hashing |

---

## 🎓 Learning Outcomes

This project demonstrates:

✨ **Core PHP**
- OOP concepts
- Session management
- File handling
- Error handling

✨ **Database**
- SQL queries
- Prepared statements
- Transactions
- Relationships

✨ **Web Development**
- MVC architecture
- API design
- User authentication
- Authorization

✨ **Integration**
- Payment gateway integration
- Email services
- Third-party APIs

✨ **Security**
- Input validation
- SQL injection prevention
- Password security
- Session security

---

## 🎉 Getting Help

### Common Issues:

| Issue | Solution |
|-------|----------|
| Database won't connect | Check credentials in `config/db.php` |
| Email not working | Verify SMTP settings, enable 2FA (Gmail) |
| eSewa error | Update URLs in `includes/esewa-helper.php` |
| Admin can't access | Make sure role is set to `admin` in database |
| Images won't upload | Check folder permissions and file size limits |

### Resources:

- **Database Issues:** Check `config/db.php`
- **Email Issues:** Check `config/setup_mailer.php`
- **Payment Issues:** Check `includes/esewa-helper.php`
- **Admin Issues:** Check user role in phpMyAdmin

---

## 📞 Support

For help and support:

1. **GitHub Issues:** Open an issue on GitHub repository
2. **Email:** Contact project owner
3. **Documentation:** Check comments in code
4. **API:** See comments in `api/` folder files

---

## 📜 License

This project is **open-source** and free for educational use.

---

## 👨‍💻 About

**Created by:** Mahendra Mahara

**Purpose:** Educational project for learning core web development concepts without frameworks.

**What Makes This Special:**
- 🎯 **No Frameworks** - Pure PHP, HTML, CSS, JavaScript
- 🎯 **No Dependencies** - Only PHPMailer (email library)
- 🎯 **Production Ready** - Secure, scalable, maintainable code
- 🎯 **Well Documented** - Comments explain complex logic
- 🎯 **Learning Focused** - Perfect for students and learners
- 🎯 **Real-World Features** - Payment, Email, Booking systems

---

## 🌟 Key Highlights

✅ **Complete System** - From customer interface to admin dashboard  
✅ **Multiple Payment Methods** - Cash, eSewa, Stripe  
✅ **Email Integration** - Notifications and verification  
✅ **Responsive Design** - Works on all devices  
✅ **Admin Control** - Full CRUD operations  
✅ **Real-time Tracking** - Order status updates  
✅ **Security First** - Prepared statements, password hashing  
✅ **User Friendly** - Intuitive interface and navigation  

---

**Version:** 2.1.2  
**Last Updated:** December 2025  
**Status:** ✅ Production Ready

**Built with ❤️ by Mahendra Mahara**
