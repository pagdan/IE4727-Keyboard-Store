<?php
require_once 'config.php';
require_once 'functions.php';

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Verify order ID is valid and matches session
if ($order_id <= 0 || !isset($_SESSION['last_order_id']) || $_SESSION['last_order_id'] != $order_id) {
    header('Location: index.php');
    exit();
}

// Get order details
$order = getOrderById($order_id);

if (!$order) {
    header('Location: index.php');
    exit();
}

// Calculate order totals
$subtotal = 0;
foreach ($order['items'] as $item) {
    $subtotal += ($item['quantity'] * $item['price_at_purchase']);
}

$shipping = $subtotal >= 100 ? 0 : 10;
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;

// Clear the last_order_id from session to prevent page refresh issues
unset($_SESSION['last_order_id']);

// Get cart count (should be 0 after checkout)
$cart_count = getCartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - KeyboardHub</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <a href="index.php"><img src="images/RBKLogo.png" height=67px width= 67px alt="RobbingKeebsLogo" /></a>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="cart.php" class="cart-icon">
                        🛒 Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a></li>
                    <li><a href="admin/login.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Success Message -->
            <div style="text-align: center; padding: 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; margin-bottom: 2rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">✓</div>
                <h2 style="margin-bottom: 0.5rem;">Order Placed Successfully!</h2>
                <p style="font-size: 1.1rem;">Thank you for your purchase</p>
            </div>

            <!-- Order Information Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <!-- Order Details -->
                <div style="background-color: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 1.5rem; color: #2c3e50;">📋 Order Details</h3>
                    
                    <div style="display: grid; gap: 1rem;">
                        <div>
                            <strong style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Order Number</strong>
                            <span style="font-size: 1.2rem; color: #2c3e50;">#<?php echo $order_id; ?></span>
                        </div>
                        
                        <div>
                            <strong style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Order Date</strong>
                            <span style="color: #2c3e50;"><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></span>
                        </div>
                        
                        <div>
                            <strong style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Order Status</strong>
                            <span style="background-color: #ffeaa7; color: #2d3436; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                                <?php echo htmlspecialchars($order['order_status']); ?>
                            </span>
                        </div>
                        
                        <div>
                            <strong style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Total Amount</strong>
                            <span style="font-size: 1.5rem; color: #27ae60; font-weight: bold;"><?php echo formatPrice($order['total_amount']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div style="background-color: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 1.5rem; color: #2c3e50;">📦 Shipping Information</h3>
                    
                    <div style="display: grid; gap: 1rem;">
                        <div>
                            <strong style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Name</strong>
                            <span style="color: #2c3e50;"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                        </div>
                        
                        <div>
                            <strong style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Email</strong>
                            <span style="color: #2c3e50;"><?php echo htmlspecialchars($order['customer_email']); ?></span>
                        </div>
                        
                        <div>
                            <strong style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Phone</strong>
                            <span style="color: #2c3e50;"><?php echo htmlspecialchars($order['customer_phone']); ?></span>
                        </div>
                        
                        <div>
                            <strong style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Shipping Address</strong>
                            <span style="color: #2c3e50;"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div style="background-color: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: #2c3e50;">🛍️ Order Items</h3>
                
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                </td>
                                <td><?php echo formatPrice($item['price_at_purchase']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><strong><?php echo formatPrice($item['quantity'] * $item['price_at_purchase']); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="border-top: 2px solid #2c3e50;">
                            <td colspan="3" style="text-align: right; padding-top: 1rem;"><strong>Subtotal:</strong></td>
                            <td style="padding-top: 1rem;"><strong><?php echo formatPrice($subtotal); ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: right;">Shipping:</td>
                            <td style="color: <?php echo $shipping == 0 ? '#27ae60' : '#2c3e50'; ?>;">
                                <strong><?php echo $shipping == 0 ? 'FREE' : formatPrice($shipping); ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: right;">Tax (8%):</td>
                            <td><strong><?php echo formatPrice($tax); ?></strong></td>
                        </tr>
                        <tr style="background-color: #f8f9fa;">
                            <td colspan="3" style="text-align: right; font-size: 1.2rem;"><strong>Total:</strong></td>
                            <td style="font-size: 1.2rem; color: #27ae60;"><strong><?php echo formatPrice($total); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- What's Next Section -->
            <div style="background-color: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: #2c3e50;">📬 What Happens Next?</h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <div style="font-size: 2rem;">📧</div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem;">Confirmation Email</h4>
                            <p style="color: #7f8c8d; font-size: 0.9rem; margin: 0;">
                                You'll receive an order confirmation email at <strong><?php echo htmlspecialchars($order['customer_email']); ?></strong>
                            </p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <div style="font-size: 2rem;">📦</div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem;">Processing</h4>
                            <p style="color: #7f8c8d; font-size: 0.9rem; margin: 0;">
                                We'll process your order within 1-2 business days
                            </p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <div style="font-size: 2rem;">🚚</div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem;">Shipping</h4>
                            <p style="color: #7f8c8d; font-size: 0.9rem; margin: 0;">
                                You'll receive a tracking number once shipped (3-5 business days)
                            </p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <div style="font-size: 2rem;">📞</div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem;">Support</h4>
                            <p style="color: #7f8c8d; font-size: 0.9rem; margin: 0;">
                                Contact us at support@keyboardhub.com or 1-800-KEYBOARD
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
                <a href="index.php" class="btn">
                    🏠 Return to Home
                </a>
                <a href="products.php" class="btn btn-secondary">
                    🛍️ Continue Shopping
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    🖨️ Print Order
                </button>
            </div>

            <!-- Additional Info -->
            <div style="background-color: #e8f5e9; padding: 1.5rem; border-radius: 10px; border-left: 4px solid #27ae60; margin-bottom: 2rem;">
                <strong style="color: #27ae60; display: block; margin-bottom: 0.5rem;">💡 Pro Tip</strong>
                <p style="color: #2c3e50; margin: 0;">
                    Save your order number <strong>#<?php echo $order_id; ?></strong> for future reference. 
                    You can use it to track your shipment or contact customer support.
                </p>
            </div>

            <!-- Customer Testimonial / Trust Badge -->
            <div style="text-align: center; padding: 2rem; background-color: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⭐⭐⭐⭐⭐</div>
                <p style="font-style: italic; color: #7f8c8d; margin-bottom: 1rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                    "KeyboardHub has the best selection of mechanical keyboards. Fast shipping and excellent customer service!"
                </p>
                <p style="font-weight: bold; color: #2c3e50;">- Happy Customer</p>
                <div style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
                    <div>
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🛡️</div>
                        <strong style="display: block; font-size: 0.9rem;">Secure</strong>
                        <span style="color: #7f8c8d; font-size: 0.8rem;">SSL Encrypted</span>
                    </div>
                    <div>
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">✓</div>
                        <strong style="display: block; font-size: 0.9rem;">Verified</strong>
                        <span style="color: #7f8c8d; font-size: 0.8rem;">100% Authentic</span>
                    </div>
                    <div>
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚡</div>
                        <strong style="display: block; font-size: 0.9rem;">Fast</strong>
                        <span style="color: #7f8c8d; font-size: 0.8rem;">3-5 Day Shipping</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 RobbingKeebs. All rights reserved.</p>
            <p>Your destination for premium mechanical keyboards</p>
        </div>
    </footer>

    <script src="js/validation.js"></script>
    <script>
        // Print-specific styling
        window.addEventListener('beforeprint', function() {
            document.querySelector('header').style.display = 'none';
            document.querySelector('footer').style.display = 'none';
        });
        
        window.addEventListener('afterprint', function() {
            document.querySelector('header').style.display = 'block';
            document.querySelector('footer').style.display = 'block';
        });
    </script>
</body>
</html>