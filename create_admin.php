<?php
try {
    require_once 'includes/db.php';
    
    echo "<h2>Admin User Setup</h2>";
    
    // First, check if the database connection works
    echo "✅ Database connection successful<br>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Users table does not exist. Please import database.sql first.<br>";
        exit;
    }
    echo "✅ Users table exists<br>";
    
    // Delete existing admin if exists
    $stmt = $pdo->prepare("DELETE FROM users WHERE email = 'adminbook@gmail.com'");
    $deleted = $stmt->execute();
    echo "✅ Deleted any existing admin user<br>";
    
    // Create new admin with properly hashed password
    $hashed_password = password_hash('adminbook', PASSWORD_DEFAULT);
    echo "✅ Password hash generated: " . $hashed_password . "<br>";
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute(['Admin', 'adminbook@gmail.com', $hashed_password, 'admin']);
    
    if ($result) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<strong>✅ Admin user created successfully!</strong><br>";
        echo "<strong>Email:</strong> adminbook@gmail.com<br>";
        echo "<strong>Password:</strong> adminbook<br>";
        echo "</div>";
        
        // Verify the user was created correctly
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'adminbook@gmail.com'");
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ User verification: Found admin user in database<br>";
            echo "User ID: " . $user['id'] . "<br>";
            echo "User Type: " . $user['user_type'] . "<br>";
            
            // Test password verification
            if (password_verify('adminbook', $user['password'])) {
                echo "✅ Password verification test: PASSED<br>";
            } else {
                echo "❌ Password verification test: FAILED<br>";
            }
        } else {
            echo "❌ User verification: Admin user not found in database<br>";
        }
        
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "❌ Failed to create admin user.";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

echo "<br><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a>";
?>
