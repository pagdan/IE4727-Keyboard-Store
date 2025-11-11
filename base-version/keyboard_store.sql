-- Create Database
CREATE DATABASE IF NOT EXISTS keyboard_store;
USE keyboard_store;

-- Table 1: categories
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table 2: products
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    specifications TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Table 3: orders
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    shipping_address TEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table 4: order_items
CREATE TABLE order_items (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Table 5: admin_users
CREATE TABLE admin_users (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample categories
INSERT INTO categories (category_name, description) VALUES
('Keyboards', 'Mechanical keyboards with various switch types and layouts'),
('Switches', 'Individual mechanical switches for custom builds'),
('Keycaps', 'Custom keycap sets in various profiles and materials');

-- Insert sample products
INSERT INTO products (product_name, category_id, description, price, stock_quantity, image_url, specifications) VALUES
-- Keyboards
('GMMK Pro 75% Keyboard', 1, 'Premium gasket-mounted mechanical keyboard with hot-swappable switches and aluminum frame', 199.99, 15, 'images/products/gmmk-pro.jpg', 'Layout: 75%|Switch: Hot-swap|Material: Aluminum|Connectivity: USB-C|RGB: Yes'),
('Keychron Q1 Pro', 1, 'Wireless mechanical keyboard with QMK/VIA support and premium build quality', 189.99, 20, 'images/products/keychron-q1.jpg', 'Layout: 75%|Switch: Hot-swap|Material: Aluminum|Connectivity: Wireless/USB-C|RGB: Yes'),
('Ducky One 3 TKL', 1, 'Tenkeyless mechanical keyboard with excellent build quality and Cherry MX switches', 139.99, 25, 'images/products/ducky-one3.jpg', 'Layout: TKL|Switch: Cherry MX|Material: Plastic|Connectivity: USB-C|RGB: Yes'),
('Tofu65 Custom Kit', 1, 'DIY custom keyboard kit for enthusiasts', 149.99, 10, 'images/products/tofu65.jpg', 'Layout: 65%|Switch: Hot-swap|Material: Aluminum|Connectivity: USB-C|RGB: Optional'),

-- Switches
('Cherry MX Red (Pack of 70)', 2, 'Linear switches with smooth actuation, perfect for gaming and typing', 49.99, 50, 'images/products/mx-red.jpg', 'Type: Linear|Actuation: 45g|Travel: 4mm|Sound: Quiet'),
('Gateron Yellow (Pack of 90)', 2, 'Budget-friendly smooth linear switches popular in custom builds', 24.99, 80, 'images/products/gateron-yellow.jpg', 'Type: Linear|Actuation: 50g|Travel: 4mm|Sound: Quiet'),
('Glorious Panda (Pack of 36)', 2, 'Premium tactile switches with satisfying bump and smooth return', 34.99, 40, 'images/products/glorious-panda.jpg', 'Type: Tactile|Actuation: 67g|Travel: 4mm|Sound: Medium'),
('Kailh Box White (Pack of 90)', 2, 'Clicky switches with crisp feedback and dust-resistant design', 29.99, 60, 'images/products/box-white.jpg', 'Type: Clicky|Actuation: 50g|Travel: 3.6mm|Sound: Loud'),

-- Keycaps
('GMK Striker Keycap Set', 3, 'Premium double-shot ABS keycaps with bold blue and white colorway', 129.99, 12, 'images/products/gmk-striker.jpg', 'Profile: Cherry|Material: ABS|Compatibility: MX|Keys: 139'),
('PBT Islander Keycaps', 3, 'Dye-sublimated PBT keycaps with tropical island theme', 89.99, 18, 'images/products/pbt-islander.jpg', 'Profile: Cherry|Material: PBT|Compatibility: MX|Keys: 150'),
('Drop MT3 Susuwatari', 3, 'High-profile MT3 keycaps with elegant black and grey design', 99.99, 15, 'images/products/mt3-susuwatari.jpg', 'Profile: MT3|Material: PBT|Compatibility: MX|Keys: 125'),
('XDA Canvas Keycaps', 3, 'Uniform profile keycaps with vintage computing aesthetic', 79.99, 20, 'images/products/xda-canvas.jpg', 'Profile: XDA|Material: PBT|Compatibility: MX|Keys: 140');

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password_hash, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@keyboardhub.com');

-- Insert sample orders for testing
INSERT INTO orders (customer_name, customer_email, customer_phone, shipping_address, total_amount, order_status) VALUES
('John Doe', 'john@example.com', '555-0101', '123 Main St, Springfield, IL 62701', 319.97, 'Pending'),
('Jane Smith', 'jane@example.com', '555-0102', '456 Oak Ave, Chicago, IL 60601', 89.99, 'Shipped'),
('Bob Wilson', 'bob@example.com', '555-0103', '789 Pine Rd, Aurora, IL 60505', 249.98, 'Processing');

-- Insert order items for sample orders
INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES
(1, 1, 1, 199.99),
(1, 9, 1, 129.99),
(2, 10, 1, 89.99),
(3, 2, 1, 189.99),
(3, 6, 2, 24.99);