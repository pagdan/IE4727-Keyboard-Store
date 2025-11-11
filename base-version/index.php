<?php
require_once 'config.php';
require_once 'functions.php';

// Get featured products (first 6 products)
$featured_products = getProducts(null, null, 'name_asc');
$featured_products = array_slice($featured_products, 0, 6);

// Get all categories
$categories = getCategories();

// Get cart count for header
$cart_count = getCartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RobbingKeebs - Premium Mechanical Keyboards</title>
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
            <!-- Hero Section -->
            <section class="hero">
                <h2>Welcome to RobbingKeebs!</h2>
                <p>Discover premium mechanical keyboards for the ultimate typing experience!</p>
                <a href="products.php" class="btn">Shop Now</a>
            </section>

            <!-- Categories Section -->
            <section>
                <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Shop by Category</h2>
                <div class="product-grid">
                    <?php foreach ($categories as $category): ?>
                        <div class="product-card" style="cursor: pointer;" onclick="window.location.href='products.php?category=<?php echo $category['category_id']; ?>'">
                            <div class="product-image">
                                <?php 
                                // Display different emojis based on category
                                $emoji = '⌨️';
                                if (stripos($category['category_name'], 'Full') !== false) {
                                    $emoji = '🖥️';
                                } elseif (stripos($category['category_name'], 'TKL') !== false) {
                                    $emoji = '⌨️';
                                } elseif (stripos($category['category_name'], '60%') !== false || stripos($category['category_name'], '65%') !== false) {
                                    $emoji = '📱';
                                } elseif (stripos($category['category_name'], 'Switch') !== false) {
                                    $emoji = '🔘';
                                } elseif (stripos($category['category_name'], 'Keycap') !== false) {
                                    $emoji = '🎨';
                                }
                                echo $emoji;
                                ?>
                            </div>
                            <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                            <p class="category"><?php echo htmlspecialchars($category['description']); ?></p>
                            <a href="products.php?category=<?php echo $category['category_id']; ?>" class="btn">Browse</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Featured Products Section -->
            <section style="margin-top: 3rem;">
                <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Featured Products</h2>
                <div class="product-grid">
                    <?php foreach ($featured_products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">⌨️</div>
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p class="category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <p class="price"><?php echo formatPrice($product['price']); ?></p>
                            <p class="stock <?php echo ($product['stock_quantity'] < 10) ? 'low-stock' : ''; ?>">
                                <?php 
                                if ($product['stock_quantity'] > 0) {
                                    echo $product['stock_quantity'] . ' in stock';
                                } else {
                                    echo 'Out of stock';
                                }
                                ?>
                            </p>
                            <a href="product-detail.php?id=<?php echo $product['product_id']; ?>" class="btn">View Details</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Why Choose Us Section -->
            <section style="margin-top: 3rem;">
                <h2 style="margin-bottom: 1.5rem; color: #2c3e50; text-align: center;">Why Choose RobbingKeebs?</h2>
                <div class="product-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                    <div class="product-card">
                        <div class="product-image">🚚</div>
                        <h3>Fast Shipping</h3>
                        <p>Get your keyboard delivered within 3-5 business days</p>
                    </div>
                    <div class="product-card">
                        <div class="product-image">✅</div>
                        <h3>Quality Guaranteed</h3>
                        <p>All products tested and verified for performance</p>
                    </div>
                    <div class="product-card">
                        <div class="product-image">💰</div>
                        <h3>Best Prices</h3>
                        <p>Competitive pricing on all premium keyboards</p>
                    </div>
                    <div class="product-card">
                        <div class="product-image">🛡️</div>
                        <h3>Warranty Support</h3>
                        <p>Comprehensive warranty on all mechanical keyboards</p>
                    </div>
                </div>
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
</body>
</html>