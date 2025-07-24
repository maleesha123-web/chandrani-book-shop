<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once 'includes/db.php';

// Get dashboard statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_orders, SUM(total) as total_sales FROM orders");
    $sales_stats = $stmt->fetch();
    
    $stmt = $pdo->query("SELECT COUNT(*) as total_books, SUM(stock) as total_stock FROM books");
    $book_stats = $stmt->fetch();
    
    $stmt = $pdo->query("SELECT COUNT(*) as total_customers FROM users WHERE user_type = 'customer'");
    $customer_stats = $stmt->fetch();
    
    $stmt = $pdo->query("SELECT COUNT(*) as offline_orders FROM offline_orders");
    $offline_stats = $stmt->fetch();
} catch (PDOException $e) {
    $error = "Error loading dashboard: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Chandrani Book Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Chandrani Book Shop - Admin</div>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="sales_report.php">Sales Report</a></li>
                <li><a href="order_book_supplier.php">Order Books</a></li>
                <li><a href="offline_orders.php">Offline Orders</a></li>
                <li><a href="manage_feedback.php">Manage Feedback</a></li>
                <li><a href="#"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="admin-dashboard">
            <div class="dashboard-card">
                <h3>📊 Total Sales</h3>
                <p style="font-size: 2rem; color: #27ae60; font-weight: bold;">
                    LKR <?php echo number_format($sales_stats['total_sales'] ?? 0, 2); ?>
                </p>
                <p><?php echo $sales_stats['total_orders'] ?? 0; ?> orders</p>
            </div>
            
            <div class="dashboard-card">
                <h3>📚 Book Inventory</h3>
                <p style="font-size: 2rem; color: #3498db; font-weight: bold;">
                    <?php echo $book_stats['total_books'] ?? 0; ?>
                </p>
                <p><?php echo $book_stats['total_stock'] ?? 0; ?> items in stock</p>
            </div>
            
            <div class="dashboard-card">
                <h3>👥 Customers</h3>
                <p style="font-size: 2rem; color: #e74c3c; font-weight: bold;">
                    <?php echo $customer_stats['total_customers'] ?? 0; ?>
                </p>
                <p>Registered customers</p>
            </div>
            
            <div class="dashboard-card">
                <h3>🛒 Offline Orders</h3>
                <p style="font-size: 2rem; color: #f39c12; font-weight: bold;">
                    <?php echo $offline_stats['offline_orders'] ?? 0; ?>
                </p>
                <p>In-store orders</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div style="margin-top: 40px;">
            <h2>Quick Actions</h2>
            <div class="admin-dashboard">
                <div class="dashboard-card">
                    <h3>📋 Sales Report</h3>
                    <p>View and export sales data for online and offline orders.</p>
                    <button onclick="window.location.href='sales_report.php'" class="btn-primary">
                        View Sales Report
                    </button>
                </div>
                
                <div class="dashboard-card">
                    <h3>📦 Order from Suppliers</h3>
                    <p>Place orders for new books from suppliers.</p>
                    <button onclick="window.location.href='order_book_supplier.php'" class="btn-success">
                        Order Books
                    </button>
                </div>
                
                <div class="dashboard-card">
                    <h3>🏪 Offline Customer Orders</h3>
                    <p>Record orders from customers who visit the store.</p>
                    <button onclick="window.location.href='offline_orders.php'" class="btn-warning">
                        Record Offline Order
                    </button>
                </div>
                
                <div class="dashboard-card">
                    <h3>📚 Manage Books</h3>
                    <p>Add, edit, or remove books from inventory.</p>
                    <button onclick="showBookManagement()" class="btn-primary">
                        Manage Inventory
                    </button>
                </div>
                
                <div class="dashboard-card">
                    <h3>💬 Manage Feedback</h3>
                    <p>Review and approve customer feedback submissions.</p>
                    <button onclick="window.location.href='manage_feedback.php'" class="btn-warning">
                        Review Feedback
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div style="margin-top: 40px;">
            <h2>Recent Online Orders</h2>
            <?php
            try {
                $stmt = $pdo->query("
                    SELECT o.*, u.name, u.email, b.title, b.author 
                    FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    JOIN books b ON o.book_id = b.id 
                    ORDER BY o.date DESC 
                    LIMIT 10
                ");
                $recent_orders = $stmt->fetchAll();
            } catch (PDOException $e) {
                $recent_orders = [];
            }
            ?>
            
            <?php if (empty($recent_orders)): ?>
                <p>No recent orders found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Book</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><?php echo date('M j, Y', strtotime($order['date'])); ?></td>
                                <td><?php echo htmlspecialchars($order['name']); ?></td>
                                <td><?php echo htmlspecialchars($order['title']); ?> by <?php echo htmlspecialchars($order['author']); ?></td>
                                <td><?php echo $order['qty']; ?></td>
                                <td>LKR <?php echo number_format($order['total'], 2); ?></td>
                                <td><span style="color: #f39c12;"><?php echo ucfirst($order['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Chandrani Book Shop - Admin</h3>
                <p>Administrative dashboard for managing books, orders, and customer feedback. Serving book lovers with quality service.</p>
                <p>📍 123 Book Street, Literature City, LC 12345</p>
                <p>📞 +1 (555) 123-4567</p>
            </div>
            
            <div class="footer-section">
                <h3>Admin Tools</h3>
                <ul class="footer-links">
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="sales_report.php">Sales Report</a></li>
                    <li><a href="order_book_supplier.php">Order Books</a></li>
                    <li><a href="offline_orders.php">Offline Orders</a></li>
                    <li><a href="manage_feedback.php">Manage Feedback</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Quick Actions</h3>
                <ul class="footer-links">
                    <li><a href="javascript:showBookManagement()">Add New Book</a></li>
                    <li><a href="sales_report.php">View Reports</a></li>
                    <li><a href="home.php">Visit Store</a></li>
                    <li><a href="logout.php">Logout</a></li>
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
            <p>&copy; 2025 Chandrani Book Shop. All rights reserved. | Admin Panel</p>
        </div>
    </footer>

    <!-- Book Management Modal (Simple) -->
    <div id="book-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="background: white; margin: 5% auto; padding: 20px; width: 80%; max-width: 600px; border-radius: 8px;">
            <h3>Add New Book</h3>
            <form id="book-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" id="book-title" required>
                </div>
                <div class="form-group">
                    <label>Author:</label>
                    <input type="text" id="book-author" required>
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <input type="text" id="book-category" placeholder="e.g., Fiction, Romance, Thriller" required>
                </div>
                <div class="form-group">
                    <label>Price (LKR):</label>
                    <input type="number" step="0.01" id="book-price" required>
                </div>
                <div class="form-group">
                    <label>Stock:</label>
                    <input type="number" id="book-stock" required>
                </div>
                <div class="form-group">
                    <label>Book Cover Image:</label>
                    <input type="file" id="book-cover" accept="image/*" onchange="previewImage(event)">
                    <small style="color: #666; font-size: 0.9rem;">Upload JPG, PNG, or WEBP image. Recommended size: 300x450px</small>
                    <div id="image-preview" style="margin-top: 10px; display: none;">
                        <img id="preview-img" style="max-width: 150px; max-height: 200px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    </div>
                </div>
                <button type="submit" class="btn-success">Add Book</button>
                <button type="button" onclick="hideBookManagement()" class="btn-danger">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function showBookManagement() {
            document.getElementById('book-modal').style.display = 'block';
        }

        function hideBookManagement() {
            document.getElementById('book-modal').style.display = 'none';
            // Reset form and image preview
            document.getElementById('book-form').reset();
            document.getElementById('image-preview').style.display = 'none';
        }

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('image-preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('image-preview').style.display = 'none';
            }
        }

        document.getElementById('book-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('title', document.getElementById('book-title').value);
            formData.append('author', document.getElementById('book-author').value);
            formData.append('category', document.getElementById('book-category').value);
            formData.append('price', document.getElementById('book-price').value);
            formData.append('stock', document.getElementById('book-stock').value);
            
            const coverImage = document.getElementById('book-cover').files[0];
            if (coverImage) {
                formData.append('cover_image', coverImage);
            }

            fetch('add_book.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('successfully')) {
                    alert('Book added successfully!');
                    hideBookManagement();
                    location.reload();
                } else {
                    alert('Error: ' + data);
                }
            })
            .catch(error => {
                alert('Error adding book: ' + error);
            });
        });
    </script>
</body>
</html>
