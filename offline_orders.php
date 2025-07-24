<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once 'includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name']);
    $contact = trim($_POST['contact']);
    $book_title = trim($_POST['book_title']);
    $quantity = intval($_POST['quantity']);
    
    if (empty($customer_name) || empty($contact) || empty($book_title) || $quantity <= 0) {
        $message = '<div class="message error">All fields are required and quantity must be positive.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO offline_orders (customer_name, contact, book_title, quantity, date) VALUES (?, ?, ?, ?, NOW())");
            
            if ($stmt->execute([$customer_name, $contact, $book_title, $quantity])) {
                $message = '<div class="message success">Offline order recorded successfully!</div>';
                
                // Generate order ID for the receipt
                $order_id = $pdo->lastInsertId();
                $show_receipt = true;
            } else {
                $message = '<div class="message error">Failed to record order. Please try again.</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="message error">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Get recent offline orders
try {
    $stmt = $pdo->query("SELECT * FROM offline_orders ORDER BY date DESC");
    $offline_orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $offline_orders = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline Orders - Chandrani Book Shop</title>
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
        <h1>Offline Customer Orders</h1>
        
        <?php echo $message; ?>

        <!-- Order Receipt Modal -->
        <?php if (isset($show_receipt) && $show_receipt): ?>
        <div id="receipt-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="background: white; margin: 5% auto; padding: 30px; width: 80%; max-width: 500px; border-radius: 8px;">
                <div id="receipt-content">
                    <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px;">
                        <h2>Chandrani Book Shop</h2>
                        <p>123 Book Street, Literature City, LC 12345</p>
                        <p>Phone: +1 (555) 123-4567</p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <h3>Order Receipt</h3>
                        <p><strong>Order ID:</strong> #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></p>
                        <p><strong>Date:</strong> <?php echo date('M j, Y H:i'); ?></p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <h3>Customer Information</h3>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($contact); ?></p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <h3>Order Details</h3>
                        <p><strong>Book:</strong> <?php echo htmlspecialchars($book_title); ?></p>
                        <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
                        <p><strong>Estimated Total:</strong> ₹<?php echo number_format($quantity * 100, 2); ?></p>
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                        <p><em>Thank you for shopping with Chandrani Book Shop!</em></p>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <button onclick="printReceipt()" class="btn-primary">Print Receipt</button>
                    <button onclick="closeReceipt()" class="btn-danger">Close</button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 30px;">
            <div>
                <h2>Record New Offline Order</h2>
                <form method="POST" class="form-container">
                    <div class="form-group">
                        <label for="customer_name">Customer Name:</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact">Contact (Phone/Email):</label>
                        <input type="text" id="contact" name="contact" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="book_title">Book Title:</label>
                        <input type="text" id="book_title" name="book_title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" min="1" required>
                    </div>
                    
                    <button type="submit" class="btn-success">Record Order</button>
                </form>
            </div>
            
            <div>
                <h2>Recent Offline Orders</h2>
                <?php if (empty($offline_orders)): ?>
                    <p>No offline orders found.</p>
                <?php else: ?>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <?php foreach (array_slice($offline_orders, 0, 5) as $order): ?>
                            <div class="dashboard-card" style="margin-bottom: 15px;">
                                <h4><?php echo htmlspecialchars($order['customer_name']); ?></h4>
                                <p><strong>Contact:</strong> <?php echo htmlspecialchars($order['contact']); ?></p>
                                <p><strong>Book:</strong> <?php echo htmlspecialchars($order['book_title']); ?></p>
                                <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                                <p><strong>Date:</strong> <?php echo date('M j, Y H:i', strtotime($order['date'])); ?></p>
                                <button onclick="generateReceipt(<?php echo $order['id']; ?>)" class="btn-warning" style="margin-top: 10px;">
                                    Generate Receipt
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- All Offline Orders -->
        <div style="margin-top: 40px;">
            <h2>All Offline Orders</h2>
            <?php if (empty($offline_orders)): ?>
                <p>No offline orders found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Contact</th>
                            <th>Book Title</th>
                            <th>Quantity</th>
                            <th>Estimated Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offline_orders as $order): ?>
                            <tr>
                                <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo date('M j, Y H:i', strtotime($order['date'])); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['contact']); ?></td>
                                <td><?php echo htmlspecialchars($order['book_title']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>₹<?php echo number_format($order['quantity'] * 100, 2); ?></td>
                                <td>
                                    <button onclick="generateReceipt(<?php echo $order['id']; ?>)" class="btn-warning">
                                        Receipt
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Chandrani Book Shop. All rights reserved.</p>
    </footer>

    <script>
        function closeReceipt() {
            document.getElementById('receipt-modal').style.display = 'none';
        }

        function printReceipt() {
            const receiptContent = document.getElementById('receipt-content').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Order Receipt</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h2, h3 { margin: 10px 0; }
                        p { margin: 5px 0; }
                    </style>
                </head>
                <body>
                    ${receiptContent}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        }

        function generateReceipt(orderId) {
            // This would typically make an AJAX call to get order details
            // For now, we'll just show an alert
            alert('Receipt generation feature would be implemented here for Order ID: ' + orderId);
        }
    </script>
</body>
</html>
