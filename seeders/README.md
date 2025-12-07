# Hotel Annapurna Database Seeders

This folder contains seeder files to populate your database with realistic demo data.

## 📁 Seeder Files

- **users_seeder.php** - 20 users (admins, staff, customers)
- **food_items_seeder.php** - 20 food items (veg, non-veg, special)
- **tables_seeder.php** - 20 dining tables (different floors and locations)
- **rooms_seeder.php** - 20 hotel rooms (single, double, deluxe, suite)
- **blogs_seeder.php** - 20 detailed blog posts about food, culture, and hospitality
- **coupons_seeder.php** - 20 promotional coupons with various discounts

## 🚀 How to Use

### Step 1: Create Database Tables
First, run the database setup script to create all necessary tables:
```
http://localhost/github/Hotel-Annapurna-Web/database_setup.php
```

### Step 2: Seed Demo Data
After tables are created, run the main seeder:
```
http://localhost/github/Hotel-Annapurna-Web/seed_database.php
```

## 🔑 Default Login Credentials

All users have the password: **12345678**

### Admin Accounts
- admin@hotelannapurna.com
- sita.manager@hotelannapurna.com

### Staff Accounts
- krishna.chef@hotelannapurna.com
- gita.receptionist@hotelannapurna.com
- hari.waiter@hotelannapurna.com

### Customer Accounts
- rajesh.kumar@gmail.com
- priya.singh@yahoo.com
- amit.patel@hotmail.com

## 📊 Data Overview

| Category | Count | Description |
|----------|-------|-------------|
| Users | 20 | 2 admins, 6 staff, 12 customers |
| Food Items | 20 | Variety of Nepali and special dishes |
| Tables | 20 | Different capacities and locations |
| Rooms | 20 | From single to presidential suite |
| Blogs | 20 | Detailed posts about food and culture |
| Coupons | 20 | Various discount codes |

## 🖼️ Images

All seeded data uses `assets/images/demo.jpg` as placeholder image. Make sure this file exists or replace with your actual images.

## ⚠️ Important Notes

1. **Run database_setup.php first** - Tables must exist before seeding
2. **Password is 12345678** - For all users (change in production!)
3. **Re-running is safe** - Script checks for existing data and skips duplicates
4. **Clear data to re-seed** - Truncate tables if you want fresh data

## 🔄 Re-seeding

To re-seed the database:
1. Truncate all tables (or drop and recreate them)
2. Run seed_database.php again

## 📝 Customization

Each seeder file is independent and can be modified:
- Edit data arrays in each seeder file
- Adjust quantities
- Change default values
- Add new fields

## 🐛 Troubleshooting

**Error: Table doesn't exist**
- Run database_setup.php first

**Error: Duplicate entry**
- Data already exists, either skip or clear existing data

**Error: Foreign key constraint**
- Seed in correct order (users first, then others)

## 💡 Tips

- Seeders are idempotent (safe to run multiple times)
- Check execution summary for details
- Use different browsers/incognito for testing multiple user roles
- Images path can be customized in each seeder file

---

Created for Hotel Annapurna Web Application
