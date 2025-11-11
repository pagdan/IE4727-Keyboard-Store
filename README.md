# RobbingKeebs - E-Commerce Web Application

> A full-featured e-commerce platform for mechanical keyboards, switches, and keycaps. Built with both traditional and modern tech stacks to demonstrate comprehensive full-stack web development skills.

![Project Status](https://img.shields.io/badge/status-complete-success)
![Base Version](https://img.shields.io/badge/base%20version-PHP%2FMySQL-blue)
![MERN Version](https://img.shields.io/badge/MERN%20version-React%2FNode%2FMongoDB-green)

---

## 📋 Project Overview

This project contains **two complete implementations** of the same e-commerce application:

1. **Base Version (85%)** - Traditional Stack (PHP, MySQL, HTML, CSS, JavaScript)
2. **MERN Version (15%)** - Modern Stack (React, Node.js, Express, MongoDB, Tailwind CSS)

Both versions feature identical functionality but demonstrate different architectural approaches and technologies, showcasing versatility in full-stack development.

---

## 🎯 Key Features

### Customer Features
- ✅ Browse products by category (Keyboards, Switches, Keycaps)
- ✅ Advanced search and filtering
- ✅ Detailed product information with specifications
- ✅ Shopping cart with real-time updates
- ✅ Secure checkout with dual-layer validation
- ✅ Order confirmation with email notifications
- ✅ Responsive design for all devices

### Admin Features
- ✅ Secure authentication system
- ✅ Dashboard with real-time statistics
- ✅ Order management with status tracking
- ✅ Inventory management
- ✅ Low stock alerts
- ✅ Product price and stock updates

---

## 📁 Repository Structure
```
keyboard-store/
│
├── base-version/              # Traditional PHP/MySQL Implementation (85%)
│   ├── index.php              # Home page
│   ├── products.php           # Product listing (server-generated)
│   ├── product-detail.php     # Product details
│   ├── cart.php               # Shopping cart
│   ├── checkout.php           # Checkout form (6 fields, dual validation)
│   ├── process-order.php      # Order processing (INSERT/UPDATE)
│   ├── order-confirmation.php # Order success page
│   ├── config.php             # Database configuration
│   ├── functions.php          # Utility functions
│   ├── css/
│   │   └── styles.css         # External stylesheet (20+ styles)
│   ├── js/
│   │   └── validation.js      # Client-side validation
│   ├── admin/
│   │   ├── login.php          # Admin login
│   │   ├── dashboard.php      # Dashboard with orders table
│   │   ├── orders.php         # Order management (UPDATE)
│   │   ├── products.php       # Product management (UPDATE)
│   │   └── logout.php         # Logout handler
│   └── keyboard_store.sql     # Database schema with sample data
│
├── mern-version/              # Modern MERN Stack Implementation (15%)
│   ├── backend/               # Node.js + Express API
│   │   ├── models/
│   │   │   ├── Product.js     # MongoDB Product model
│   │   │   ├── Order.js       # MongoDB Order model
│   │   │   └── Admin.js       # MongoDB Admin model
│   │   ├── routes/
│   │   │   ├── productRoutes.js  # Product API endpoints
│   │   │   ├── orderRoutes.js    # Order API endpoints
│   │   │   └── adminRoutes.js    # Admin API endpoints
│   │   ├── middleware/
│   │   │   └── auth.js        # JWT authentication
│   │   ├── server.js          # Express server
│   │   ├── seed.js            # Database seeder
│   │   └── package.json       # Backend dependencies
│   │
│   └── frontend/              # React + Tailwind UI
│       ├── src/
│       │   ├── components/
│       │   │   ├── Header.jsx
│       │   │   ├── Footer.jsx
│       │   │   ├── ProductCard.jsx
│       │   │   └── ProtectedRoute.jsx
│       │   ├── context/
│       │   │   ├── CartContext.jsx    # Cart state management
│       │   │   └── AuthContext.jsx    # Authentication state
│       │   ├── pages/
│       │   │   ├── Home.jsx
│       │   │   ├── Products.jsx
│       │   │   ├── ProductDetail.jsx
│       │   │   ├── Cart.jsx
│       │   │   ├── Checkout.jsx
│       │   │   ├── OrderConfirmation.jsx
│       │   │   └── admin/
│       │   │       ├── AdminLogin.jsx
│       │   │       ├── AdminDashboard.jsx
│       │   │       ├── AdminOrders.jsx
│       │   │       └── AdminProducts.jsx
│       │   ├── services/
│       │   │   └── api.js     # Axios API client
│       │   ├── App.jsx        # Main app component
│       │   └── index.css      # Tailwind styles
│       └── package.json       # Frontend dependencies
│
├── README.md                  # This file
└── .gitignore                # Git ignore rules
```

---

## 🚀 Quick Start

### Base Version (PHP/MySQL)

**Prerequisites:**
- XAMPP / WAMP / LAMP (PHP 7.4+, MySQL 8.0+)
- Web browser

**Setup:**
```bash
1. Install XAMPP from https://www.apachefriends.org/
2. Copy base-version/ folder to C:\xampp\htdocs\keyboard-store\
3. Start Apache and MySQL in XAMPP Control Panel
4. Open phpMyAdmin: http://localhost/phpmyadmin
5. Create database: keyboard_store
6. Import: base-version/keyboard_store.sql
7. Access: http://localhost/keyboard-store/base-version/
```

**Admin Access:**
- URL: `http://localhost/keyboard-store/base-version/admin/`
- Username: `admin`
- Password: `admin123`

---

### MERN Version (React/Node/MongoDB)

**Prerequisites:**
- Node.js 18+ (https://nodejs.org/)
- MongoDB 6+ (https://www.mongodb.com/try/download/community)
- OR MongoDB Atlas account (cloud database)

**Backend Setup:**
```bash
# Navigate to backend
cd mern-version/backend

# Install dependencies
npm install

# Create .env file (or use existing)
# MongoDB URI, JWT Secret, etc.

# Seed database
npm run seed

# Start backend server
npm run dev
# Backend runs on: http://localhost:5000
```

**Frontend Setup:**
```bash
# Navigate to frontend (new terminal)
cd mern-version/frontend

# Install dependencies
npm install

# Create .env file (or use existing)
# VITE_API_URL=http://localhost:5000/api

# Start development server
npm run dev
# Frontend runs on: http://localhost:3000
```

**MongoDB Setup:**

*Option A - Local MongoDB:*
```bash
# Start MongoDB
mongod
```

*Option B - MongoDB Atlas (Cloud):*
1. Sign up at https://cloud.mongodb.com
2. Create free cluster
3. Get connection string
4. Update backend/.env with connection string

**Admin Access:**
- URL: `http://localhost:3000/admin/login`
- Username: `admin`
- Password: `admin123`

---

## 💻 Technology Stack

### Base Version (Traditional Stack)

| Component | Technology | Purpose |
|-----------|-----------|---------|
| **Frontend** | HTML5, CSS3, Vanilla JavaScript | User interface |
| **Styling** | Custom CSS | Responsive design |
| **Backend** | PHP 7.4+ | Server-side logic |
| **Database** | MySQL 8.0 | Relational data storage |
| **Server** | Apache | Web server |
| **Validation** | JavaScript + PHP | Dual-layer validation |
| **Session** | PHP Sessions | State management |

**Key Features:**
- Server-side rendering
- Direct database queries with prepared statements
- Session-based authentication
- SQL injection prevention
- XSS protection

### MERN Version (Modern Stack)

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| **Frontend** | React | 18.2.0 | UI library |
| **Styling** | Tailwind CSS | 3.3.6 | Utility-first CSS |
| **Routing** | React Router | 6.20.0 | Client-side routing |
| **State** | Context API | - | State management |
| **HTTP Client** | Axios | 1.6.2 | API requests |
| **Backend** | Node.js + Express | 4.18.2 | Server & API |
| **Database** | MongoDB | 6+ | NoSQL database |
| **ODM** | Mongoose | 8.0.0 | MongoDB object modeling |
| **Authentication** | JWT | 9.0.2 | Token-based auth |
| **Validation** | express-validator | 7.0.1 | Server validation |
| **Build Tool** | Vite | 5.0.4 | Fast dev server |
| **UI Icons** | Heroicons | 2.0.18 | React icons |
| **Notifications** | react-hot-toast | 2.4.1 | Toast messages |

**Key Features:**
- Single Page Application (SPA)
- RESTful API architecture
- JWT authentication
- Component-based UI
- Hot module replacement
- Client-side routing

---

## 🗄️ Database Design

### Base Version - MySQL Schema

**5 Tables with Relationships:**
```sql
categories (category_id, category_name, description)
    ↓ (1:N)
products (product_id, product_name, category_id, price, stock_quantity, ...)
    ↓ (1:N)
order_items (item_id, order_id, product_id, quantity, price_at_purchase)
    ↑ (N:1)
orders (order_id, customer_name, customer_email, total_amount, order_status, ...)
    
admin_users (admin_id, username, password_hash, email)
```

**Sample Data:**
- 3 categories
- 12 products (4 per category)
- 3 sample orders
- 1 admin user

### MERN Version - MongoDB Collections

**3 Collections with References:**
```javascript
products {
  _id, name, category, price, stockQuantity,
  specifications: Map, featured: Boolean
}

orders {
  _id, orderNumber, customer: {}, shippingAddress: {},
  items: [{ product: ObjectId, quantity, priceAtPurchase }],
  totalAmount, status
}

admins {
  _id, username, email, password (hashed), role
}
```

---

## 🎓 Academic Requirements Met

### Base Version Requirements (85%)

| Requirement | Implementation | Status |
|-------------|----------------|--------|
| **Pages** | 9 pages (1 home + 8 content) | ✅ |
| **Table** | Orders table in admin dashboard | ✅ |
| **Form** | Checkout form with 6 fields | ✅ |
| **Form Processing** | process-order.php with validation | ✅ |
| **SQL SELECT** | Products, orders display | ✅ |
| **SQL INSERT** | New orders, order items | ✅ |
| **SQL UPDATE** | Order status, product stock | ✅ |
| **Server-generated** | products.php (dynamic listing) | ✅ |
| **JS Validation** | validation.js (client-side) | ✅ |
| **PHP Validation** | Server-side checks | ✅ |
| **External CSS** | styles.css (20+ styles) | ✅ |
| **Security** | Prepared statements, XSS protection | ✅ |

### MERN Version Requirements (15%)

| Requirement | Implementation | Status |
|-------------|----------------|--------|
| **Modern Framework** | React 18 | ✅ |
| **Modern CSS** | Tailwind CSS 3 | ✅ |
| **Backend** | Node.js + Express | ✅ |
| **NoSQL Database** | MongoDB | ✅ |
| **API Architecture** | RESTful endpoints | ✅ |
| **Authentication** | JWT tokens | ✅ |
| **State Management** | Context API | ✅ |
| **Build Tools** | Vite | ✅ |

---

## 🔐 Security Features

### Base Version
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (htmlspecialchars)
- ✅ Input sanitization
- ✅ Session management
- ✅ Server-side validation
- ✅ Password hashing

### MERN Version
- ✅ JWT token authentication
- ✅ Bcrypt password hashing
- ✅ CORS configuration
- ✅ Request validation (express-validator)
- ✅ MongoDB injection prevention
- ✅ Environment variables for secrets

---

## 📡 API Documentation (MERN Version)

### Public Endpoints
```
GET    /api/health                    - Health check
GET    /api/products                  - Get all products (with filters)
GET    /api/products/featured         - Get featured products
GET    /api/products/:id              - Get single product
POST   /api/orders                    - Create new order
GET    /api/orders/:orderNumber       - Get order by number
POST   /api/admin/login               - Admin login
```

### Protected Endpoints (Require JWT)
```
POST   /api/products                  - Create product
PUT    /api/products/:id              - Update product
DELETE /api/products/:id              - Delete product
GET    /api/orders                    - Get all orders (admin)
PUT    /api/orders/:id/status         - Update order status
GET    /api/admin/stats               - Get dashboard statistics
GET    /api/admin/me                  - Get current admin info
```

**Authentication:**
```javascript
headers: {
  'Authorization': 'Bearer <JWT_TOKEN>'
}
```

---

## 🧪 Testing

### Test Credentials

**Customer:**
- Use any email for orders
- Test card numbers if payment integrated

**Admin:**
- Username: `admin`
- Password: `admin123`

### Test Data

**Products:** 12 sample products across 3 categories
- 4 Keyboards (GMMK Pro, Keychron Q1, etc.)
- 4 Switches (Cherry MX, Gateron, etc.)
- 4 Keycaps (GMK Striker, PBT Islander, etc.)

**Orders:** 3 sample orders (base version only)

### Testing Checklist

**Customer Flow:**
- [ ] Browse products
- [ ] Filter by category
- [ ] Search products
- [ ] View product details
- [ ] Add to cart
- [ ] Update cart quantities
- [ ] Proceed to checkout
- [ ] Fill checkout form
- [ ] Submit order
- [ ] View confirmation

**Admin Flow:**
- [ ] Login to admin panel
- [ ] View dashboard statistics
- [ ] View orders table
- [ ] Update order status
- [ ] View order details
- [ ] Manage product inventory
- [ ] Update prices and stock
- [ ] Logout

---

## 📊 Performance Comparison

| Metric | Base Version | MERN Version |
|--------|--------------|--------------|
| **Initial Load** | Fast (server-rendered) | Moderate (SPA bundle) |
| **Navigation** | Slow (full page reload) | Instant (client routing) |
| **Interactivity** | Limited | Rich (React) |
| **API Response** | N/A (direct DB) | Fast (JSON only) |
| **Scalability** | Vertical | Horizontal + Vertical |
| **SEO** | Excellent | Good (with SSR) |

---

## 🐛 Troubleshooting

### Base Version Issues

**Problem:** Database connection failed
- **Solution:** Check MySQL is running in XAMPP
- Verify credentials in config.php

**Problem:** Admin login fails
- **Solution:** Check admin_users table exists
- Verify password: admin123

**Problem:** 404 errors
- **Solution:** Ensure .htaccess allows PHP files
- Check file paths are correct

### MERN Version Issues

**Problem:** MongoDB connection error
- **Solution:** Ensure MongoDB is running: `mongod`
- Check MONGODB_URI in backend/.env

**Problem:** Port already in use
- **Solution:** Kill process or change port
```bash
# Windows
netstat -ano | findstr :5000
taskkill /PID <PID> /F

# Mac/Linux
lsof -ti:5000 | xargs kill -9
```

**Problem:** API not responding
- **Solution:** Verify backend is running
- Check VITE_API_URL in frontend/.env
- Check browser console for CORS errors

**Problem:** Tailwind styles not working
- **Solution:** Ensure tailwind.config.js exists
- Verify @tailwind imports in index.css
- Restart dev server

---

## 🚀 Deployment

### Base Version Deployment

**Hosting Options:**
- Shared hosting (Hostinger, Bluehost, etc.)
- VPS (DigitalOcean, Linode)

**Steps:**
1. Upload files via FTP
2. Import database via phpMyAdmin
3. Update config.php with production credentials
4. Set proper file permissions

**Cost:** $3-10/month

### MERN Version Deployment

**Frontend:** Vercel / Netlify (Free tier)
```bash
npm run build
# Deploy dist/ folder
```

**Backend:** Railway / Render (Free tier)
```bash
# Set environment variables
# Deploy from GitHub
```

**Database:** MongoDB Atlas (Free tier)
- Already cloud-hosted
- 512MB free storage

**Cost:** Free - $15/month

---

## 📚 Learning Outcomes

This project demonstrates:
- ✅ Full-stack web development
- ✅ Database design and implementation
- ✅ RESTful API architecture
- ✅ Authentication and authorization
- ✅ Form validation (client & server)
- ✅ E-commerce workflow
- ✅ Security best practices
- ✅ Modern vs traditional approaches
- ✅ State management
- ✅ Component-based architecture
- ✅ Version control with Git

---

## 👨‍💻 Developers

**Name:** Robert & Bing 
**Course:** IE4717 - Web Application Design  
**Institution:** Nanyang Technological University  
**Year:** 2025  

---

## 📄 License

This project is developed for educational purposes as part of IE4717 coursework.

---

## 🙏 Acknowledgments

- Inspiration: ktechs.store
- Icons: Heroicons
- Fonts: Google Fonts (Inter)
- Styling: Tailwind CSS
- Framework: React.js
- Runtime: Node.js

---

## 📞 Contact & Support

For questions about this project:
- 📧 Email: rmgp2001@gmail.com
- 🐱 GitHub: https://github.com/pagdan/IE4727-Keyboard-Store

---

## ⭐ Project Status

**Status:** ✅ Complete and Ready for Submission

**Completion Date:** November 2025

**Grade Distribution:**
- Base Version: 85% ✅
- MERN Version: 15% ✅
- **Total:** 100% ✅

---

<div align="center">

### 🎉 Thank you for reviewing this project! 🎉

**Built with ❤️ using PHP, React, Node.js, and MongoDB**

</div>