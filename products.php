<?php
require_once 'config.php';
require_once 'functions.php';

// Get filter parameters
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'name_asc';

// Get products based on filters
$products = getProducts($category_id, $search, $sort);

// Get all categories for filter dropdown
$categories = getCategories();

// Get cart count for header
$cart_count = getCartCount();

// Get current category name if filtered
$current_category = '';
if ($category_id) {
    foreach ($categories as $cat) {
        if ($cat['category_id'] == $category_id) {
            $current_category = $cat['category_name'];
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - KeyboardHub</title>
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
            <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">
                <?php 
                if ($current_category) {
                    echo htmlspecialchars($current_category);
                } elseif ($search) {
                    echo 'Search Results for "' . htmlspecialchars($search) . '"';
                } else {
                    echo 'All Products';
                }
                ?>
            </h2>

            <!-- Filters Section -->
            <div class="filters">
                <form method="GET" action="products.php">
                    <div class="filter-group">
                        <!-- Category Filter -->
                        <label for="category">Category:</label>
                        <select name="category" id="category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['category_id']; ?>" 
                                    <?php echo ($category_id == $cat['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Search Box -->
                        <label for="search">Search:</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($search ?? ''); ?>">

                        <!-- Sort Options -->
                        <label for="sort">Sort by:</label>
                        <select name="sort" id="sort" onchange="this.form.submit()">
                            <option value="name_asc" <?php echo ($sort == 'name_asc') ? 'selected' : ''; ?>>
                                Name (A-Z)
                            </option>
                            <option value="name_desc" <?php echo ($sort == 'name_desc') ? 'selected' : ''; ?>>
                                Name (Z-A)
                            </option>
                            <option value="price_asc" <?php echo ($sort == 'price_asc') ? 'selected' : ''; ?>>
                                Price (Low to High)
                            </option>
                            <option value="price_desc" <?php echo ($sort == 'price_desc') ? 'selected' : ''; ?>>
                                Price (High to Low)
                            </option>
                        </select>

                        <!-- Submit Button -->
                        <button type="submit" class="btn">Apply Filters</button>
                        
                        <!-- Clear Filters -->
                        <?php if ($category_id || $search || $sort != 'name_asc'): ?>
                            <a href="products.php" class="btn btn-secondary">Clear Filters</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Product Count -->
            <p style="margin-bottom: 1rem; color: #7f8c8d;">
                Found <?php echo count($products); ?> product<?php echo (count($products) != 1) ? 's' : ''; ?>
            </p>

            <!-- Products Grid -->
            <?php if (count($products) > 0): ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">⌨️</div>
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p class="category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            
                            <!-- Short Description -->
                            <p style="font-size: 0.9rem; color: #7f8c8d; margin: 0.5rem 0;">
                                <?php 
                                $desc = htmlspecialchars($product['description']);
                                echo (strlen($desc) > 80) ? substr($desc, 0, 80) . '...' : $desc;
                                ?>
                            </p>
                            
                            <p class="price"><?php echo formatPrice($product['price']); ?></p>
                            
                            <!-- Stock Status -->
                            <p class="stock <?php echo ($product['stock_quantity'] < 10 && $product['stock_quantity'] > 0) ? 'low-stock' : ''; ?>">
                                <?php 
                                if ($product['stock_quantity'] > 0) {
                                    echo $product['stock_quantity'] . ' in stock';
                                    if ($product['stock_quantity'] < 10) {
                                        echo ' - Hurry!';
                                    }
                                } else {
                                    echo 'Out of stock';
                                }
                                ?>
                            </p>
                            
                            <!-- Action Buttons -->
                            <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                                <a href="product-detail.php?id=<?php echo $product['product_id']; ?>" class="btn">
                                    View Details
                                </a>
                                
                                <?php if ($product['stock_quantity'] > 0): ?>
                                    <form method="POST" action="cart.php" style="margin: 0;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-secondary" style="width: 100%;">
                                            🛒 Add to Cart
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn" style="background-color: #95a5a6; cursor: not-allowed;" disabled>
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- No Products Found -->
                <div style="text-align: center; padding: 3rem; background-color: #fff; border-radius: 10px;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🔍</div>
                    <h3 style="color: #2c3e50; margin-bottom: 1rem;">No Products Found</h3>
                    <p style="color: #7f8c8d; margin-bottom: 1.5rem;">
                        <?php if ($search): ?>
                            We couldn't find any products matching "<?php echo htmlspecialchars($search); ?>"
                        <?php else: ?>
                            No products available in this category.
                        <?php endif; ?>
                    </p>
                    <a href="products.php" class="btn">View All Products</a>
                </div>
            <?php endif; ?>

            <!-- Back to Top -->
            <?php if (count($products) > 6): ?>
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="#" class="btn">Back to Top ↑</a>
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
</body>
</html>