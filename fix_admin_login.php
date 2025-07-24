<?php
require_once 'includes/db.php';

echo "<h2>Admin Login Fix Tool</h2>";

try {
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'adminbook@gmail.com'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p><strong>✅ Admin user found!</strong></p>";
        echo "<p>ID: " . $admin['id'] . "</p>";
        echo "<p>Name: " . $admin['name'] . "</p>";
        echo "<p>Email: " . $admin['email'] . "</p>";
        echo "<p>User Type: " . $admin['user_type'] . "</p>";
        echo "<p>Current Password Hash: " . substr($admin['password'], 0, 20) . "...</p>";
        
        // Test if current password works with "adminbook"
        if (password_verify('adminbook', $admin['password'])) {
            echo "<p><strong>✅ Current password 'adminbook' works correctly!</strong></p>";
            echo "<p><em>Login should work. Check for other issues.</em></p>";
        } else {
            echo "<p><strong>❌ Current password doesn't match 'adminbook'</strong></p>";
            echo "<p><strong>🔧 Fixing password...</strong></p>";
            
            // Update with new hashed password
            $new_password = password_hash('adminbook', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'adminbook@gmail.com'");
            $stmt->execute([$new_password]);
            
            echo "<p><strong>✅ Password updated successfully!</strong></p>";
            echo "<p>New password hash: " . substr($new_password, 0, 20) . "...</p>";
        }
        
    } else {
        echo "<p><strong>❌ Admin user not found! Creating admin user...</strong></p>";
        
        // Create admin user
        $password_hash = password_hash('adminbook', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin', 'adminbook@gmail.com', $password_hash, 'admin']);
        
        echo "<p><strong>✅ Admin user created successfully!</strong></p>";
    }
    
    echo "<hr>";
    echo "<h3>Login Test</h3>";
    echo "<p><strong>Email:</strong> adminbook@gmail.com</p>";
    echo "<p><strong>Password:</strong> adminbook</p>";
    echo "<p><a href='login.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<p><strong>❌ Database Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection in includes/db.php</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h2 { color: #333; }
p { margin: 10px 0; }
strong { color: #007cba; }
hr { margin: 30px 0; }
</style>
