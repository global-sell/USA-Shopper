# YBT Digital - Installation Guide

Complete step-by-step installation instructions for YBT Digital marketplace.

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **XAMPP/WAMP/LAMP** (includes Apache, MySQL, PHP)
  - PHP 7.4 or higher
  - MySQL 5.7 or higher
  - Apache 2.4 or higher

## 🚀 Installation Steps

### Step 1: Download and Extract

1. Download the project files
2. Extract to your web server directory:
   - **XAMPP:** `C:/xampp/htdocs/Rangpur food/`
   - **WAMP:** `C:/wamp64/www/Rangpur food/`
   - **LAMP:** `/var/www/html/Rangpur food/`

### Step 2: Start Services

1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** services
3. Ensure both services are running (green indicators)

### Step 3: Create Database

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on **"New"** in the left sidebar
3. Enter database name: `ybt_digital`
4. Select collation: `utf8mb4_general_ci`
5. Click **"Create"**

### Step 4: Import Database Schema

1. Click on the `ybt_digital` database in the left sidebar
2. Click on the **"Import"** tab
3. Click **"Choose File"** and select `database.sql` from the project folder
4. Scroll down and click **"Go"**
5. Wait for the import to complete (you should see a success message)

### Step 5: Configure Database Connection

1. Open `config/database.php` in a text editor
2. Verify the database credentials (default XAMPP settings):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'ybt_digital');
   ```
3. If you have custom MySQL credentials, update them accordingly

### Step 6: Set File Permissions

**For Windows (XAMPP):**
- Right-click on the `uploads` folder
- Properties → Security → Edit
- Give "Full Control" to your user account

**For Linux/Mac:**
```bash
chmod -R 755 uploads/
chmod -R 755 uploads/products/
chmod -R 755 uploads/screenshots/
```

### Step 7: Access the Application

1. **Frontend (User Side):**
   - URL: `http://localhost/Rangpur%20food/`
   - Or: `http://localhost/Rangpur%20food/index.php`

2. **Admin Panel:**
   - URL: `http://localhost/Rangpur%20food/admin/`
   - Default credentials:
     - Email: `admin@ybtdigital.com`
     - Password: `admin123`

### Step 8: First Login and Configuration

1. **Login to Admin Panel**
   - Use the default credentials above
   - **IMPORTANT:** Change the password immediately!

2. **Configure Settings**
   - Go to: Admin → Settings
   - Update the following:
     - Site Name
     - Currency settings
     - Tax percentage
     - Email settings
     - Payment gateway credentials

3. **Add Categories**
   - Go to: Admin → Categories
   - Add product categories (e.g., WordPress Themes, Mobile Apps, etc.)

4. **Add Products**
   - Go to: Admin → Add Product
   - Fill in product details
   - Upload product files and screenshots
   - Set pricing and category

## 🔧 Configuration Details

### Email Configuration

To enable email functionality:

1. Go to: Admin → Settings
2. Configure SMTP settings:
   - **From Email:** your-email@domain.com
   - **From Name:** Your Site Name

For testing, you can use Gmail SMTP:
- Host: smtp.gmail.com
- Port: 587
- Username: your-gmail@gmail.com
- Password: your-app-password

### Payment Gateway Setup

#### Razorpay
1. Sign up at [razorpay.com](https://razorpay.com)
2. Navigate to Settings → API Keys
3. Copy Key ID and Key Secret
4. In Admin → Settings → Payment Gateway:
   - Select "Razorpay"
   - Enter Key ID and Secret
   - Save settings

#### Stripe
1. Sign up at [stripe.com](https://stripe.com)
2. Get your API keys from Dashboard
3. In Admin → Settings → Payment Gateway:
   - Select "Stripe"
   - Enter Public and Secret keys
   - Save settings

#### PayPal
1. Sign up at [developer.paypal.com](https://developer.paypal.com)
2. Create a REST API app
3. Get Client ID and Secret
4. In Admin → Settings → Payment Gateway:
   - Select "PayPal"
   - Enter credentials
   - Save settings

## 🧪 Testing the Installation

### Test User Registration
1. Go to: `http://localhost/Rangpur%20food/signup.php`
2. Create a test user account
3. Verify you can login

### Test Product Browsing
1. Go to: `http://localhost/Rangpur%20food/products.php`
2. Browse products (if you added any)
3. Test search and filters

### Test Shopping Cart
1. Add products to cart
2. View cart
3. Apply a coupon code
4. Proceed to checkout

### Test Admin Panel
1. Login to admin panel
2. Check dashboard statistics
3. Add a test product
4. Create a coupon code

## 🐛 Troubleshooting

### Issue: "Database connection failed"
**Solution:**
- Ensure MySQL service is running in XAMPP
- Check database credentials in `config/database.php`
- Verify database `ybt_digital` exists

### Issue: "Cannot upload files"
**Solution:**
- Check folder permissions on `uploads/` directory
- Verify PHP settings in `php.ini`:
  ```ini
  upload_max_filesize = 50M
  post_max_size = 50M
  max_execution_time = 300
  ```
- Restart Apache after changing php.ini

### Issue: "Page not found" or 404 errors
**Solution:**
- Ensure `.htaccess` file exists in root directory
- Enable `mod_rewrite` in Apache:
  - Open `httpd.conf`
  - Uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
  - Restart Apache

### Issue: "Headers already sent" error
**Solution:**
- Check for whitespace before `<?php` tags
- Ensure no output before `header()` calls
- Check file encoding (should be UTF-8 without BOM)

### Issue: Emails not sending
**Solution:**
- Configure SMTP settings in Admin → Settings
- For local testing, use a service like Mailtrap
- Check PHP mail() function is enabled

### Issue: CSS/JS not loading
**Solution:**
- Clear browser cache (Ctrl + F5)
- Check browser console for errors
- Verify CDN links are accessible
- Check file paths in header.php

## 📱 Mobile Testing

Test responsive design:
1. Open in browser
2. Press F12 (Developer Tools)
3. Click device toolbar icon
4. Test different screen sizes:
   - Mobile: 375px width
   - Tablet: 768px width
   - Desktop: 1920px width

## 🔒 Security Recommendations

### After Installation:

1. **Change Default Admin Password**
   - Login to admin panel
   - Go to Profile
   - Update password

2. **Update Database Credentials**
   - Use a strong MySQL password
   - Update in `config/database.php`

3. **Secure Uploads Directory**
   - Ensure `.htaccess` is in uploads folder
   - Prevent direct file execution

4. **Enable HTTPS** (Production)
   - Get SSL certificate
   - Update SITE_URL in config
   - Force HTTPS redirects

5. **Disable Error Display** (Production)
   - In `config/config.php`:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

## 📊 Sample Data

The database includes:
- 1 Admin user (admin@ybtdigital.com)
- 6 Sample categories
- 5 Sample FAQ entries
- Default settings

To add sample products:
1. Login to admin panel
2. Go to Add Product
3. Create products manually or import

## 🎯 Next Steps

After successful installation:

1. ✅ Customize site settings
2. ✅ Add your logo
3. ✅ Create product categories
4. ✅ Add products
5. ✅ Configure payment gateway
6. ✅ Test complete purchase flow
7. ✅ Set up email notifications
8. ✅ Create coupon codes
9. ✅ Customize theme colors (optional)
10. ✅ Launch your store!

## 💡 Tips

- **Backup regularly:** Export database from phpMyAdmin
- **Test payments:** Use sandbox/test mode for payment gateways
- **Monitor logs:** Check Apache error logs for issues
- **Update regularly:** Keep PHP and MySQL updated
- **Use version control:** Track changes with Git

## 📞 Support

If you encounter issues:
1. Check this installation guide
2. Review the README.md file
3. Check error logs in XAMPP
4. Search for similar issues online
5. Contact support

## ✅ Installation Checklist

- [ ] XAMPP/WAMP installed
- [ ] Apache and MySQL running
- [ ] Database created
- [ ] Database schema imported
- [ ] Database credentials configured
- [ ] File permissions set
- [ ] Admin panel accessible
- [ ] Default admin login works
- [ ] Admin password changed
- [ ] Settings configured
- [ ] Categories added
- [ ] Test product added
- [ ] User registration works
- [ ] Shopping cart works
- [ ] Payment gateway configured
- [ ] Email settings configured

---

**Congratulations! Your YBT Digital marketplace is now ready to use! 🎉**
