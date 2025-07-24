<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Chandrani Book Shop</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    </style>
</head>
<body>
    <h1>Chandrani Book Shop - Database Setup</h1>
    
    <?php
    try {
        require_once 'includes/db.php';
        echo "<div class='success'>✅ Database connection successful</div>";
        
        // Read and execute the complete database.sql file
        $sql_content = file_get_contents('database.sql');
        
        // Remove the database creation lines since we're already connected
        $sql_content = preg_replace('/CREATE DATABASE.*?;/i', '', $sql_content);
        $sql_content = preg_replace('/USE .*?;/i', '', $sql_content);
        
        // Split into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql_content)));
        
        $tables_created = 0;
        $data_inserted = 0;
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            try {
                $pdo->exec($statement);
                
                if (stripos($statement, 'CREATE TABLE') !== false) {
                    $tables_created++;
                    // Extract table name
                    preg_match('/CREATE TABLE.*?`?([a-zA-Z_]+)`?/i', $statement, $matches);
                    $table_name = isset($matches[1]) ? $matches[1] : 'unknown';
                    echo "<div class='success'>✅ Table '{$table_name}' created successfully</div>";
                } elseif (stripos($statement, 'INSERT INTO') !== false) {
                    $data_inserted++;
                }
            } catch (PDOException $e) {
                // Ignore duplicate key errors for sample data
                if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                    echo "<div class='error'>❌ Error executing statement: " . $e->getMessage() . "</div>";
                }
            }
        }
        
        echo "<div class='success'>✅ Database setup completed! Created {$tables_created} tables and inserted sample data.</div>";
        
        // Insert sample approved feedback if feedback table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'feedback'");
        if ($stmt->rowCount() > 0) {
            $sampleFeedback = [
                ['customer_email' => 'john@example.com', 'customer_name' => 'John Doe', 'description' => 'Great selection of books and excellent customer service! Will definitely shop here again.', 'rating' => 5, 'status' => 'approved'],
                ['customer_email' => 'jane@example.com', 'customer_name' => 'Jane Smith', 'description' => 'Fast delivery and books were in perfect condition. Highly recommended!', 'rating' => 4, 'status' => 'approved'],
                ['customer_email' => 'mike@example.com', 'customer_name' => 'Mike Johnson', 'description' => 'Amazing bookstore with competitive prices. The website is easy to use.', 'rating' => 5, 'status' => 'approved']
            ];
            
            // Check if sample feedback already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE customer_email = ?");
        $stmt->execute(['john@example.com']);
        $exists = $stmt->fetchColumn();
        
        if ($exists == 0) {
            $stmt = $pdo->prepare("INSERT INTO feedback (customer_email, customer_name, description, rating, status) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($sampleFeedback as $feedback) {
                $stmt->execute([$feedback['customer_email'], $feedback['customer_name'], $feedback['description'], $feedback['rating'], $feedback['status']]);
            }
            echo "<div class='success'>✅ Sample feedback data inserted successfully</div>";
            } else {
                echo "<div class='info'>ℹ️ Sample feedback data already exists</div>";
            }
        }
        
        // Create/Update Admin User
        echo "<h2>Admin User Setup</h2>";
        
        // Check if users table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() == 0) {
            echo "<div class='error'>❌ Users table does not exist. Database setup may have failed.</div>";
        } else {
            echo "<div class='success'>✅ Users table exists</div>";
            
            // Delete existing admin if exists to avoid duplicates
            $stmt = $pdo->prepare("DELETE FROM users WHERE email = 'adminbook@gmail.com'");
            $stmt->execute();
            
            // Create new admin with properly hashed password
            $hashed_password = password_hash('adminbook', PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute(['Admin', 'adminbook@gmail.com', $hashed_password, 'admin']);
            
            if ($result) {
                echo "<div class='success'>";
                echo "<strong>✅ Admin user created successfully!</strong><br>";
                echo "<strong>Email:</strong> adminbook@gmail.com<br>";
                echo "<strong>Password:</strong> adminbook<br>";
                echo "</div>";
                
                // Verify the user was created correctly
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'adminbook@gmail.com'");
                $stmt->execute();
                $user = $stmt->fetch();
                
                if ($user && password_verify('adminbook', $user['password'])) {
                    echo "<div class='success'>✅ Admin user verification: PASSED</div>";
                } else {
                    echo "<div class='error'>❌ Admin user verification: FAILED</div>";
                }
            } else {
                echo "<div class='error'>❌ Failed to create admin user</div>";
            }
        }
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Database Error: " . $e->getMessage() . "</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    }
    ?>
    
    <h2>Next Steps</h2>
    <div class='info'>
        <p>If everything was set up successfully, you can now:</p>
        <ul>
            <li>Try logging in as admin using the credentials above</li>
            <li>Submit feedback to test the feedback system</li>
            <li>Check that the homepage displays approved feedback</li>
        </ul>
    </div>
    
    <a href="login.php" class="btn">Go to Login Page</a>
    <a href="home.php" class="btn">Go to Homepage</a>
    <a href="feedback.php" class="btn">Test Feedback Form</a>
    
</body>
</html>
