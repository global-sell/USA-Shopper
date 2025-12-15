# YBT Digital - Digital Product Selling Platform

A complete, responsive digital product marketplace built with PHP, MySQL, and Material Design Bootstrap.

## 🌟 Features

### User Features
- **Authentication System**
  - User registration and login
  - Password reset functionality
  - Profile management
  
- **Product Browsing**
  - Advanced search and filtering
  - Category-based navigation
  - Product detail pages with screenshots
  - Related products suggestions
  
- **Shopping & Checkout**
  - Shopping cart functionality
  - Coupon/discount code support
  - Multiple payment gateway support (Razorpay, Stripe, PayPal)
  - Tax calculation
  
- **Order Management**
  - Order history
  - Secure download system with expiry
  - Download limit tracking
  - Order receipts

### Admin Features
- **Dashboard**
  - Sales analytics
  - Revenue charts
  - Top-selling products
  - Recent orders overview
  
- **Product Management**
  - Add/Edit/Delete products
  - Upload digital files
  - Multiple screenshot support
  - Category management
  - Status control (Active/Inactive)
  
- **Order Management**
  - View all orders
  - Update order status
  - Transaction tracking
  
- **User Management**
  - View all users
  - Block/Unblock users
  - Purchase history
  
- **Coupon System**
  - Create discount codes
  - Flat or percentage discounts
  - Usage limits
  - Expiry dates
  
- **Settings**
  - Payment gateway configuration
  - Tax settings
  - Email settings
  - Currency settings

### Design Features
- **Responsive Design**
  - Mobile-first approach
  - Native app-like mobile experience
  - Professional desktop layout
  - Adaptive components
  
- **Dark/Light Mode**
  - Theme toggle
  - Persistent theme preference
  
- **Mobile Navigation**
  - Bottom navigation bar
  - AppBar header
  - Touch-friendly interface

## 🛠️ Technology Stack

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript
- **UI Framework:** Material Design Bootstrap (MDB)
- **Icons:** Font Awesome 6
- **Charts:** Chart.js

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/LAMP (for local development)

## 🚀 Installation

1. **Clone or Download the Project**
   ```bash
   # Place files in your web server directory
   # For XAMPP: C:/xampp/htdocs/Rangpur food/
   ```

2. **Create Database**
   - Open phpMyAdmin
   - Create a new database named `ybt_digital`
   - Import the `database.sql` file

3. **Configure Database Connection**
   - Open `config/database.php`
   - Update database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'ybt_digital');
     ```

4. **Set Permissions**
   - Ensure `uploads/` directory is writable
   - Set appropriate permissions for file uploads

5. **Access the Application**
   - Frontend: `http://localhost/Rangpur%20food/`
   - Admin Panel: `http://localhost/Rangpur%20food/admin/`

## 🔐 Default Admin Credentials

- **Email:** admin@ybtdigital.com
- **Password:** admin123

**⚠️ Important:** Change the default admin password immediately after first login!

## 📁 Project Structure

```
Rangpur food/
├── admin/                  # Admin panel
│   ├── includes/          # Admin header/footer
│   ├── index.php          # Dashboard
│   ├── products.php       # Product management
│   ├── orders.php         # Order management
│   ├── settings.php       # System settings
│   └── ...
├── api/                   # API endpoints
│   ├── add-to-cart.php
│   └── cart-count.php
├── config/                # Configuration files
│   ├── config.php         # Main config
│   └── database.php       # Database connection
├── includes/              # Shared components
│   ├── header.php
│   └── footer.php
├── uploads/               # Upload directories
│   ├── products/          # Digital product files
│   └── screenshots/       # Product images
├── index.php              # Homepage
├── products.php           # Product listing
├── product-detail.php     # Product details
├── cart.php               # Shopping cart
├── checkout.php           # Checkout page
├── orders.php             # User orders
├── profile.php            # User profile
├── login.php              # Login page
├── signup.php             # Registration
├── database.sql           # Database schema
└── README.md              # This file
```

## 💳 Payment Gateway Setup

### Razorpay
1. Sign up at [razorpay.com](https://razorpay.com)
2. Get your API Key ID and Secret
3. Add them in Admin → Settings → Payment Gateway

### Stripe
1. Sign up at [stripe.com](https://stripe.com)
2. Get your Public and Secret keys
3. Add them in Admin → Settings → Payment Gateway

### PayPal
1. Sign up at [paypal.com](https://developer.paypal.com)
2. Get your Client ID and Secret
3. Add them in Admin → Settings → Payment Gateway

## 📧 Email Configuration

Configure SMTP settings in Admin → Settings for:
- Order confirmations
- Password reset emails
- Welcome emails

## 🎨 Customization

### Change Site Name
- Admin → Settings → Site Name

### Update Colors
- Edit CSS variables in `includes/header.php`

### Add Custom Pages
- Create new PHP files
- Include header and footer
- Add navigation links

## 🔒 Security Features

- Password hashing (bcrypt)
- SQL injection prevention (prepared statements)
- XSS protection (input sanitization)
- Session timeout
- Secure download tokens
- CSRF protection ready

## 📱 Mobile Features

- Bottom navigation bar (4 tabs)
- Native app-like interface
- Touch-friendly buttons
- Swipeable carousels
- Optimized images

## 🐛 Troubleshooting

### Database Connection Error
- Check database credentials in `config/database.php`
- Ensure MySQL service is running
- Verify database exists

### File Upload Issues
- Check folder permissions (uploads/)
- Verify PHP upload limits in php.ini
- Ensure adequate disk space

### Email Not Sending
- Configure SMTP settings
- Check PHP mail() function
- Verify firewall settings

## 📝 License

This project is open-source and available for educational and commercial use.

## 👨‍💻 Support

For support and questions:
- Create a support ticket in the admin panel
- Contact: support@ybtdigital.com

## 🔄 Updates

### Version 1.0.0 (Current)
- Initial release
- Complete user and admin functionality
- Responsive design
- Dark/light mode
- Payment gateway integration

## 🎯 Future Enhancements

- [ ] Multi-language support
- [ ] Advanced analytics
- [ ] Product reviews and ratings
- [ ] Wishlist functionality
- [ ] Social media integration
- [ ] Live chat support
- [ ] Mobile app (React Native)
- [ ] API for third-party integrations

## 🙏 Credits

- Material Design Bootstrap (MDB)
- Font Awesome Icons
- Chart.js
- Google Fonts

---

**Built with ❤️ for digital entrepreneurs**
