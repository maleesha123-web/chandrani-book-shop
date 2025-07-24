<?php
require_once 'includes/db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_email VARCHAR(255) NOT NULL,
        customer_name VARCHAR(255),
        description TEXT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
    )";
    
    $pdo->exec($sql);
    echo "Feedback table created successfully!";
    
    // Insert some sample approved feedback
    $sampleFeedback = [
        ['customer_email' => 'john@example.com', 'customer_name' => 'John Doe', 'description' => 'Great selection of books and excellent customer service! Will definitely shop here again.', 'rating' => 5, 'status' => 'approved'],
        ['customer_email' => 'jane@example.com', 'customer_name' => 'Jane Smith', 'description' => 'Fast delivery and books were in perfect condition. Highly recommended!', 'rating' => 4, 'status' => 'approved'],
        ['customer_email' => 'mike@example.com', 'customer_name' => 'Mike Johnson', 'description' => 'Amazing bookstore with competitive prices. The website is easy to use.', 'rating' => 5, 'status' => 'approved']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO feedback (customer_email, customer_name, description, rating, status) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($sampleFeedback as $feedback) {
        $stmt->execute([$feedback['customer_email'], $feedback['customer_name'], $feedback['description'], $feedback['rating'], $feedback['status']]);
    }
    
    echo "<br>Sample feedback data inserted successfully!";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
