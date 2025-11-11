<?php
require_once 'config.php';
require_once 'functions.php';

// Check if cart is empty - redirect to cart page
$cart_items = getCartItems();
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

$cart_total = getCartTotal();
$cart_count = getCartCount();

// Calculate order totals
$shipping = $cart_total >= 100 ? 0 : 10;
$tax = $cart_total * 0.08;
$total = $cart_total + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - RobbingKeebs</title>
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
            <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Checkout</h2>

            <!-- Checkout Process Steps -->
            <div style="display: flex; justify-content: center; margin-bottom: 2rem; gap: 2rem;">
                <div style="text-align: center;">
                    <div style="width: 40px; height: 40px; background-color: #27ae60; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem;">✓</div>
                    <span style="font-size: 0.9rem; color: #27ae60; font-weight: bold;">Cart</span>
                </div>
                <div style="text-align: center;">
                    <div style="width: 40px; height: 40px; background-color: #3498db; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem;">2</div>
                    <span style="font-size: 0.9rem; color: #3498db; font-weight: bold;">Shipping Info</span>
                </div>
                <div style="text-align: center;">
                    <div style="width: 40px; height: 40px; background-color: #95a5a6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem;">3</div>
                    <span style="font-size: 0.9rem; color: #95a5a6;">Confirmation</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 450px; gap: 2rem;">
                <!-- Checkout Form -->
                <div class="form-container" style="max-width: 100%;">
                    <h3 style="margin-bottom: 1.5rem;">📦 Shipping Information</h3>
                    
                    <form method="POST" action="process-order.php" id="checkout-form">
                        <!-- Customer Name -->
                        <div class="form-group">
                            <label for="customer_name">Full Name *</label>
                            <input type="text" 
                                   id="customer_name" 
                                   name="customer_name" 
                                   placeholder="John Doe"
                                   required>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="customer_email">Email Address *</label>
                            <input type="email" 
                                   id="customer_email" 
                                   name="customer_email" 
                                   placeholder="john.doe@example.com"
                                   oninput="validateEmailRealtime(this)"
                                   required>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="customer_phone">Phone Number *</label>
                            <input type="tel" 
                                   id="customer_phone" 
                                   name="customer_phone" 
                                   placeholder="+1 (555) 123-4567"
                                   oninput="validatePhoneRealtime(this)"
                                   required>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label for="shipping_address">Street Address *</label>
                            <textarea id="shipping_address" 
                                      name="shipping_address" 
                                      rows="3"
                                      placeholder="123 Main Street, Apt 4B"
                                      required></textarea>
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   placeholder="New York"
                                   required>
                        </div>

                        <!-- State and Postal Code -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="state">State/Province *</label>
                                <input type="text" 
                                       id="state" 
                                       name="state" 
                                       placeholder="NY"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="postal_code">Postal Code *</label>
                                <input type="text" 
                                       id="postal_code" 
                                       name="postal_code" 
                                       placeholder="10001"
                                       required>
                            </div>
                        </div>

                        <!-- Special Instructions (Optional) -->
                        <div class="form-group">
                            <label for="special_instructions">Special Instructions (Optional)</label>
                            <textarea id="special_instructions" 
                                      name="special_instructions" 
                                      rows="3"
                                      placeholder="Leave package at front door, ring doorbell, etc."></textarea>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" 
                                       id="terms" 
                                       name="terms" 
                                       required>
                                <span>I agree to the terms and conditions *</span>
                            </label>
                        </div>

                        <!-- Form Buttons -->
                        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                            <button type="submit" class="btn" style="flex: 1;">
                                🔒 Place Order
                            </button>
                            <a href="cart.php" class="btn btn-secondary">
                                ← Back to Cart
                            </a>
                        </div>

                        <p style="font-size: 0.9rem; color: #7f8c8d; margin-top: 1rem; text-align: center;">
                            * Required fields
                        </p>
                    </form>
                </div>

                <!-- Order Summary Sidebar -->
                <div>
                    <!-- Order Summary -->
                    <div class="cart-summary">
                        <h3>Order Summary</h3>
                        
                        <!-- Cart Items -->
                        <div style="max-height: 300px; overflow-y: auto; margin-bottom: 1rem;">
                            <?php foreach ($cart_items as $item): ?>
                                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #eee;">
                                    <div style="flex: 1;">
                                        <strong style="display: block; margin-bottom: 0.25rem;">
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                        </strong>
                                        <span style="font-size: 0.9rem; color: #7f8c8d;">
                                            Qty: <?php echo $item['quantity']; ?> × <?php echo formatPrice($item['price']); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <strong><?php echo formatPrice($item['subtotal']); ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Price Breakdown -->
                        <div style="border-top: 2px solid #eee; padding-top: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Subtotal (<?php echo $cart_count; ?> item<?php echo $cart_count != 1 ? 's' : ''; ?>):</span>
                                <strong><?php echo formatPrice($cart_total); ?></strong>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Shipping:</span>
                                <strong style="color: <?php echo $shipping == 0 ? '#27ae60' : '#2c3e50'; ?>;">
                                    <?php echo $shipping == 0 ? 'FREE' : formatPrice($shipping); ?>
                                </strong>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Tax (8%):</span>
                                <strong><?php echo formatPrice($tax); ?></strong>
                            </div>
                        </div>
                        
                        <!-- Total -->
                        <div class="cart-total" style="border-top: 2px solid #2c3e50; padding-top: 1rem; margin-top: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 1.2rem;">Total:</span>
                                <span><?php echo formatPrice($total); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Security Badge -->
                    <div style="background-color: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-top: 1rem; text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔒</div>
                        <h4 style="margin-bottom: 0.5rem;">Secure Checkout</h4>
                        <p style="font-size: 0.9rem; color: #7f8c8d; margin: 0;">
                            Your payment information is protected with SSL encryption
                        </p>
                    </div>

                    <!-- Payment Methods -->
                    <div style="background-color: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-top: 1rem;">
                        <h4 style="margin-bottom: 1rem;">We Accept</h4>
                        <div style="display: flex; justify-content: space-around; font-size: 2rem;">
                            <span>💳</span>
                            <span>💰</span>
                            <span>📱</span>
                            <span>🏦</span>
                        </div>
                        <p style="font-size: 0.85rem; color: #7f8c8d; text-align: center; margin-top: 0.5rem;">
                            Visa, Mastercard, PayPal, and more
                        </p>
                    </div>

                    <!-- Customer Support -->
                    <div style="background-color: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-top: 1rem;">
                        <h4 style="margin-bottom: 1rem;">Need Help?</h4>
                        <p style="font-size: 0.9rem; color: #7f8c8d;">
                            📞 Call us: 1-800-KEYBOARD<br>
                            📧 Email: support@keyboardhub.com<br>
                            💬 Live chat available 24/7
                        </p>
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
</body>
</html>