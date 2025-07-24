<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chandrani Book Shop - Home</title>
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
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="feedback.php">Feedback</a></li>
                
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

    <!-- Banner Slideshow -->
    <div class="banner">
        <div class="slide active">
            <img src="images/thought-catalog-mmWqrsjZ4Lw-unsplash.jpg" alt="Welcome">
            <div class="slide-content">
                <h2>Welcome to Chandrani Book Shop</h2>
                <p>Discover endless stories and knowledge</p>
            </div>
        </div>
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=1200&h=500&fit=crop&crop=center" alt="Books">
            <div class="slide-content">
                <h2>Explore Amazing Collections</h2>
                <p>From classics to contemporary bestsellers</p>
            </div>
        </div>
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=1200&h=500&fit=crop&crop=center" alt="Reading">
            <div class="slide-content">
                <h2>Special Offers Available</h2>
                <p>Great deals on your favorite books</p>
            </div>
        </div>
    </div>

    <div class="container">
        <?php 
        // Display feedback message if exists
        if (isset($_SESSION['feedback_message'])) {
            echo $_SESSION['feedback_message'];
            unset($_SESSION['feedback_message']);
        }
        ?>
        
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome to Chandrani Book Shop</h1>
            <p>Discover a world of knowledge and imagination through our carefully curated collection of books. From bestsellers to classics, from fiction to non-fiction, we have something for every reader. Browse our extensive catalog and embark on your next literary adventure today!</p>
        </div>

        <!-- About Us Section -->
        <section id="about">
            <h2>About Us</h2>
            <p>Chandrani Book Shop has been serving book lovers for years, providing quality books at affordable prices. We believe in the power of reading to transform lives and expand horizons. Our collection spans across various genres and categories, ensuring that every customer finds their perfect read.</p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
                <div class="dashboard-card">
                    <h3>📚 Wide Selection</h3>
                    <p>Thousands of books across all genres and categories</p>
                </div>
                <div class="dashboard-card">
                    <h3>💰 Best Prices</h3>
                    <p>Competitive pricing with special offers and discounts</p>
                </div>
                <div class="dashboard-card">
                    <h3>🚚 Fast Delivery</h3>
                    <p>Quick and reliable delivery to your doorstep</p>
                </div>
                <div class="dashboard-card">
                    <h3>🔒 Secure Payment</h3>
                    <p>Safe and secure online payment options</p>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" style="margin-top: 50px;">
            <h2>Contact Us</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                <div class="dashboard-card">
                    <h3>📍 Address</h3>
                    <p>123 Book Street<br>Literature City, LC 12345</p>
                </div>
                <div class="dashboard-card">
                    <h3>📞 Phone</h3>
                    <p>+1 (555) 123-4567</p>
                </div>
                <div class="dashboard-card">
                    <h3>📧 Email</h3>
                    <p>info@chandranibookshop.com</p>
                </div>
                <div class="dashboard-card">
                    <h3>🕒 Hours</h3>
                    <p>Mon-Sat: 9 AM - 8 PM<br>Sunday: 10 AM - 6 PM</p>
                </div>
            </div>
        </section>
        
        <!-- Customer Feedback Section -->
        <section id="customer-feedback" style="margin-top: 50px; padding: 40px 0; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 15px;">
            <h2 style="text-align: center; margin-bottom: 30px; color: #333;">What Our Customers Say</h2>
            <div id="feedback-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <!-- Feedback will be loaded here -->
            </div>
            <div style="text-align: center; margin-top: 30px;">
                <a href="feedback.php" class="btn-primary" style="display: inline-block; padding: 12px 24px; background: linear-gradient(45deg, #667eea, #764ba2); color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">Share Your Feedback</a>
            </div>
        </section>
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
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
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
        // Load customer feedback on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCustomerFeedback();
        });

        function loadCustomerFeedback() {
            fetch('get_feedback.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('feedback-container');
                    if (data.length === 0) {
                        container.innerHTML = '<p style="text-align: center; color: #666; grid-column: 1 / -1;">No feedback available yet. Be the first to share your experience!</p>';
                        return;
                    }
                    
                    container.innerHTML = data.map(feedback => `
                        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 4px solid #667eea;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <strong style="color: #333;">${feedback.customer_name || 'Anonymous Customer'}</strong>
                                <div style="color: #ffd700;">${'⭐'.repeat(feedback.rating)}</div>
                            </div>
                            <p style="color: #666; margin: 10px 0; line-height: 1.5;">${feedback.description}</p>
                            <small style="color: #999;">${new Date(feedback.created_at).toLocaleDateString()}</small>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading feedback:', error);
                    document.getElementById('feedback-container').innerHTML = '<p style="text-align: center; color: #666; grid-column: 1 / -1;">Unable to load feedback at this time.</p>';
                });
        }
    </script>
</body>
</html>
