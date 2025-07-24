<?php
session_start();
require_once 'includes/db.php';

// Handle search and filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query with search and filter
$query = "SELECT * FROM books WHERE stock > 0";
$params = [];

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category) && $category !== 'all') {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY title";

// Fetch books from database
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
    
    // Get all categories for filter
    $stmt = $pdo->query("SELECT DISTINCT category FROM books WHERE stock > 0 ORDER BY category");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $books = [];
    $categories = [];
    $error = "Error loading books: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Chandrani Book Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Chandrani Book Shop</div>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart <span class="cart-count">0</span></a></li>
                <li><a href="home.php#about">About Us</a></li>
                <li><a href="home.php#contact">Contact</a></li>
                <li><a href="feedback.php">Feedback</a></li>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="#"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Our Book Collection</h1>
        
        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <form method="GET" action="shop.php">
                <div class="search-filter-row">
                    <div class="form-group">
                        <label for="search">Search Books:</label>
                        <input type="search" id="search" name="search" placeholder="Search by title or author..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="form-group">
                        <label for="category">Filter by Category:</label>
                        <select id="category" name="category">
                            <option value="all" <?php echo $category === 'all' || empty($category) ? 'selected' : ''; ?>>All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['category']); ?>" 
                                        <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-primary">Search</button>
                        <button type="button" onclick="clearFilters()" class="btn-warning" style="margin-left: 10px;">Clear</button>
                    </div>
                </div>
            </form>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($books)): ?>
            <div class="message">
                <p>No books available at the moment. Please check back later!</p>
            </div>
        <?php else: ?>
            <div class="books-grid">
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <div class="book-category-badge"><?php echo htmlspecialchars($book['category']); ?></div>
                        
                        <!-- Book Cover Image -->
                        <div class="book-cover">
                            <?php if (!empty($book['cover_image']) && file_exists('images/' . $book['cover_image'])): ?>
                                <img src="images/<?php echo htmlspecialchars($book['cover_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($book['title']); ?>" 
                                     class="book-cover-image">
                            <?php else: ?>
                                <div class="book-cover-placeholder">
                                    <span class="book-cover-text">📚</span>
                                    <span class="book-cover-title"><?php echo htmlspecialchars(substr($book['title'], 0, 30)); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                        <div class="book-author">by <?php echo htmlspecialchars($book['author']); ?></div>
                        <div class="book-price">LKR <?php echo number_format($book['price'], 2); ?></div>
                        <div class="book-stock">
                            <span class="stock-indicator <?php echo $book['stock'] > 10 ? 'in-stock' : ($book['stock'] > 0 ? 'low-stock' : 'out-stock'); ?>">
                                <?php echo $book['stock']; ?> in stock
                            </span>
                        </div>
                        <button 
                            onclick="addToCart(<?php echo $book['id']; ?>, '<?php echo addslashes(htmlspecialchars($book['title'])); ?>', '<?php echo addslashes(htmlspecialchars($book['author'])); ?>', <?php echo $book['price']; ?>)" 
                            class="btn-success add-to-cart-btn">
                            <i>🛒</i> Add to Cart
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Chandrani Book Shop</h3>
                <p>Your trusted partner in discovering amazing books and expanding your literary horizons. We've been serving book lovers with quality books at affordable prices.</p>
                <p>📍 123 Book Street, Literature City, LC 12345</p>
                <p>📞 +1 (555) 123-4567</p>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="home.php#about">About Us</a></li>
                    <li><a href="home.php#contact">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Customer Care</h3>
                <ul class="footer-links">
                    <li><a href="feedback.php">Feedback</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="profile.php">My Account</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">📘</a>
                    <a href="#" class="social-link" title="Instagram">📷</a>
                    <a href="#" class="social-link" title="Twitter">🐦</a>
                    <a href="#" class="social-link" title="YouTube">📺</a>
                    <a href="#" class="social-link" title="WhatsApp">💬</a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Chandrani Book Shop. All rights reserved. | Crafted with ❤️ for book lovers</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script>
        function clearFilters() {
            document.getElementById('search').value = '';
            document.getElementById('category').value = 'all';
            // Reload page without parameters
            window.location.href = 'shop.php';
        }
        
        // Auto-submit form when category changes for better UX
        document.getElementById('category').addEventListener('change', function() {
            document.querySelector('form').submit();
        });
        
        // Enable Enter key search
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>
