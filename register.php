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
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = '<div class="message error">All fields are required.</div>';
    } elseif ($password !== $confirm_password) {
        $message = '<div class="message error">Passwords do not match.</div>';
    } elseif (strlen($password) < 6) {
        $message = '<div class="message error">Password must be at least 6 characters long.</div>';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $message = '<div class="message error">Email already exists.</div>';
        } else {
            // Register user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, 'customer')");
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $message = '<div class="message success">Registration successful! You can now login.</div>';
            } else {
                $message = '<div class="message error">Registration failed. Please try again.</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Chandrani Book Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Chandrani Book Shop</div>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="form-container">
            <h2>Register</h2>
            
            <?php echo $message; ?>
            
            <form method="POST" onsubmit="return validateRegistration()">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit">Register</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
