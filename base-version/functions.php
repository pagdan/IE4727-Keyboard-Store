<?php
// Include configuration
require_once 'config.php';

// Sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone number (basic validation)
function isValidPhone($phone) {
    return preg_match('/^[0-9\s\-\+\(\)]{8,20}$/', $phone);
}

// Format price with currency symbol
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Get all categories
function getCategories() {
    $conn = getDBConnection();
    $sql = "SELECT * FROM categories ORDER BY category_name";
    $result = $conn->query($sql);
    
    $categories = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    
    $conn->close();
    return $categories;
}

// Get products by category (optional category filter)
function getProducts($category_id = null, $search = null, $sort = 'name_asc') {
    $conn = getDBConnection();
    
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE 1=1";
    
    // Category filter
    if ($category_id) {
        $sql .= " AND p.category_id = " . intval($category_id);
    }
    
    // Search filter
    if ($search) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (p.product_name LIKE '%$search%' OR p.description LIKE '%$search%')";
    }
    
    // Sorting
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY p.price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY p.price DESC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY p.product_name DESC";
            break;
        default: // name_asc
            $sql .= " ORDER BY p.product_name ASC";
    }
    
    $result = $conn->query($sql);
    
    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    $conn->close();
    return $products;
}

// Get single product by ID
function getProductById($product_id) {
    $conn = getDBConnection();
    $product_id = intval($product_id);
    
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id = $product_id";
    
    $result = $conn->query($sql);
    $product = null;
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
    
    $conn->close();
    return $product;
}

// Shopping Cart Functions
function addToCart($product_id, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function updateCartQuantity($product_id, $quantity) {
    if ($quantity <= 0) {
        removeFromCart($product_id);
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function getCartItems() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }
    
    $conn = getDBConnection();
    $product_ids = array_keys($_SESSION['cart']);
    $ids = implode(',', array_map('intval', $product_ids));
    
    $sql = "SELECT * FROM products WHERE product_id IN ($ids)";
    $result = $conn->query($sql);
    
    $items = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['quantity'] = $_SESSION['cart'][$row['product_id']];
            $row['subtotal'] = $row['price'] * $row['quantity'];
            $items[] = $row;
        }
    }
    
    $conn->close();
    return $items;
}

function getCartTotal() {
    $items = getCartItems();
    $total = 0;
    
    foreach ($items as $item) {
        $total += $item['subtotal'];
    }
    
    return $total;
}

function getCartCount() {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    return array_sum($_SESSION['cart']);
}

function clearCart() {
    $_SESSION['cart'] = [];
}

// Admin authentication
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Create order
function createOrder($customer_data, $cart_items) {
    $conn = getDBConnection();
    
    // Sanitize customer data
    $name = $conn->real_escape_string($customer_data['name']);
    $email = $conn->real_escape_string($customer_data['email']);
    $phone = $conn->real_escape_string($customer_data['phone']);
    $address = $conn->real_escape_string($customer_data['address']);
    $total = getCartTotal();
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert order
        $sql = "INSERT INTO orders (customer_name, customer_email, customer_phone, shipping_address, total_amount, order_status) 
                VALUES ('$name', '$email', '$phone', '$address', $total, 'Pending')";
        
        if (!$conn->query($sql)) {
            throw new Exception("Error creating order");
        }
        
        $order_id = $conn->insert_id;
        
        // Insert order items and update stock
        foreach ($cart_items as $item) {
            $product_id = intval($item['product_id']);
            $quantity = intval($item['quantity']);
            $price = floatval($item['price']);
            
            // Insert order item
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                    VALUES ($order_id, $product_id, $quantity, $price)";
            
            if (!$conn->query($sql)) {
                throw new Exception("Error adding order items");
            }
            
            // Update stock
            $sql = "UPDATE products SET stock_quantity = stock_quantity - $quantity 
                    WHERE product_id = $product_id";
            
            if (!$conn->query($sql)) {
                throw new Exception("Error updating stock");
            }
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return $order_id;
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $conn->close();
        return false;
    }
}

// Get order details
function getOrderById($order_id) {
    $conn = getDBConnection();
    $order_id = intval($order_id);
    
    $sql = "SELECT * FROM orders WHERE order_id = $order_id";
    $result = $conn->query($sql);
    
    $order = null;
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        
        // Get order items
        $sql = "SELECT oi.*, p.product_name 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.product_id 
                WHERE oi.order_id = $order_id";
        
        $items_result = $conn->query($sql);
        $order['items'] = [];
        
        if ($items_result && $items_result->num_rows > 0) {
            while ($item = $items_result->fetch_assoc()) {
                $order['items'][] = $item;
            }
        }
    }
    
    $conn->close();
    return $order;
}
?>