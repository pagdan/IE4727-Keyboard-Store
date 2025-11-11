<?php
require_once 'config.php';
require_once 'functions.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get product details
$product = getProductById($product_id);

// Redirect to products page if product not found
if (!$product) {
    header('Location: products.php');
    exit();
}

// Get cart count for header
$cart_count = getCartCount();

// Handle add to cart action
$added_to_cart = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    // Validate quantity
    if ($quantity > 0 && $quantity <= $product['stock_quantity']) {
        addToCart($product_id, $quantity);
        $added_to_cart = true;
        $cart_count = getCartCount(); // Update cart count
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - RobbingKeebs</title>
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
            <!-- Breadcrumb Navigation -->
            <div style="margin-bottom: 1.5rem;">
                <a href="index.php" style="color: #3498db; text-decoration: none;">Home</a> / 
                <a href="products.php" style="color: #3498db; text-decoration: none;">Products</a> / 
                <a href="products.php?category=<?php echo $product['category_id']; ?>" style="color: #3498db; text-decoration: none;">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </a> / 
                <span style="color: #7f8c8d;"><?php echo htmlspecialchars($product['product_name']); ?></span>
            </div>

            <!-- Success Message -->
            <?php if ($added_to_cart): ?>
                <div class="success-message">
                    ✓ Product added to cart successfully! 
                    <a href="cart.php" style="color: #155724; text-decoration: underline; font-weight: bold;">View Cart</a>
                </div>
            <?php endif; ?>

            <!-- Product Detail -->
            <div class="product-detail">
                <div class="product-detail-grid">
                    <!-- Product Image -->
                    <div class="product-detail-image">
                        <img width=600px src="<?php echo htmlspecialchars($product['image_url']); ?>">
                    </div>

                    <!-- Product Information -->
                    <div class="product-detail-info">
                        <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
                        
                        <p class="category" style="font-size: 1.1rem; color: #7f8c8d; margin-bottom: 1rem;">
                            Category: <a href="products.php?category=<?php echo $product['category_id']; ?>" 
                                        style="color: #3498db; text-decoration: none;">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </a>
                        </p>

                        <p class="price"><?php echo formatPrice($product['price']); ?></p>

                        <!-- Stock Status -->
                        <p class="stock <?php echo ($product['stock_quantity'] < 10 && $product['stock_quantity'] > 0) ? 'low-stock' : ''; ?>" 
                           style="font-size: 1.1rem; margin-bottom: 1.5rem;">
                            <?php 
                            if ($product['stock_quantity'] > 0) {
                                echo '✓ ' . $product['stock_quantity'] . ' in stock';
                                if ($product['stock_quantity'] < 10) {
                                    echo ' - Only a few left!';
                                }
                            } else {
                                echo '✗ Out of stock';
                            }
                            ?>
                        </p>

                        <!-- Description -->
                        <div class="description">
                            <h3 style="margin-bottom: 0.5rem;">Description</h3>
                            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        </div>

                        <!-- Specifications -->
                        <?php if (!empty($product['specifications'])): ?>
                            <div class="specifications">
                                <h3>Specifications</h3>
                                <p><?php echo nl2br(htmlspecialchars($product['specifications'])); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Add to Cart Form -->
                        <?php if ($product['stock_quantity'] > 0): ?>
                            <form method="POST" action="" id="add-to-cart-form">
                                <input type="hidden" name="action" value="add">
                                
                                <div class="quantity-selector">
                                    <label for="quantity">Quantity:</label>
                                    <input type="number" 
                                           id="quantity" 
                                           name="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="<?php echo $product['stock_quantity']; ?>"
                                           required>
                                    <span style="color: #7f8c8d; font-size: 0.9rem;">
                                        (Max: <?php echo $product['stock_quantity']; ?>)
                                    </span>
                                </div>

                                <div style="display: flex; gap: 1rem;">
                                    <button type="submit" class="btn" style="flex: 1;">
                                        🛒 Add to Cart
                                    </button>
                                    <a href="products.php" class="btn btn-secondary">
                                        ← Continue Shopping
                                    </a>
                                </div>
                            </form>
                        <?php else: ?>
                            <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                                This product is currently out of stock.
                            </div>
                            <a href="products.php" class="btn">← Back to Products</a>
                        <?php endif; ?>

                        <!-- Additional Info -->
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee;">
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 1.5rem;">🚚</span>
                                    <div>
                                        <strong>Free Shipping</strong>
                                        <p style="font-size: 0.9rem; color: #7f8c8d; margin: 0;">On orders over $100</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 1.5rem;">🛡️</span>
                                    <div>
                                        <strong>Warranty</strong>
                                        <p style="font-size: 0.9rem; color: #7f8c8d; margin: 0;">1 year manufacturer warranty</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 1.5rem;">↩️</span>
                                    <div>
                                        <strong>Easy Returns</strong>
                                        <p style="font-size: 0.9rem; color: #7f8c8d; margin: 0;">30-day return policy</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 1.5rem;">💳</span>
                                    <div>
                                        <strong>Secure Payment</strong>
                                        <p style="font-size: 0.9rem; color: #7f8c8d; margin: 0;">Safe & secure checkout</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products Section -->
            <section style="margin-top: 3rem;">
                <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Related Products</h2>
                <?php
                // Get related products from same category
                $related_products = getProducts($product['category_id']);
                // Remove current product and limit to 4
                $related_products = array_filter($related_products, function($p) use ($product_id) {
                    return $p['product_id'] != $product_id;
                });
                $related_products = array_slice($related_products, 0, 4);
                ?>

                <?php if (count($related_products) > 0): ?>
                    <div class="product-grid">
                        <?php foreach ($related_products as $related): ?>
                            <div class="product-card">
                                <div class="product-image">⌨️</div>
                                <h3><?php echo htmlspecialchars($related['product_name']); ?></h3>
                                <p class="category"><?php echo htmlspecialchars($related['category_name']); ?></p>
                                <p class="price"><?php echo formatPrice($related['price']); ?></p>
                                <p class="stock">
                                    <?php echo $related['stock_quantity'] > 0 ? $related['stock_quantity'] . ' in stock' : 'Out of stock'; ?>
                                </p>
                                <a href="product-detail.php?id=<?php echo $related['product_id']; ?>" class="btn">
                                    View Details
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
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
        // Validate quantity on form submit
        document.getElementById('add-to-cart-form')?.addEventListener('submit', function(e) {
            const quantityInput = document.getElementById('quantity');
            const quantity = parseInt(quantityInput.value);
            const maxQuantity = parseInt(quantityInput.max);
            
            if (quantity < 1) {
                e.preventDefault();
                alert('Quantity must be at least 1');
                quantityInput.value = 1;
            } else if (quantity > maxQuantity) {
                e.preventDefault();
                alert('Quantity cannot exceed available stock (' + maxQuantity + ')');
                quantityInput.value = maxQuantity;
            }
        });
    </script>
</body>
</html>