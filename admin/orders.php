<?php
require_once '../config.php';
require_once '../functions.php';

// Require admin authentication
requireAdmin();

$message = '';
$message_type = '';

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $new_status = isset($_POST['order_status']) ? sanitize($_POST['order_status']) : '';
    
    $valid_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    
    if ($order_id > 0 && in_array($new_status, $valid_statuses)) {
        $conn = getDBConnection();
        $status = $conn->real_escape_string($new_status);
        
        $sql = "UPDATE orders SET order_status = '$status', updated_at = CURRENT_TIMESTAMP WHERE order_id = $order_id";
        
        if ($conn->query($sql)) {
            $message = "Order #$order_id status updated to $new_status";
            $message_type = 'success';
        } else {
            $message = "Error updating order status";
            $message_type = 'error';
        }
        
        $conn->close();
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$view_order_id = isset($_GET['view']) ? intval($_GET['view']) : 0;

// Get orders based on filters
$conn = getDBConnection();
$sql = "SELECT * FROM orders WHERE 1=1";

if (!empty($status_filter)) {
    $status_filter = $conn->real_escape_string($status_filter);
    $sql .= " AND order_status = '$status_filter'";
}

if (!empty($search)) {
    $search_term = $conn->real_escape_string($search);
    $sql .= " AND (customer_name LIKE '%$search_term%' OR customer_email LIKE '%$search_term%' OR order_id = '$search_term')";
}

$sql .= " ORDER BY order_date DESC";
$orders_result = $conn->query($sql);

// Get specific order details if viewing
$order_details = null;
if ($view_order_id > 0) {
    $order_details = getOrderById($view_order_id);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - KeyboardHub Admin</title>
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
            <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Orders Management</h2>

            <!-- Message Display -->
            <?php if (!empty($message)): ?>
                <div class="<?php echo $message_type === 'success' ? 'success-message' : 'error-message'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($order_details): ?>
                <!-- Order Detail View -->
                <div style="background-color: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="color: #2c3e50;">Order #<?php echo $order_details['order_id']; ?></h3>
                        <a href="orders.php" class="btn btn-secondary">← Back to All Orders</a>
                    </div>

                    <!-- Order Info Grid -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                        <div>
                            <h4 style="margin-bottom: 1rem;">Customer Information</h4>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($order_details['customer_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order_details['customer_email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order_details['customer_phone']); ?></p>
                            <p><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($order_details['shipping_address'])); ?></p>
                        </div>

                        <div>
                            <h4 style="margin-bottom: 1rem;">Order Information</h4>
                            <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order_details['order_date'])); ?></p>
                            <p><strong>Total Amount:</strong> <span style="color: #27ae60; font-size: 1.2rem; font-weight: bold;"><?php echo formatPrice($order_details['total_amount']); ?></span></p>
                            <p><strong>Current Status:</strong> 
                                <span style="background-color: #ffeaa7; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                                    <?php echo htmlspecialchars($order_details['order_status']); ?>
                                </span>
                            </p>

                            <!-- Update Status Form -->
                            <form method="POST" action="orders.php?view=<?php echo $order_details['order_id']; ?>" style="margin-top: 1.5rem;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="order_id" value="<?php echo $order_details['order_id']; ?>">
                                <label for="order_status" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Update Status:</label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <select name="order_status" id="order_status" style="flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                                        <option value="Pending" <?php echo $order_details['order_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Processing" <?php echo $order_details['order_status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Shipped" <?php echo $order_details['order_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $order_details['order_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $order_details['order_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <h4 style="margin-bottom: 1rem;">Order Items</h4>
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
                            <?php foreach ($order_details['items'] as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo formatPrice($item['price_at_purchase']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><strong><?php echo formatPrice($item['quantity'] * $item['price_at_purchase']); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <!-- Orders List View -->
                
                <!-- Filters -->
                <div class="filters" style="margin-bottom: 2rem;">
                    <form method="GET" action="orders.php">
                        <div class="filter-group">
                            <label for="status">Filter by Status:</label>
                            <select name="status" id="status" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Processing" <?php echo $status_filter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="Shipped" <?php echo $status_filter === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="Delivered" <?php echo $status_filter === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="Cancelled" <?php echo $status_filter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>

                            <label for="search">Search:</label>
                            <input type="text" name="search" id="search" placeholder="Order ID, name, or email" value="<?php echo htmlspecialchars($search); ?>">

                            <button type="submit" class="btn">Apply Filters</button>
                            
                            <?php if ($status_filter || $search): ?>
                                <a href="orders.php" class="btn btn-secondary">Clear Filters</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Orders Table -->
                <div style="background-color: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                        <p style="margin-bottom: 1rem; color: #7f8c8d;">
                            Found <?php echo $orders_result->num_rows; ?> order<?php echo $orders_result->num_rows != 1 ? 's' : ''; ?>
                        </p>

                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $orders_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                                        <td><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
                                        <td>
                                            <span style="background-color: 
                                                <?php 
                                                switch($order['order_status']) {
                                                    case 'Pending': echo '#ffeaa7'; break;
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
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
                            <h3 style="color: #2c3e50; margin-bottom: 1rem;">No Orders Found</h3>
                            <p style="color: #7f8c8d;">
                                <?php if ($status_filter || $search): ?>
                                    No orders match your current filters.
                                <?php else: ?>
                                    No orders have been placed yet.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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