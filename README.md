# KeyboardHub - Mechanical Keyboard E-Commerce Store

## 📋 Project Overview

A fully functional e-commerce web application for selling mechanical keyboards, switches, and keycaps. Built with HTML, CSS, JavaScript, PHP, and MySQL following traditional web development practices.

**Project Grade Distribution:**
- Base Version (Traditional Stack): 85%
- Additional Version (Modern Stack): 15%

---

## 🎯 Project Requirements Met

### ✅ Base Version Requirements (85%)

| Requirement | Implementation | Status |
|-------------|----------------|--------|
| **1 Home + 4-10 Content Pages** | 9 pages total (Home, Products, Product Detail, Cart, Checkout, Order Confirmation, Admin Login, Dashboard, Orders, Products) | ✓ |
| **Appropriate Text & Images** | All pages contain relevant content and emoji-based imagery | ✓ |
| **Page Titles** | Unique, descriptive titles on all pages | ✓ |
| **1 Table** | Orders table in admin dashboard | ✓ |
| **1 Form (4+ fields)** | Checkout form with 6 fields (name, email, phone, address, city, postal) | ✓ |
| **Server-side Processing** | process-order.php handles form submission | ✓ |
| **SQL SELECT** | Product listings, order displays | ✓ |
| **SQL INSERT** | New orders and order items | ✓ |
| **SQL UPDATE** | Order status updates, stock updates | ✓ |
| **Server-generated Page** | products.php dynamically generates product listings | ✓ |
| **JavaScript Validation** | Client-side form validation (validation.js) | ✓ |
| **PHP Validation** | Server-side validation in process-order.php | ✓ |
| **External CSS** | styles.css with 20+ styles | ✓ |
| **No Prohibited Elements** | No mailto, frames, jQuery, templates, or external links | ✓ |

---

## 🚀 Installation Instructions

### Prerequisites
- **XAMPP** / **WAMP** / **LAMP** (PHP 7.4+ and MySQL 8.0+)
- Web browser (Chrome, Firefox, Edge)
- Text editor (VS Code, Sublime Text)

### Step 1: Setup Web Server

1. **Download and Install XAMPP**
   - Download from: https://www.apachefriends.org/
   - Install to default location (C:\xampp on Windows)

2. **Start Apache and MySQL**
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL

### Step 2: Database Setup

1. **Access phpMyAdmin**
   - Open browser and go to: `http://localhost/phpmyadmin`
   - Click "New" to create a database

2. **Import Database**
   - Create database named: `keyboard_store`
   - Click "Import" tab
   - Choose `keyboard_store.sql` file
   - Click "Go" to import

   **OR run SQL manually:**
   - Click on `keyboard_store` database
   - Click "SQL" tab
   - Copy and paste entire contents of `keyboard_store.sql`
   - Click "Go"

### Step 3: Install Project Files

1. **Copy Project to htdocs**
   ```
   Windows: C:\xampp\htdocs\keyboard-store\
   Mac: /Applications/XAMPP/htdocs/keyboard-store/
   Linux: /opt/lampp/htdocs/keyboard-store/
   ```

2. **Project Structure**
   ```
   keyboard-store/
   ├── index.php
   ├── products.php
   ├── product-detail.php
   ├── cart.php
   ├── checkout.php
   ├── process-order.php
   ├── order-confirmation.php
   ├── config.php
   ├── functions.php
   ├── css/
   │   └── styles.css
   ├── js/
   │   └── validation.js
   ├── images/
   │   └── products/
   └── admin/
       ├── login.php
       ├── dashboard.php
       ├── products.php
       ├── orders.php
       └── logout.php
   ```

### Step 4: Configure Database Connection

1. Open `config.php`
2. Update database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Usually empty for XAMPP
   define('DB_NAME', 'keyboard_store');
   ```

### Step 5: Access the Application

1. **Customer Frontend:**
   - URL: `http://localhost/keyboard-store/`
   - Browse products, add to cart, checkout

2. **Admin Panel:**
   - URL: `http://localhost/keyboard-store/admin/`
   - Username: `admin`
   - Password: `admin123`

---

## 📱 Features

### Customer Features
- **Home Page:** Featured products and category browsing
- **Product Catalog:** Filter by category, search, sort by price/name
- **Product Details:** View specifications, images, stock availability
- **Shopping Cart:** Add/remove items, update quantities
- **Checkout:** Form with client & server-side validation
- **Order Confirmation:** Email notification (mock), order summary

### Admin Features
- **Dashboard:** Statistics, recent orders table, low stock alerts
- **Order Management:** View all orders, update order status, email notifications
- **Product Management:** Update prices and stock quantities
- **Secure Login:** Session-based authentication

---

## 🗄️ Database Schema

### Tables

1. **categories**
   - category_id (PK)
   - category_name
   - description

2. **products**
   - product_id (PK)
   - product_name
   - category_id (FK)
   - description
   - price
   - stock_quantity
   - specifications

3. **orders**
   - order_id (PK)
   - customer_name
   - customer_email
   - customer_phone
   - shipping_address
   - total_amount
   - order_status

4. **order_items**
   - item_id (PK)
   - order_id (FK)
   - product_id (FK)
   - quantity
   - price_at_purchase

5. **admin_users**
   - admin_id (PK)
   - username
   - password_hash

---

## 🎨 Design Features

- **Dark Theme:** Modern cyberpunk-inspired design
- **Gradient Accents:** Purple and blue gradients
- **Responsive Layout:** CSS Grid and Flexbox
- **Interactive Elements:** Hover effects, transitions
- **Form Validation:** Real-time client-side feedback

---

## 🔒 Security Features

- **SQL Injection Prevention:** Prepared statements
- **XSS Protection:** htmlspecialchars() on all outputs
- **Input Sanitization:** All user inputs sanitized
- **Session Management:** Secure admin authentication
- **Server-side Validation:** Double validation (client + server)

---

## 📧 Email Functionality

Email notifications are sent for:
- Order confirmation to customer
- Order status updates
- Low stock alerts (can be implemented)

**Note:** Email requires proper SMTP configuration in production.

---

## 🧪 Testing

### Test Scenarios

1. **Customer Flow:**
   - Browse products → Add to cart → Checkout → Receive confirmation

2. **Admin Flow:**
   - Login → View dashboard → Update order status → Manage inventory

3. **Validation Testing:**
   - Try invalid email formats
   - Try incomplete forms
   - Try SQL injection attempts

### Sample Data
The database includes:
- 3 categories (Keyboards, Switches, Keycaps)
- 12 products (4 per category)
- 3 sample orders
- 1 admin user

---

## 🐛 Troubleshooting

### Common Issues

**Problem:** "Connection failed" error
- **Solution:** Check MySQL is running in XAMPP
- Verify database credentials in config.php

**Problem:** 404 Not Found
- **Solution:** Ensure project is in htdocs folder
- Check URL: `http://localhost/keyboard-store/` (include trailing slash)

**Problem:** Cannot add to cart
- **Solution:** Check PHP sessions are enabled
- Verify session_start() is called

**Problem:** Admin login fails
- **Solution:** Username: `admin`, Password: `admin123`
- Check admin_users table exists

**Problem:** Email not sending
- **Solution:** Email requires SMTP configuration (for demo, emails are "sent" but not actually delivered)

---

## 📊 SQL Commands Used

**SELECT:** Display products, orders, categories
```sql
SELECT * FROM products WHERE category_id = 1
```

**INSERT:** Create new orders
```sql
INSERT INTO orders (customer_name, customer_email, ...) VALUES (?, ?, ...)
```

**UPDATE:** Update order status, stock quantity
```sql
UPDATE orders SET order_status = 'Shipped' WHERE order_id = 1
UPDATE products SET stock_quantity = 15 WHERE product_id = 1
```

---

## 🎓 Learning Outcomes

This project demonstrates:
- Full-stack web development
- Database design and normalization
- Form handling and validation
- Session management
- Security best practices
- MVC-like architecture (separation of concerns)
- CRUD operations
- E-commerce workflow

---

## 📝 Additional Version Ideas (15%)

For the additional modern version, consider:

1. **React.js Frontend**
   - Single Page Application (SPA)
   - React Router for navigation
   - Context API for state management

2. **Node.js + Express Backend**
   - RESTful API architecture
   - JWT authentication
   - Express middleware

3. **MongoDB Database**
   - NoSQL document structure
   - Mongoose ODM

4. **Modern Features**
   - Stripe payment integration
   - Real-time order tracking
   - Image uploads with Cloudinary
   - Advanced search with filters

---

## 👨‍💻 Development Notes

### Code Organization
- **config.php:** Database connection and constants
- **functions.php:** Reusable utility functions
- **Separation of Concerns:** Display logic separated from business logic

### Best Practices Followed
- Prepared statements for SQL queries
- Input sanitization and validation
- Consistent naming conventions
- Commented code where necessary
- Error handling with try-catch

---

## 📄 License

This project is created for educational purposes.

---

## 🤝 Credits

**Developers:** Pagdanganan Robert Martin Gosioco & Tan Chuan Bing 
**Course:** IE4727 - Web Application Design  
**Year:** Y25S1  

**Design Inspiration:** ktechs.store

---

## 📞 Support

For issues or questions:
- Check troubleshooting section
- Review code comments
- Test with sample data included

---

**🎉 Project Complete! Ready for submission and demonstration.**