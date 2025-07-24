<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Chandrani Book Shop</title>
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
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                
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
        <div class="about-container">
            <div class="welcome-section">
                <h1>About Chandrani Book Shop</h1>
                <p>Your Gateway to Literary Adventures</p>
            </div>

            <div class="about-content">
                <div class="about-story">
                    <h2>Our Story</h2>
                    <p>Founded with a passion for literature and learning, Chandrani Book Shop has been serving book lovers for years. Our journey began with a simple belief: that books have the power to transform lives, expand horizons, and connect people across cultures and generations.</p>
                    
                    <p>Named after our founder's vision of creating a "moonlit sanctuary" for readers (Chandrani meaning "moonlight" in Sanskrit), our bookshop has grown from a small local store to a comprehensive online platform serving readers worldwide.</p>
                    
                    <p>We pride ourselves on our carefully curated collection that spans across genres, from timeless classics that have shaped literature to contemporary bestsellers that capture the spirit of our times. Whether you're seeking adventure in fantasy realms, wisdom in philosophical texts, or escape in romantic tales, we have something special waiting for you.</p>
                </div>

                <div class="about-mission">
                    <h2>Our Mission</h2>
                    <div class="mission-grid">
                        <div class="mission-card">
                            <h3>📚 Curated Excellence</h3>
                            <p>We handpick every book in our collection, ensuring quality and variety that caters to diverse reading preferences and interests.</p>
                        </div>
                        <div class="mission-card">
                            <h3>🌟 Customer First</h3>
                            <p>Your reading journey is our priority. We provide personalized recommendations and exceptional service to enhance your book-buying experience.</p>
                        </div>
                        <div class="mission-card">
                            <h3>💡 Knowledge Access</h3>
                            <p>We believe knowledge should be accessible. Our competitive pricing and regular offers make great books affordable for everyone.</p>
                        </div>
                        <div class="mission-card">
                            <h3>🤝 Community Building</h3>
                            <p>We foster a community of readers through book recommendations, reviews, and connecting people who share the love for literature.</p>
                        </div>
                    </div>
                </div>

                <div class="about-values">
                    <h2>What We Offer</h2>
                    <div class="values-list">
                        <div class="value-item">
                            <strong>Extensive Collection:</strong> Over 1000+ books across multiple genres including fiction, non-fiction, academic texts, children's books, and specialty publications.
                        </div>
                        <div class="value-item">
                            <strong>Quality Assurance:</strong> All books are carefully inspected for quality, and we ensure authentic publications from reputable publishers.
                        </div>
                        <div class="value-item">
                            <strong>Competitive Pricing:</strong> Fair prices in LKR with regular discounts and special offers for our valued customers.
                        </div>
                        <div class="value-item">
                            <strong>Fast Delivery:</strong> Quick and reliable delivery service across Sri Lanka with careful packaging to protect your books.
                        </div>
                        <div class="value-item">
                            <strong>Online & Offline:</strong> Shop online through our website or visit our physical store for a traditional browsing experience.
                        </div>
                        <div class="value-item">
                            <strong>Expert Recommendations:</strong> Our book-loving staff can help you discover your next great read based on your preferences.
                        </div>
                    </div>
                </div>

                <div class="about-team">
                    <h2>Why Choose Us?</h2>
                    <p>At Chandrani Book Shop, we're not just selling books—we're sharing stories, spreading knowledge, and building connections. Every purchase supports our mission to make quality literature accessible to everyone.</p>
                    
                    <p>Our team of book enthusiasts understands the magic that happens when the right book finds the right reader. We're here to help you discover that magic, whether you're a casual reader looking for entertainment or a serious scholar seeking knowledge.</p>
                    
                    <div class="stats-grid">
                        <div class="stat-item">
                            <h3>1000+</h3>
                            <p>Books in Collection</p>
                        </div>
                        <div class="stat-item">
                            <h3>50+</h3>
                            <p>Book Categories</p>
                        </div>
                        <div class="stat-item">
                            <h3>500+</h3>
                            <p>Happy Customers</p>
                        </div>
                        <div class="stat-item">
                            <h3>24/7</h3>
                            <p>Online Service</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Chandrani Book Shop. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
