<?php
require_once '../config.php';
require_once '../functions.php';

// Require admin authentication
requireAdmin();

$message = '';
$message_type = '';

// Handle product update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_product') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $stock = isset($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : 0;
    
    if ($product_id > 0 && $price >= 0 && $stock >= 0) {
        $conn = getDBConnection();
        
        $sql = "UPDATE products SET price = $price, stock_quantity = $stock WHERE product_id = $product_id";
        
        if ($conn->query($sql)) {
            $message = "Product updated successfully!";
            $message_type = 'success';
        } else {
            $message = "Error updating product: " . $conn->error;
            $message_type = 'error';
        }
        
        $conn->close();
    } else {
        $message = "Invalid product data";
        $message_type = 'error';
    }
}

// Get filter parameters
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$edit_product_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;

// Get products based on filters
$products = getProducts($category_filter > 0 ? $category_filter : null, $search);

// Get all categories for filter
$categories = getCategories();

// Get specific product for editing
$edit_product = null;
if ($edit_product_id > 0) {
    $edit_product = getProductById($edit_product_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - KeyboardHub Admin</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <h1><a href="dashboard.php">⌨️ KeyboardHub Admin</a></h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="orders.php">Orders</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="../index.php" target="_blank">View Store</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Products Management</h2>

            <!-- Message Display -->
            <?php if (!empty($message)): ?>
                <div class="<?php echo $message_type === 'success' ? 'success-message' : 'error-message'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($edit_product): ?>
                <!-- Edit Product Form -->
                <div style="background-color: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="color: #2c3e50;">Edit Product: <?php echo htmlspecialchars($edit_product['product_name']); ?></h3>
                        <a href="products.php" class="btn btn-secondary">← Back to All Products</a>
                    </div>

                    <form method="POST" action="products.php?edit=<?php echo $edit_product['product_id']; ?>">
                        <input type="hidden" name="action" value="update_product">
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['product_id']; ?>">

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                            <!-- Product Info (Read-only) -->
                            <div>
                                <h4 style="margin-bottom: 1rem;">Product Information (Read-only)</h4>
                                
                                <div style="margin-bottom: 1rem;">
                                    <strong style="display: block; color: #7f8c8d; margin-bottom: 0.25rem;">Product ID:</strong>
                                    <span>#<?php echo $edit_product['product_id']; ?></span>
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <strong style="display: block; color: #7f8c8d; margin-bottom: 0.25rem;">Product Name:</strong>
                                    <span><?php echo htmlspecialchars($edit_product['product_name']); ?></span>
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <strong style="display: block; color: #7f8c8d; margin-bottom: 0.25rem;">Category:</strong>
                                    <span><?php echo htmlspecialchars($edit_product['category_name']); ?></span>
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <strong style="display: block; color: #7f8c8d; margin-bottom: 0.25rem;">Description:</strong>
                                    <span><?php echo htmlspecialchars($edit_product['description']); ?></span>
                                </div>
                            </div>

                            <!-- Editable Fields -->
                            <div>
                                <h4 style="margin-bottom: 1rem;">Update Price & Stock</h4>

                                <div class="form-group">
                                    <label for="price">Price ($) *</label>
                                    <input type="number" 
                                           id="price" 
                                           name="price" 
                                           step="0.01" 
                                           min="0"
                                           value="<?php echo $edit_product['price']; ?>"
                                           required>
                                    <small style="color: #7f8c8d;">Current: <?php echo formatPrice($edit_product['price']); ?></small>
                                </div>

                                <div class="form-group">
                                    <label for="stock_quantity">Stock Quantity *</label>
                                    <input type="number" 
                                           id="stock_quantity" 
                                           name="stock_quantity" 
                                           min="0"
                                           value="<?php echo $edit_product['stock_quantity']; ?>"
                                           required>
                                    <small style="color: <?php echo $edit_product['stock_quantity'] < 10 ? '#e74c3c' : '#7f8c8d'; ?>;">
                                        Current: <?php echo $edit_product['stock_quantity']; ?> units
                                        <?php if ($edit_product['stock_quantity'] < 10): ?>
                                            - ⚠️ Low Stock!
                                        <?php endif; ?>
                                    </small>
                                </div>

                                <button type="submit" class="btn" style="width: 100%;">
                                    💾 Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            <?php else: ?>
                <!-- Products List View -->
                
                <!-- Filters -->
                <div class="filters" style="margin-bottom: 2rem;">
                    <form method="GET" action="products.php">
                        <div class="filter-group">
                            <label for="category">Filter by Category:</label>
                            <select name="category" id="category" onchange="this.form.submit()">
                                <option value="0">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>" <?php echo $category_filter == $cat['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <label for="search">Search:</label>
                            <input type="text" name="search" id="search" placeholder="Product name..." value="<?php echo htmlspecialchars($search); ?>">

                            <button type="submit" class="btn">Apply Filters</button>
                            
                            <?php if ($category_filter || $search): ?>
                                <a href="products.php" class="btn btn-secondary">Clear Filters</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Products Table -->
                <div style="background-color: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <?php if (count($products) > 0): ?>
                        <p style="margin-bottom: 1rem; color: #7f8c8d;">
                            Found <?php echo count($products); ?> product<?php echo count($products) != 1 ? 's' : ''; ?>
                        </p>

                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><strong>#<?php echo $product['product_id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                        <td><strong><?php echo formatPrice($product['price']); ?></strong></td>
                                        <td>
                                            <strong style="color: <?php 
                                                if ($product['stock_quantity'] == 0) echo '#e74c3c';
                                                elseif ($product['stock_quantity'] < 10) echo '#f39c12';
                                                else echo '#27ae60';
                                            ?>;">
                                                <?php echo $product['stock_quantity']; ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <?php if ($product['stock_quantity'] == 0): ?>
                                                <span style="background-color: #ff7675; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                                    Out of Stock
                                                </span>
                                            <?php elseif ($product['stock_quantity'] < 10): ?>
                                                <span style="background-color: #ffeaa7; color: #2d3436; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                                    Low Stock
                                                </span>
                                            <?php else: ?>
                                                <span style="background-color: #55efc4; color: #2d3436; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                                    In Stock
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="products.php?edit=<?php echo $product['product_id']; ?>" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                                ✏️ Edit
                                            </a>
                                            <a href="../product-detail.php?id=<?php echo $product['product_id']; ?>" target="_blank" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                                👁️ View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">📦</div>
                            <h3 style="color: #2c3e50; margin-bottom: 1rem;">No Products Found</h3>
                            <p style="color: #7f8c8d;">
                                <?php if ($category_filter || $search): ?>
                                    No products match your current filters.
                                <?php else: ?>
                                    No products available in the database.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Stock Summary -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 2rem;">
                <?php
                $total_products = count($products);
                $out_of_stock = 0;
                $low_stock = 0;
                $in_stock = 0;
                
                foreach ($products as $p) {
                    if ($p['stock_quantity'] == 0) $out_of_stock++;
                    elseif ($p['stock_quantity'] < 10) $low_stock++;
                    else $in_stock++;
                }
                ?>
                <div style="background-color: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📦</div>
                    <h4 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><?php echo $total_products; ?></h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem;">Total Products</p>
                </div>

                <div style="background-color: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">✅</div>
                    <h4 style="font-size: 1.5rem; margin-bottom: 0.25rem; color: #27ae60;"><?php echo $in_stock; ?></h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem;">In Stock</p>
                </div>

                <div style="background-color: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚠️</div>
                    <h4 style="font-size: 1.5rem; margin-bottom: 0.25rem; color: #f39c12;"><?php echo $low_stock; ?></h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem;">Low Stock</p>
                </div>

                <div style="background-color: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">❌</div>
                    <h4 style="font-size: 1.5rem; margin-bottom: 0.25rem; color: #e74c3c;"><?php echo $out_of_stock; ?></h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem;">Out of Stock</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 KeyboardHub. All rights reserved.</p>
            <p>Admin Panel</p>
        </div>
    </footer>

    <script src="../js/validation.js"></script>
</body>
</html>