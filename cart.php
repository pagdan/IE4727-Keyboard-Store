<?php
require_once 'config.php';
require_once 'functions.php';

// Handle cart actions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'add':
            // Add item to cart
            $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
            
            if ($product_id > 0 && $quantity > 0) {
                addToCart($product_id, $quantity);
                $message = 'Product added to cart successfully!';
                $message_type = 'success';
            }
            break;
            
        case 'update':
            // Update cart quantities
            if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
                foreach ($_POST['quantities'] as $product_id => $quantity) {
                    $product_id = intval($product_id);
                    $quantity = intval($quantity);
                    updateCartQuantity($product_id, $quantity);
                }
                $message = 'Cart updated successfully!';
                $message_type = 'success';
            }
            break;
            
        case 'remove':
            // Remove item from cart
            $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
            if ($product_id > 0) {
                removeFromCart($product_id);
                $message = 'Product removed from cart.';
                $message_type = 'success';
            }
            break;
            
        case 'clear':
            // Clear entire cart
            clearCart();
            $message = 'Cart cleared successfully.';
            $message_type = 'success';
            break;
    }
    
    // Redirect to avoid form resubmission
    if (!empty($message)) {
        $_SESSION['cart_message'] = $message;
        $_SESSION['cart_message_type'] = $message_type;
        header('Location: cart.php');
        exit();
    }
}

// Get message from session if exists
if (isset($_SESSION['cart_message'])) {
    $message = $_SESSION['cart_message'];
    $message_type = $_SESSION['cart_message_type'];
    unset($_SESSION['cart_message']);
    unset($_SESSION['cart_message_type']);
}

// Get cart items
$cart_items = getCartItems();
$cart_total = getCartTotal();
$cart_count = getCartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - KeyboardHub</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <h1><a href="index.php">⌨️ KeyboardHub</a></h1>
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
            <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Shopping Cart</h2>

            <!-- Message Display -->
            <?php if (!empty($message)): ?>
                <div class="<?php echo $message_type === 'success' ? 'success-message' : 'error-message'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (count($cart_items) > 0): ?>
                <!-- Cart Items Form -->
                <form method="POST" action="cart.php" id="cart-form">
                    <input type="hidden" name="action" value="update">
                    
                    <div class="cart-items">
                        <!-- Cart Header (Desktop) -->
                        <div class="cart-item" style="font-weight: bold; border-bottom: 2px solid #2c3e50;">
                            <div>Image</div>
                            <div>Product</div>
                            <div>Price</div>
                            <div>Quantity</div>
                            <div>Subtotal</div>
                            <div>Action</div>
                        </div>

                        <!-- Cart Items -->
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <!-- Product Image -->
                                <div class="cart-item-image">⌨️</div>

                                <!-- Product Info -->
                                <div>
                                    <h3 style="margin-bottom: 0.5rem;">
                                        <a href="product-detail.php?id=<?php echo $item['product_id']; ?>" 
                                           style="color: #2c3e50; text-decoration: none;">
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                        </a>
                                    </h3>
                                    <p style="color: #7f8c8d; font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($item['description']); ?>
                                    </p>
                                    <p style="color: #7f8c8d; font-size: 0.9rem;">
                                        Stock: <?php echo $item['stock_quantity']; ?> available
                                    </p>
                                </div>

                                <!-- Price -->
                                <div>
                                    <strong><?php echo formatPrice($item['price']); ?></strong>
                                </div>

                                <!-- Quantity -->
                                <div class="cart-item-quantity">
                                    <input type="number" 
                                           name="quantities[<?php echo $item['product_id']; ?>]" 
                                           value="<?php echo $item['quantity']; ?>"
                                           min="1"
                                           max="<?php echo $item['stock_quantity']; ?>"
                                           onchange="this.form.submit()">
                                </div>

                                <!-- Subtotal -->
                                <div>
                                    <strong><?php echo formatPrice($item['subtotal']); ?></strong>
                                </div>

                                <!-- Remove Button -->
                                <div>
                                    <form method="POST" action="cart.php" style="display: inline;" 
                                          onsubmit="return confirm('Remove <?php echo htmlspecialchars($item['product_name']); ?> from cart?');">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem;">
                                            🗑️ Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Cart Actions -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-secondary">
                                🔄 Update Cart
                            </button>
                            <a href="products.php" class="btn btn-secondary">
                                ← Continue Shopping
                            </a>
                        </div>
                        
                        <form method="POST" action="cart.php" style="display: inline;"
                              onsubmit="return confirm('Are you sure you want to clear your entire cart?');">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-danger">
                                🗑️ Clear Cart
                            </button>
                        </form>
                    </div>
                </form>

                <!-- Cart Summary -->
                <div style="display: grid; grid-template-columns: 1fr 400px; gap: 2rem; margin-top: 2rem;">
                    <!-- Shipping Info -->
                    <div style="background-color: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <h3 style="margin-bottom: 1rem;">📦 Shipping Information</h3>
                        <ul style="list-style: none; padding: 0;">
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                                <strong>🚚 Free Shipping:</strong> On orders over $100
                            </li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                                <strong>⏱️ Delivery Time:</strong> 3-5 business days
                            </li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                                <strong>📍 Tracking:</strong> Provided after shipment
                            </li>
                            <li style="padding: 0.5rem 0;">
                                <strong>↩️ Returns:</strong> 30-day return policy
                            </li>
                        </ul>
                    </div>

                    <!-- Cart Summary -->
                    <div class="cart-summary">
                        <h3>Order Summary</h3>
                        
                        <div style="border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Subtotal (<?php echo $cart_count; ?> item<?php echo $cart_count != 1 ? 's' : ''; ?>):</span>
                                <strong><?php echo formatPrice($cart_total); ?></strong>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Shipping:</span>
                                <strong style="color: #27ae60;">
                                    <?php echo $cart_total >= 100 ? 'FREE' : formatPrice(10); ?>
                                </strong>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Tax (estimated):</span>
                                <strong><?php echo formatPrice($cart_total * 0.08); ?></strong>
                            </div>
                        </div>
                        
                        <div class="cart-total" style="border-top: 2px solid #2c3e50; padding-top: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 1.2rem;">Total:</span>
                                <span>
                                    <?php 
                                    $shipping = $cart_total >= 100 ? 0 : 10;
                                    $tax = $cart_total * 0.08;
                                    $total = $cart_total + $shipping + $tax;
                                    echo formatPrice($total); 
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if ($cart_total < 100): ?>
                            <p style="font-size: 0.9rem; color: #7f8c8d; margin-top: 0.5rem;">
                                💡 Add <?php echo formatPrice(100 - $cart_total); ?> more to get FREE shipping!
                            </p>
                        <?php endif; ?>
                        
                        <a href="checkout.php" class="btn" style="width: 100%; margin-top: 1rem; text-align: center; display: block;">
                            Proceed to Checkout →
                        </a>
                        
                        <div style="margin-top: 1rem; text-align: center;">
                            <p style="font-size: 0.9rem; color: #7f8c8d;">
                                🔒 Secure checkout with SSL encryption
                            </p>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Empty Cart Message -->
                <div style="text-align: center; padding: 3rem; background-color: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="font-size: 5rem; margin-bottom: 1rem;">🛒</div>
                    <h3 style="color: #2c3e50; margin-bottom: 1rem;">Your Cart is Empty</h3>
                    <p style="color: #7f8c8d; margin-bottom: 2rem;">
                        Looks like you haven't added any products to your cart yet.
                    </p>
                    <a href="products.php" class="btn">
                        Start Shopping →
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 KeyboardHub. All rights reserved.</p>
            <p>Your destination for premium mechanical keyboards</p>
        </div>
    </footer>

    <script src="js/validation.js"></script>
    <script>
        // Auto-submit form when quantity changes
        document.querySelectorAll('.cart-item-quantity input').forEach(input => {
            input.addEventListener('change', function() {
                const form = document.getElementById('cart-form');
                if (form) {
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>