<?php
require_once 'config.php';
require_once 'functions.php';

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit();
}

// Check if cart is empty
$cart_items = getCartItems();
if (empty($cart_items)) {
    $_SESSION['error_message'] = 'Your cart is empty. Please add items before checking out.';
    header('Location: cart.php');
    exit();
}

// Initialize error array
$errors = [];

// Validate and sanitize input data
$customer_name = isset($_POST['customer_name']) ? sanitize($_POST['customer_name']) : '';
$customer_email = isset($_POST['customer_email']) ? sanitize($_POST['customer_email']) : '';
$customer_phone = isset($_POST['customer_phone']) ? sanitize($_POST['customer_phone']) : '';
$street_address = isset($_POST['shipping_address']) ? sanitize($_POST['shipping_address']) : '';
$city = isset($_POST['city']) ? sanitize($_POST['city']) : '';
$state = isset($_POST['state']) ? sanitize($_POST['state']) : '';
$postal_code = isset($_POST['postal_code']) ? sanitize($_POST['postal_code']) : '';
$special_instructions = isset($_POST['special_instructions']) ? sanitize($_POST['special_instructions']) : '';
$terms = isset($_POST['terms']) ? true : false;

// Server-side validation
if (empty($customer_name) || strlen($customer_name) < 2) {
    $errors[] = 'Full name must be at least 2 characters long';
}

if (empty($customer_email) || !isValidEmail($customer_email)) {
    $errors[] = 'Please provide a valid email address';
}

if (empty($customer_phone) || !isValidPhone($customer_phone)) {
    $errors[] = 'Please provide a valid phone number (8-20 digits)';
}

if (empty($street_address) || strlen($street_address) < 10) {
    $errors[] = 'Please provide a complete street address (at least 10 characters)';
}

if (empty($city) || strlen($city) < 2) {
    $errors[] = 'Please provide a valid city name';
}

if (empty($state) || strlen($state) < 2) {
    $errors[] = 'Please provide a valid state/province';
}

if (empty($postal_code) || strlen($postal_code) < 3) {
    $errors[] = 'Please provide a valid postal code';
}

if (!$terms) {
    $errors[] = 'You must agree to the terms and conditions';
}

// Validate stock availability for all cart items
foreach ($cart_items as $item) {
    $product = getProductById($item['product_id']);
    
    if (!$product) {
        $errors[] = 'Product "' . htmlspecialchars($item['product_name']) . '" is no longer available';
    } elseif ($product['stock_quantity'] < $item['quantity']) {
        $errors[] = 'Insufficient stock for "' . htmlspecialchars($item['product_name']) . '". Only ' . $product['stock_quantity'] . ' available';
    }
}

// If there are validation errors, redirect back to checkout
if (!empty($errors)) {
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['form_data'] = $_POST; // Preserve form data
    header('Location: checkout.php');
    exit();
}

// Build complete shipping address
$full_address = $street_address . ', ' . $city . ', ' . $state . ' ' . $postal_code;
if (!empty($special_instructions)) {
    $full_address .= "\n\nSpecial Instructions: " . $special_instructions;
}

// Prepare customer data for order creation
$customer_data = [
    'name' => $customer_name,
    'email' => $customer_email,
    'phone' => $customer_phone,
    'address' => $full_address
];

// Create the order
$order_id = createOrder($customer_data, $cart_items);

// Check if order was created successfully
if ($order_id === false) {
    $_SESSION['error_message'] = 'There was an error processing your order. Please try again or contact support.';
    header('Location: checkout.php');
    exit();
}

// Order created successfully
// Clear the cart
clearCart();

// Store order ID in session for confirmation page
$_SESSION['last_order_id'] = $order_id;

// Send order confirmation email (mock - in production, use proper email service)
$email_subject = 'Order Confirmation - Order #' . $order_id;
$email_body = buildOrderConfirmationEmail($order_id, $customer_data, $cart_items);
// mock_send_email($customer_email, $email_subject, $email_body);

// Redirect to order confirmation page
header('Location: order-confirmation.php?order_id=' . $order_id);
exit();

/**
 * Build email body for order confirmation (mock function)
 */
function buildOrderConfirmationEmail($order_id, $customer_data, $cart_items) {
    $email = "Dear " . htmlspecialchars($customer_data['name']) . ",\n\n";
    $email .= "Thank you for your order at KeyboardHub!\n\n";
    $email .= "Order Number: #" . $order_id . "\n";
    $email .= "Order Date: " . date('F j, Y, g:i a') . "\n\n";
    
    $email .= "SHIPPING INFORMATION:\n";
    $email .= "Name: " . htmlspecialchars($customer_data['name']) . "\n";
    $email .= "Email: " . htmlspecialchars($customer_data['email']) . "\n";
    $email .= "Phone: " . htmlspecialchars($customer_data['phone']) . "\n";
    $email .= "Address: " . htmlspecialchars($customer_data['address']) . "\n\n";
    
    $email .= "ORDER ITEMS:\n";
    $email .= str_repeat('-', 60) . "\n";
    
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $email .= htmlspecialchars($item['product_name']) . "\n";
        $email .= "  Quantity: " . $item['quantity'] . " × " . formatPrice($item['price']) . " = " . formatPrice($item['subtotal']) . "\n";
        $subtotal += $item['subtotal'];
    }
    
    $shipping = $subtotal >= 100 ? 0 : 10;
    $tax = $subtotal * 0.08;
    $total = $subtotal + $shipping + $tax;
    
    $email .= str_repeat('-', 60) . "\n";
    $email .= "Subtotal: " . formatPrice($subtotal) . "\n";
    $email .= "Shipping: " . ($shipping == 0 ? 'FREE' : formatPrice($shipping)) . "\n";
    $email .= "Tax (8%): " . formatPrice($tax) . "\n";
    $email .= "TOTAL: " . formatPrice($total) . "\n\n";
    
    $email .= "Your order will be shipped within 1-2 business days.\n";
    $email .= "You will receive a tracking number once your order has been shipped.\n\n";
    
    $email .= "Thank you for shopping with KeyboardHub!\n\n";
    $email .= "Best regards,\n";
    $email .= "The KeyboardHub Team\n";
    $email .= "support@keyboardhub.com\n";
    $email .= "1-800-KEYBOARD";
    
    return $email;
}

/**
 * Mock email sending function
 * In production, replace with actual email service (PHPMailer, SendGrid, etc.)
 */
function mock_send_email($to, $subject, $body) {
    // Log email instead of sending (for development)
    $log_file = 'order_emails.log';
    $log_entry = "\n" . str_repeat('=', 80) . "\n";
    $log_entry .= "Date: " . date('Y-m-d H:i:s') . "\n";
    $log_entry .= "To: " . $to . "\n";
    $log_entry .= "Subject: " . $subject . "\n";
    $log_entry .= str_repeat('-', 80) . "\n";
    $log_entry .= $body . "\n";
    $log_entry .= str_repeat('=', 80) . "\n";
    
    // Append to log file
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    return true;
}
?>  