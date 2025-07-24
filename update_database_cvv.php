<?php
// Script to add CVV column to existing payment_details table
require_once 'includes/db.php';

echo "<h2>Database Update: Adding CVV Column</h2>";

try {
    // Check if CVV column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM payment_details LIKE 'cvv'");
    $columnExists = $stmt->rowCount() > 0;
    
    if ($columnExists) {
        echo "<p style='color: green;'>✅ CVV column already exists in payment_details table.</p>";
    } else {
        // Add CVV column
        $pdo->exec("ALTER TABLE payment_details ADD COLUMN cvv VARCHAR(4) DEFAULT NULL");
        echo "<p style='color: green;'>✅ CVV column added to payment_details table.</p>";
        
        // Update existing records with default CVV (for demo purposes)
        $pdo->exec("UPDATE payment_details SET cvv = '123' WHERE cvv IS NULL");
        echo "<p style='color: green;'>✅ Existing records updated with default CVV.</p>";
        
        // Make CVV NOT NULL
        $pdo->exec("ALTER TABLE payment_details MODIFY cvv VARCHAR(4) NOT NULL");
        echo "<p style='color: green;'>✅ CVV column set to NOT NULL.</p>";
    }
    
    // Show current table structure
    echo "<h3>Current payment_details table structure:</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM payment_details");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p style='color: blue; margin-top: 20px;'>✅ Database update completed successfully!</p>";
    echo "<p><a href='checkout.php'>Test the auto-fill functionality</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
