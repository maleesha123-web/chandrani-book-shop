<?php
session_start();

// If user is already logged in, redirect
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['user_type'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: home.php');
    }
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/db.php';
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($email) || empty($password)) {
        $message = '<div class="message error">All fields are required.</div>';
    } else {
        // Check credentials
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'user_type' => $user['user_type']
            ];
            
            // Redirect based on user type
            if ($user['user_type'] === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: home.php');
            }
            exit();
        } else {
            $message = '<div class="message error">Invalid email or password.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chandrani Book Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Chandrani Book Shop</div>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="form-container">
            <h2>Login</h2>
            
            <?php echo $message; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit">Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
            
            <div style="margin-top: 30px; padding: 20px; background-color: #f0f8ff; border-radius: 5px;">
                <h4>Admin Login:</h4>
                <p><strong>Email:</strong> adminbook@gmail.com</p>
                <p><strong>Password:</strong> adminbook</p>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Chandrani Book Shop</h3>
                <p>Your trusted partner in discovering amazing books and expanding your literary horizons. We've been serving book lovers with quality books at affordable prices.</p>
                <p>📍 123 Book Street, Literature City, LC 12345</p>
                <p>📞 +1 (555) 123-4567</p>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="home.php#about">About Us</a></li>
                    <li><a href="home.php#contact">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Customer Care</h3>
                <ul class="footer-links">
                    <li><a href="feedback.php">Feedback</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="profile.php">My Account</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">📘</a>
                    <a href="#" class="social-link" title="Instagram">📷</a>
                    <a href="#" class="social-link" title="Twitter">🐦</a>
                    <a href="#" class="social-link" title="YouTube">📺</a>
                    <a href="#" class="social-link" title="WhatsApp">💬</a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Chandrani Book Shop. All rights reserved. | Crafted with ❤️ for book lovers</p>
        </div>
    </footer>
</body>
</html>
