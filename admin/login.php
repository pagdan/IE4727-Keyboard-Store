<?php
require_once '../config.php';
require_once '../functions.php';

// If already logged in, redirect to dashboard
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password';
    } else {
        // Check credentials against database
        $conn = getDBConnection();
        $username = $conn->real_escape_string($username);
        
        $sql = "SELECT * FROM admin_users WHERE username = '$username' LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $admin['password'])) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                $conn->close();
                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = 'Invalid username or password';
            }
        } else {
            $error_message = 'Invalid username or password';
        }
        
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - RobbingKeebs</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <a href="../index.php"><img src="../images/RBKLogo.png" height=67px width= 67px alt="RobbingKeebsLogo" /></a>
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../products.php">Products</a></li>
                    <li><a href="../cart.php">🛒 Cart</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <div class="form-container" style="max-width: 450px;">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🔐</div>
                    <h2 style="margin-bottom: 0.5rem;">Admin Login</h2>
                    <p style="color: #7f8c8d;">Enter your credentials to access the admin panel</p>
                </div>

                <!-- Error Message -->
                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="login.php" id="login-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               placeholder="Enter your username"
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                               required
                               autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your password"
                               required>
                    </div>

                    <button type="submit" class="btn" style="width: 100%;">
                        🔓 Login
                    </button>

                    <div style="text-align: center; margin-top: 1rem;">
                        <a href="../index.php" style="color: #3498db; text-decoration: none; font-size: 0.9rem;">
                            ← Back to Store
                        </a>
                    </div>
                </form>

                <!-- Demo Credentials (for development only - remove in production) -->
                <div style="background-color: #fff3cd; border: 1px solid #ffc107; padding: 1rem; border-radius: 5px; margin-top: 2rem;">
                    <strong style="color: #856404; display: block; margin-bottom: 0.5rem;">Demo Credentials:</strong>
                    <p style="color: #856404; margin: 0; font-size: 0.9rem;">
                        Username: <code>admin</code><br>
                        Password: <code>admin123</code>
                    </p>
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

    <script src="../js/validation.js"></script>
</body>
</html>