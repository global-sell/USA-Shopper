# YBT Digital - Quick Start Guide

Get your digital marketplace up and running in 5 minutes!

## ⚡ Quick Installation (5 Minutes)

### 1. Setup (2 minutes)
```bash
# Start XAMPP services
- Open XAMPP Control Panel
- Start Apache
- Start MySQL
```

### 2. Database (1 minute)
```bash
# Create database
1. Go to http://localhost/phpmyadmin
2. Create database: ybt_digital
3. Import: database.sql
```

### 3. Access (30 seconds)
```bash
# Open in browser
Frontend: http://localhost/Rangpur%20food/
Admin: http://localhost/Rangpur%20food/admin/

# Login credentials
Email: admin@ybtdigital.com
Password: admin123
```

### 4. Configure (1.5 minutes)
```bash
# In Admin Panel
1. Change admin password
2. Update site name
3. Set currency
4. Add a category
5. Add a product
```

## 🎯 Essential Features

### For Users
- ✅ Browse products
- ✅ Search & filter
- ✅ Add to cart
- ✅ Apply coupons
- ✅ Checkout
- ✅ Download purchases

### For Admins
- ✅ Dashboard analytics
- ✅ Manage products
- ✅ Manage orders
- ✅ Manage users
- ✅ Create coupons
- ✅ Configure settings

## 📱 Mobile Features

- Bottom navigation bar
- Native app-like UI
- Touch-friendly buttons
- Responsive design
- Dark/Light mode

## 🔑 Default Accounts

**Admin:**
- Email: admin@ybtdigital.com
- Password: admin123

**Test User:**
- Create via signup page

## 🎨 Customization

### Change Colors
Edit in `includes/header.php`:
```css
--primary-color: #1976d2;
--secondary-color: #dc004e;
```

### Change Logo
Admin → Settings → Site Logo

### Add Products
Admin → Add Product → Fill form → Upload files

## 💳 Payment Setup

### Quick Test Mode
1. Admin → Settings
2. Select payment gateway
3. Enter test API keys
4. Save

### Supported Gateways
- Razorpay
- Stripe
- PayPal

## 📊 Dashboard Overview

After login, you'll see:
- Total revenue
- Total orders
- Total products
- Total users
- Recent orders
- Top products
- Revenue chart

## 🛒 Test Purchase Flow

1. Browse products
2. Add to cart
3. Apply coupon: TEST10
4. Checkout
5. Complete payment
6. Download product

## 🎁 Sample Coupons

Create test coupons:
- Code: WELCOME10 (10% off)
- Code: FLAT50 ($50 off)
- Code: FREESHIP (Free shipping)

## 📁 Important Files

```
config/config.php       - Main configuration
config/database.php     - Database settings
includes/header.php     - Site header
includes/footer.php     - Site footer
admin/                  - Admin panel
uploads/                - Upload directory
```

## 🔧 Quick Fixes

### Can't login?
- Check database connection
- Verify credentials
- Clear browser cache

### Files won't upload?
- Check uploads/ permissions
- Increase PHP upload limit
- Check disk space

### Page looks broken?
- Clear cache (Ctrl + F5)
- Check internet (for CDN)
- Verify file paths

## 📞 Need Help?

1. Check INSTALLATION.md
2. Check README.md
3. Review error logs
4. Contact support

## ✅ Quick Checklist

- [ ] XAMPP running
- [ ] Database imported
- [ ] Admin login works
- [ ] Password changed
- [ ] Category added
- [ ] Product added
- [ ] Test purchase done

## 🚀 You're Ready!

Your marketplace is now live and ready to sell digital products!

**Next Steps:**
1. Add more products
2. Configure payment gateway
3. Customize design
4. Launch to users

---

**Happy Selling! 💰**
