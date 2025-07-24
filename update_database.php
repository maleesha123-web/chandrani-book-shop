<?php
require_once 'includes/db.php';

try {
    // Add cover_image column to books table if it doesn't exist
    $stmt = $pdo->query("SHOW COLUMNS FROM books LIKE 'cover_image'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE books ADD COLUMN cover_image VARCHAR(255) DEFAULT NULL");
        echo "Successfully added cover_image column to books table.\n";
    } else {
        echo "cover_image column already exists in books table.\n";
    }
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
?>
