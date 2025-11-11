<?php
require_once '../config.php';
require_once '../functions.php';

// Require admin authentication
requireAdmin();

// Get statistics
$conn = getDBConnection();

// Total products
$sql = "SELECT COUNT(*) as total FROM products";
$result = $conn->query($sql);
$total_products = $result->fetch_assoc()['total'];

// Total orders
$sql = "SELECT COUNT(*) as total FROM orders";
$result = $conn->query($sql);
$total_orders = $result->fetch_assoc()['total'];

// Total revenue
$sql = "SELECT SUM(total_amount) as revenue FROM orders WHERE order_status != 'Cancelled'";
$result = $conn->query($sql);
$total_revenue = $result->fetch_assoc()['revenue'] ?? 0;

// Pending orders
$sql = "SELECT COUNT(*) as total FROM orders WHERE order_status = 'Pending'";
$result = $conn->query($sql);
$pending_orders = $result->fetch_assoc()['total'];

// Low stock products (less than 10)
$sql = "SELECT COUNT(*) as total FROM products WHERE stock_quantity < 10";
$result = $conn->query($sql);
$low_stock_count = $result->fetch_assoc()['total'];

// Recent orders (last 10)
$sql = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 10";
$recent_orders = $conn->query($sql);

// Low stock products
$sql = "SELECT * FROM products WHERE stock_quantity < 10 ORDER BY stock_quantity ASC LIMIT 5";
$low_stock_products = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RobbingKeebs</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <h1><a href="dashboard.php">RBK Admin</a></h1>
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="color: #2c3e50;">Admin Dashboard</h2>
                <span style="color: #7f8c8d;">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            </div>

            <!-- Statistics Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <!-- Total Products -->
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📦</div>
                    <h3 style="margin-bottom: 0.5rem; font-size: 2rem;"><?php echo $total_products; ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Total Products</p>
                </div>

                <!-- Total Orders -->
                <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📋</div>
                    <h3 style="margin-bottom: 0.5rem; font-size: 2rem;"><?php echo $total_orders; ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Total Orders</p>
                </div>

                <!-- Total Revenue -->
                <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">💰</div>
                    <h3 style="margin-bottom: 0.5rem; font-size: 2rem;"><?php echo formatPrice($total_revenue); ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Total Revenue</p>
                </div>

                <!-- Pending Orders -->
                <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⏳</div>
                    <h3 style="margin-bottom: 0.5rem; font-size: 2rem;"><?php echo $pending_orders; ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Pending Orders</p>
                </div>

                <!-- Low Stock Alert -->
                <div style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #2c3e50; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⚠️</div>
                    <h3 style="margin-bottom: 0.5rem; font-size: 2rem;"><?php echo $low_stock_count; ?></h3>
                    <p style="margin: 0;">Low Stock Items</p>
                </div>
            </div>

            <!-- Recent Orders -->
            <div style="background-color: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: #2c3e50;">📋 Recent Orders</h3>
                
                <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                                    <td><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
                                    <td>
                                        <span style="background-color: 
                                            <?php 
                                            switch($order['order_status']) {
                                                case 'Pending': echo '#ffeaa7';break;
                                                case 'Processing': echo '#74b9ff'; break;
                                                case 'Shipped': echo '#a29bfe'; break;
                                                case 'Delivered': echo '#55efc4'; break;
                                                case 'Cancelled': echo '#ff7675'; break;
                                                default: echo '#dfe6e9';
                                            }
                                            ?>; 
                                            padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                            <?php echo htmlspecialchars($order['order_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <a href="orders.php?view=<?php echo $order['order_id']; ?>" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <div style="text-align: center; margin-top: 1rem;">
                        <a href="orders.php" class="btn btn-secondary">View All Orders</a>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #7f8c8d; padding: 2rem;">No orders yet</p>
                <?php endif; ?>
            </div>

            <!-- Low Stock Alert -->
            <?php if ($low_stock_products && $low_stock_products->num_rows > 0): ?>
                <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; color: #856404;">⚠️ Low Stock Alert</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Current Stock</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $low_stock_products->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $product['product_id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td>
                                        <strong style="color: <?php echo $product['stock_quantity'] == 0 ? '#e74c3c' : '#f39c12'; ?>;">
                                            <?php echo $product['stock_quantity']; ?>
                                        </strong>
                                    </td>
                                    <td><?php echo formatPrice($product['price']); ?></td>
                                    <td>
                                        <a href="products.php?edit=<?php echo $product['product_id']; ?>" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            Update Stock
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <div style="text-align: center; margin-top: 1rem;">
                        <a href="products.php" class="btn btn-secondary">Manage All Products</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div style="background-color: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 1.5rem; color: #2c3e50;">⚡ Quick Actions</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <a href="orders.php" class="btn" style="text-align: center;">
                        📋 View All Orders
                    </a>
                    <a href="products.php" class="btn" style="text-align: center;">
                        📦 Manage Products
                    </a>
                    <a href="../index.php" target="_blank" class="btn btn-secondary" style="text-align: center;">
                        🛍️ View Store
                    </a>
                    <a href="logout.php" class="btn btn-danger" style="text-align: center;">
                        🚪 Logout
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 RobbingKeebs. All rights reserved.</p>
            <p>Admin Panel</p>
        </div>
    </footer>

    <script src="../js/validation.js"></script>
</body>
</html>