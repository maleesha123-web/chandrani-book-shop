<?php
session_start();
require_once 'includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_email = trim($_POST['customer_email']);
    $customer_name = trim($_POST['customer_name']);
    $description = trim($_POST['description']);
    $rating = (int)$_POST['rating'];
    
    if (empty($customer_email) || empty($description) || $rating < 1 || $rating > 5) {
        $message = '<div class="message error">Please fill all required fields and provide a valid rating (1-5 stars).</div>';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedback (customer_email, customer_name, description, rating) VALUES (?, ?, ?, ?)");
            $stmt->execute([$customer_email, $customer_name, $description, $rating]);
            
            // Set success message in session and redirect
            $_SESSION['feedback_message'] = '<div class="message success">Thank you for your feedback! Your review has been submitted successfully and is pending approval.</div>';
            header('Location: home.php');
            exit();
        } catch (PDOException $e) {
            $message = '<div class="message error">Error submitting feedback: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Chandrani Book Shop</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .feedback-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            color: white;
        }
        
        .feedback-form {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .feedback-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .feedback-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .feedback-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .star-rating {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }
        
        .star-rating input[type="radio"] {
            display: none;
        }
        
        .star-rating label {
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input[type="radio"]:checked ~ label {
            color: #ffd700;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
            backdrop-filter: blur(5px);
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }
        
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #333;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 215, 0, 0.3);
        }
        
        .rating-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .feedback-icon {
            font-size: 4rem;
            text-align: center;
            margin-bottom: 20px;
            opacity: 0.8;
        }
    </style>
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
                <li><a href="feedback.php" class="active">Feedback</a></li>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="profile.php"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <?php echo $message; ?>
        
        <div class="feedback-container">
            <div class="feedback-icon">💬</div>
            <div class="feedback-header">
                <h1>We Value Your Feedback</h1>
                <p>Help us improve by sharing your experience with Chandrani Book Shop</p>
            </div>
            
            <div class="feedback-form">
                <form method="POST" id="feedback-form">
                    <div class="form-group">
                        <label for="customer_email">Email Address *</label>
                        <input type="email" id="customer_email" name="customer_email" 
                               value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['email']) : ''; ?>" 
                               placeholder="your@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_name">Your Name</label>
                        <input type="text" id="customer_name" name="customer_name" 
                               value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['name']) : ''; ?>" 
                               placeholder="Your full name">
                    </div>
                    
                    <div class="form-group">
                        <label>Rate Our Service *</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5">⭐</label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4">⭐</label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3">⭐</label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2">⭐</label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1">⭐</label>
                        </div>
                        <div class="rating-labels">
                            <span>Excellent</span>
                            <span>Good</span>
                            <span>Average</span>
                            <span>Poor</span>
                            <span>Terrible</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Your Feedback *</label>
                        <textarea id="description" name="description" rows="6" 
                                  placeholder="Tell us about your experience with our books, service, delivery, or anything else you'd like to share..." required></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Submit Feedback</button>
                </form>
            </div>
        </div>
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
        // Star rating interaction
        const stars = document.querySelectorAll('.star-rating input[type="radio"]');
        const labels = document.querySelectorAll('.star-rating label');
        
        labels.forEach((label, index) => {
            label.addEventListener('mouseover', () => {
                labels.forEach((l, i) => {
                    if (i >= index) {
                        l.style.color = '#ffd700';
                        l.style.textShadow = '0 0 10px rgba(255, 215, 0, 0.5)';
                    } else {
                        l.style.color = 'rgba(255, 255, 255, 0.3)';
                        l.style.textShadow = 'none';
                    }
                });
            });
        });
        
        document.querySelector('.star-rating').addEventListener('mouseleave', () => {
            const checked = document.querySelector('.star-rating input[type="radio"]:checked');
            labels.forEach((label, index) => {
                if (checked && index >= Array.from(stars).indexOf(checked)) {
                    label.style.color = '#ffd700';
                    label.style.textShadow = '0 0 10px rgba(255, 215, 0, 0.5)';
                } else {
                    label.style.color = 'rgba(255, 255, 255, 0.3)';
                    label.style.textShadow = 'none';
                }
            });
        });
        
        // Form validation
        document.getElementById('feedback-form').addEventListener('submit', function(e) {
            const rating = document.querySelector('input[name="rating"]:checked');
            if (!rating) {
                e.preventDefault();
                alert('Please select a star rating for our service.');
                return;
            }
        });
    </script>
</body>
</html>
