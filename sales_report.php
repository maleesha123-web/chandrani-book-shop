<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once 'includes/db.php';

// Get online orders
try {
    $stmt = $pdo->query("
        SELECT o.*, u.name, u.email, b.title, b.author 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        JOIN books b ON o.book_id = b.id 
        ORDER BY o.date DESC
    ");
    $online_orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $online_orders = [];
    $error = "Error loading online orders: " . $e->getMessage();
}

// Get offline orders
try {
    $stmt = $pdo->query("SELECT * FROM offline_orders ORDER BY date DESC");
    $offline_orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $offline_orders = [];
}

// Calculate totals
$online_total = array_sum(array_column($online_orders, 'total'));
$offline_total = 0;
foreach ($offline_orders as $order) {
    $offline_total += $order['quantity'] * 100; // Assume average price of ₹100 per book for offline orders
}
$grand_total = $online_total + $offline_total;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Chandrani Book Shop</title>
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
                <li><a href="#"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>Sales Report</h1>
            <button onclick="printReport()" class="btn-primary">Print Report</button>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Summary Cards -->
        <div class="admin-dashboard">
            <div class="dashboard-card">
                <h3>💻 Online Sales</h3>
                <p style="font-size: 1.5rem; color: #27ae60; font-weight: bold;">
                    LKR <?php echo number_format($online_total, 2); ?>
                </p>
                <p><?php echo count($online_orders); ?> orders</p>
            </div>
            
            <div class="dashboard-card">
                <h3>🏪 Offline Sales</h3>
                <p style="font-size: 1.5rem; color: #3498db; font-weight: bold;">
                    LKR <?php echo number_format($offline_total, 2); ?>
                </p>
                <p><?php echo count($offline_orders); ?> orders</p>
            </div>
            
            <div class="dashboard-card">
                <h3>📊 Total Sales</h3>
                <p style="font-size: 1.5rem; color: #e74c3c; font-weight: bold;">
                    LKR <?php echo number_format($grand_total, 2); ?>
                </p>
                <p><?php echo count($online_orders) + count($offline_orders); ?> orders</p>
            </div>
        </div>

        <!-- Online Orders -->
        <div style="margin-top: 40px;">
            <h2>Online Orders</h2>
            <?php if (empty($online_orders)): ?>
                <p>No online orders found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Book</th>
                            <th>Author</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($online_orders as $order): ?>
                            <tr>
                                <td><?php echo date('M j, Y H:i', strtotime($order['date'])); ?></td>
                                <td><?php echo htmlspecialchars($order['name']); ?></td>
                                <td><?php echo htmlspecialchars($order['email']); ?></td>
                                <td><?php echo htmlspecialchars($order['title']); ?></td>
                                <td><?php echo htmlspecialchars($order['author']); ?></td>
                                <td><?php echo $order['qty']; ?></td>
                                <td>LKR <?php echo number_format($order['total'], 2); ?></td>
                                <td><?php echo ucfirst($order['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Offline Orders -->
        <div style="margin-top: 40px;">
            <h2>Offline Orders</h2>
            <?php if (empty($offline_orders)): ?>
                <p>No offline orders found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Contact</th>
                            <th>Book Title</th>
                            <th>Quantity</th>
                            <th>Estimated Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offline_orders as $order): ?>
                            <tr>
                                <td><?php echo date('M j, Y H:i', strtotime($order['date'])); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['contact']); ?></td>
                                <td><?php echo htmlspecialchars($order['book_title']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>LKR <?php echo number_format($order['quantity'] * 100, 2); ?></td>
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
                <p>Administrative dashboard for managing books, orders, and customer feedback.</p>
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
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Quick Actions</h3>
                <ul class="footer-links">
                    <li><a href="admin_dashboard.php">View Dashboard</a></li>
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
            <p>&copy; 2025 Chandrani Book Shop. All rights reserved. | Sales Report</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
