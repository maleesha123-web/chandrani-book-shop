<?php
try {
    require_once 'includes/db.php';
    
    echo "<h2>Login Debug Tool</h2>";
    
    $email = 'adminbook@gmail.com';
    $password = 'adminbook';
    
    echo "<h3>Testing Login for: $email</h3>";
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✅ User found in database<br>";
        echo "User ID: " . $user['id'] . "<br>";
        echo "Name: " . $user['name'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "User Type: " . $user['user_type'] . "<br>";
        echo "Password Hash: " . $user['password'] . "<br><br>";
        
        // Test password verification
        if (password_verify($password, $user['password'])) {
            echo "✅ Password verification: SUCCESS<br>";
            echo "✅ Login should work correctly<br>";
            
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<strong>✅ Login Test Successful!</strong><br>";
            echo "You should be able to login with:<br>";
            echo "<strong>Email:</strong> adminbook@gmail.com<br>";
            echo "<strong>Password:</strong> adminbook<br>";
            echo "</div>";
            
        } else {
            echo "❌ Password verification: FAILED<br>";
            echo "Password hash in database: " . $user['password'] . "<br>";
            
            // Try to create a new hash and compare
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            echo "New hash for 'adminbook': " . $new_hash . "<br>";
            
            if (password_verify($password, $new_hash)) {
                echo "✅ New hash verification: SUCCESS<br>";
                echo "The issue is with the stored password hash. Please run create_admin.php again.<br>";
            } else {
                echo "❌ New hash verification: FAILED<br>";
                echo "There might be a PHP configuration issue.<br>";
            }
        }
        
    } else {
        echo "❌ User not found in database<br>";
        echo "Please run create_admin.php first<br>";
        
        // Check all users in database
        $stmt = $pdo->query("SELECT id, name, email, user_type FROM users");
        $users = $stmt->fetchAll();
        
        echo "<br><strong>All users in database:</strong><br>";
        if (empty($users)) {
            echo "No users found in database<br>";
        } else {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th></tr>";
            foreach ($users as $u) {
                echo "<tr>";
                echo "<td>" . $u['id'] . "</td>";
                echo "<td>" . $u['name'] . "</td>";
                echo "<td>" . $u['email'] . "</td>";
                echo "<td>" . $u['user_type'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

echo "<br><a href='create_admin.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Create Admin User</a>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login</a>";
?>
