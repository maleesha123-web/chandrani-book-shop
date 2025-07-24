<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

try {
    // Get approved feedback, limit to latest 6 entries
    $stmt = $pdo->prepare("SELECT customer_name, description, rating, created_at FROM feedback WHERE status = 'approved' ORDER BY created_at DESC LIMIT 6");
    $stmt->execute();
    $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($feedback);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>
