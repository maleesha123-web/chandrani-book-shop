<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once 'includes/db.php';

$message = '';

// Handle feedback approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $feedback_id = (int)$_POST['feedback_id'];
    $action = $_POST['action'];
    
    if (in_array($action, ['approved', 'rejected'])) {
        try {
            $stmt = $pdo->prepare("UPDATE feedback SET status = ? WHERE id = ?");
            $stmt->execute([$action, $feedback_id]);
            $message = "<div class='message success'>Feedback {$action} successfully!</div>";
        } catch (PDOException $e) {
            $message = "<div class='message error'>Error updating feedback: " . $e->getMessage() . "</div>";
        }
    }
}

// Get all feedback
try {
    $stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC");
    $feedback_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $feedback_list = [];
    $message = "<div class='message error'>Error loading feedback: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback - Chandrani Book Shop Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .feedback-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .rating-stars {
            color: #ffd700;
            font-size: 1.2rem;
        }
        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Chandrani Book Shop - Admin</div>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_feedback.php" class="active">Manage Feedback</a></li>
                <li><a href="sales_report.php">Sales Report</a></li>
                <li><a href="order_book_supplier.php">Order Books</a></li>
                <li><a href="offline_orders.php">Offline Orders</a></li>
                <li><a href="#"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Manage Customer Feedback</h1>
        
        <?php echo $message; ?>
        
        <div style="margin-bottom: 30px;">
            <p><strong>Total Feedback:</strong> <?php echo count($feedback_list); ?></p>
            <?php
            $pending = array_filter($feedback_list, function($f) { return $f['status'] === 'pending'; });
            $approved = array_filter($feedback_list, function($f) { return $f['status'] === 'approved'; });
            $rejected = array_filter($feedback_list, function($f) { return $f['status'] === 'rejected'; });
            ?>
            <p><strong>Pending:</strong> <?php echo count($pending); ?> | 
               <strong>Approved:</strong> <?php echo count($approved); ?> | 
               <strong>Rejected:</strong> <?php echo count($rejected); ?></p>
        </div>

        <?php if (empty($feedback_list)): ?>
            <div class="message">No feedback submissions found.</div>
        <?php else: ?>
            <?php foreach ($feedback_list as $feedback): ?>
                <div class="feedback-card">
                    <div class="feedback-header">
                        <div>
                            <strong><?php echo htmlspecialchars($feedback['customer_name'] ?: 'Anonymous Customer'); ?></strong>
                            <br>
                            <small><?php echo htmlspecialchars($feedback['customer_email']); ?></small>
                            <br>
                            <small><?php echo date('M j, Y g:i A', strtotime($feedback['created_at'])); ?></small>
                        </div>
                        <div style="text-align: right;">
                            <div class="rating-stars">
                                <?php echo str_repeat('⭐', $feedback['rating']); ?>
                            </div>
                            <span class="status-badge status-<?php echo $feedback['status']; ?>">
                                <?php echo ucfirst($feedback['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div style="margin: 15px 0; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($feedback['description'])); ?>
                    </div>
                    
                    <?php if ($feedback['status'] === 'pending'): ?>
                        <div class="action-buttons">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                <input type="hidden" name="action" value="approved">
                                <button type="submit" class="btn-success" onclick="return confirm('Approve this feedback?')">
                                    ✅ Approve
                                </button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                <input type="hidden" name="action" value="rejected">
                                <button type="submit" class="btn-danger" onclick="return confirm('Reject this feedback?')">
                                    ❌ Reject
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 Chandrani Book Shop. All rights reserved.</p>
    </footer>
</body>
</html>
