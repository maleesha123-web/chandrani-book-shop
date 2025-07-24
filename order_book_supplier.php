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
    $book_title = trim($_POST['book_title']);
    $author = trim($_POST['author']);
    $quantity = intval($_POST['quantity']);
    $supplier_name = trim($_POST['supplier_name']);
    
    if (empty($book_title) || empty($author) || empty($supplier_name) || $quantity <= 0) {
        $message = '<div class="message error">All fields are required and quantity must be positive.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO supplier_orders (book_title, author, quantity, supplier_name, date) VALUES (?, ?, ?, ?, NOW())");
            
            if ($stmt->execute([$book_title, $author, $quantity, $supplier_name])) {
                $message = '<div class="message success">Order placed with supplier successfully!</div>';
            } else {
                $message = '<div class="message error">Failed to place order. Please try again.</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="message error">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Get recent supplier orders
try {
    $stmt = $pdo->query("SELECT * FROM supplier_orders ORDER BY date DESC LIMIT 10");
    $recent_orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $recent_orders = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Books from Suppliers - Chandrani Book Shop</title>
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
        <h1>Order Books from Suppliers</h1>
        
        <?php echo $message; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 30px;">
            <div>
                <h2>Place New Order</h2>
                <form method="POST" class="form-container">
                    <div class="form-group">
                        <label for="book_title">Book Title:</label>
                        <input type="text" id="book_title" name="book_title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="author">Author:</label>
                        <input type="text" id="author" name="author" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="supplier_name">Supplier Name:</label>
                        <select id="supplier_name" name="supplier_name" required>
                            <option value="">Select Supplier</option>
                            <option value="Penguin Random House">Penguin Random House</option>
                            <option value="HarperCollins Publishers">HarperCollins Publishers</option>
                            <option value="Macmillan Publishers">Macmillan Publishers</option>
                            <option value="Hachette Book Group">Hachette Book Group</option>
                            <option value="Simon & Schuster">Simon & Schuster</option>
                            <option value="Scholastic Corporation">Scholastic Corporation</option>
                            <option value="Pearson Education">Pearson Education</option>
                            <option value="Oxford University Press">Oxford University Press</option>
                            <option value="Cambridge University Press">Cambridge University Press</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="other-supplier" style="display: none;">
                        <label for="other_supplier_name">Other Supplier Name:</label>
                        <input type="text" id="other_supplier_name" name="other_supplier_name">
                    </div>
                    
                    <button type="submit" class="btn-success">Place Order</button>
                </form>
            </div>
            
            <div>
                <h2>Recent Supplier Orders</h2>
                <?php if (empty($recent_orders)): ?>
                    <p>No supplier orders found.</p>
                <?php else: ?>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <?php foreach ($recent_orders as $order): ?>
                            <div class="dashboard-card" style="margin-bottom: 15px;">
                                <h4><?php echo htmlspecialchars($order['book_title']); ?></h4>
                                <p><strong>Author:</strong> <?php echo htmlspecialchars($order['author']); ?></p>
                                <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                                <p><strong>Supplier:</strong> <?php echo htmlspecialchars($order['supplier_name']); ?></p>
                                <p><strong>Date:</strong> <?php echo date('M j, Y H:i', strtotime($order['date'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- All Supplier Orders -->
        <div style="margin-top: 40px;">
            <h2>All Supplier Orders</h2>
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM supplier_orders ORDER BY date DESC");
                $all_orders = $stmt->fetchAll();
            } catch (PDOException $e) {
                $all_orders = [];
            }
            ?>
            
            <?php if (empty($all_orders)): ?>
                <p>No supplier orders found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Quantity</th>
                            <th>Supplier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_orders as $order): ?>
                            <tr>
                                <td><?php echo date('M j, Y H:i', strtotime($order['date'])); ?></td>
                                <td><?php echo htmlspecialchars($order['book_title']); ?></td>
                                <td><?php echo htmlspecialchars($order['author']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td><?php echo htmlspecialchars($order['supplier_name']); ?></td>
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
        document.getElementById('supplier_name').addEventListener('change', function() {
            const otherSupplierDiv = document.getElementById('other-supplier');
            const otherSupplierInput = document.getElementById('other_supplier_name');
            
            if (this.value === 'Other') {
                otherSupplierDiv.style.display = 'block';
                otherSupplierInput.required = true;
            } else {
                otherSupplierDiv.style.display = 'none';
                otherSupplierInput.required = false;
            }
        });

        // Handle form submission for "Other" supplier
        document.querySelector('form').addEventListener('submit', function(e) {
            const supplierSelect = document.getElementById('supplier_name');
            const otherSupplierInput = document.getElementById('other_supplier_name');
            
            if (supplierSelect.value === 'Other' && otherSupplierInput.value.trim() !== '') {
                // Create a hidden input with the other supplier name
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'supplier_name';
                hiddenInput.value = otherSupplierInput.value.trim();
                this.appendChild(hiddenInput);
            }
        });
    </script>
</body>
</html>
