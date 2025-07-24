<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']) ?: 'General';
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    
    if (empty($title) || empty($author) || $price <= 0 || $stock < 0) {
        http_response_code(400);
        echo "Invalid input data";
        exit();
    }
    
    $cover_image = null;
    
    // Handle image upload
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cover_image'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        
        if (!in_array($file['type'], $allowed_types)) {
            http_response_code(400);
            echo "Invalid file type. Only JPG, PNG, and WEBP images are allowed.";
            exit();
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            http_response_code(400);
            echo "File too large. Maximum size is 5MB.";
            exit();
        }
        
        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $cover_image = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($title)) . '_' . uniqid() . '.' . $file_extension;
        
        $upload_path = 'images/' . $cover_image;
        
        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            http_response_code(500);
            echo "Failed to upload image";
            exit();
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, category, price, stock, cover_image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author, $category, $price, $stock, $cover_image]);
        echo "Book added successfully" . ($cover_image ? " with cover image" : "");
    } catch (PDOException $e) {
        // If database insert fails, delete the uploaded image
        if ($cover_image && file_exists('images/' . $cover_image)) {
            unlink('images/' . $cover_image);
        }
        http_response_code(500);
        echo "Error: " . $e->getMessage();
    }
}
?>
