<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/db.php';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email)) {
        $message = '<div class="message error">Name and email are required.</div>';
    } else {
        try {
            // Check if email is already used by another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user']['id']]);
            
            if ($stmt->fetch()) {
                $message = '<div class="message error">Email is already in use by another account.</div>';
            } else {
                // Update basic info
                if (!empty($new_password)) {
                    // Verify current password first
                    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user']['id']]);
                    $user_data = $stmt->fetch();
                    
                    if (!password_verify($current_password, $user_data['password'])) {
                        $message = '<div class="message error">Current password is incorrect.</div>';
                    } elseif ($new_password !== $confirm_password) {
                        $message = '<div class="message error">New passwords do not match.</div>';
                    } elseif (strlen($new_password) < 6) {
                        $message = '<div class="message error">Password must be at least 6 characters long.</div>';
                    } else {
                        // Update with new password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                        $stmt->execute([$name, $email, $hashed_password, $_SESSION['user']['id']]);
                        
                        $_SESSION['user']['name'] = $name;
                        $_SESSION['user']['email'] = $email;
                        $message = '<div class="message success">Profile updated successfully with new password!</div>';
                    }
                } else {
                    // Update without password change
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $_SESSION['user']['id']]);
                    
                    $_SESSION['user']['name'] = $name;
                    $_SESSION['user']['email'] = $email;
                    $message = '<div class="message success">Profile updated successfully!</div>';
                }
            }
        } catch (PDOException $e) {
            $message = '<div class="message error">Update failed: ' . $e->getMessage() . '</div>';
        }
    }
}

// Get user's saved payment details
try {
    $stmt = $pdo->prepare("SELECT * FROM payment_details WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user']['id']]);
    $saved_payments = $stmt->fetchAll();
} catch (PDOException $e) {
    $saved_payments = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Chandrani Book Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Chandrani Book Shop</div>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart <span class="cart-count">0</span></a></li>
                <li><a href="home.php#about">About Us</a></li>
                <li><a href="home.php#contact">Contact</a></li>
                <li><a href="feedback.php">Feedback</a></li>
                <li><a href="profile.php"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="profile-container">
            <h1>My Profile</h1>
            
            <?php echo $message; ?>

            <div class="profile-sections">
                <div class="profile-section">
                    <h2>Personal Information</h2>
                    <form method="POST" class="profile-form">
                        <div class="profile-avatar">
                            <div class="avatar-placeholder">
                                <?php echo strtoupper(substr($_SESSION['user']['name'], 0, 2)); ?>
                            </div>
                            <h3><?php echo htmlspecialchars($_SESSION['user']['name']); ?></h3>
                        </div>

                        <div class="form-group">
                            <label for="name">Full Name:</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>" required>
                        </div>
                        
                        <h3 style="margin-top: 30px; margin-bottom: 15px;">Change Password (Optional)</h3>
                        
                        <div class="form-group">
                            <label for="current_password">Current Password:</label>
                            <input type="password" id="current_password" name="current_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <button type="submit" class="btn-primary">Update Profile</button>
                    </form>
                </div>

                <div class="profile-section">
                    <h2>Saved Payment Methods</h2>
                    <?php if (empty($saved_payments)): ?>
                        <p>No saved payment methods yet. Save your payment details during checkout for easier future purchases!</p>
                    <?php else: ?>
                        <div class="saved-payments">
                            <?php foreach ($saved_payments as $payment): ?>
                                <div class="payment-card">
                                    <h4><?php echo htmlspecialchars($payment['cardholder_name']); ?> 
                                        <span class="card-type-badge"><?php echo strtoupper($payment['card_type'] ?? 'CARD'); ?></span>
                                    </h4>
                                    <p class="card-number"><?php 
                                        // Mask card number for display (show only last 4 digits)
                                        $masked_number = '**** **** **** ' . substr($payment['card_number'], -4);
                                        echo htmlspecialchars($masked_number); 
                                    ?></p>
                                    <p class="card-expiry">Expires: <?php echo $payment['expiry_month']; ?>/<?php echo $payment['expiry_year']; ?></p>
                                    <p class="card-address"><?php echo htmlspecialchars($payment['billing_address']); ?></p>
                                    <?php if ($payment['is_default']): ?>
                                        <span class="default-badge">Default</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
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
                    <li><a href="cart.php">Cart</a></li>
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

    <script src="js/script.js"></script>
</body>
</html>
