<?php
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message_text = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $message = '<div class="message error">All fields are required.</div>';
    } else {
        // In a real application, you would send email or save to database
        $message = '<div class="message success">Thank you for your message! We will get back to you within 24 hours.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Chandrani Book Shop</title>
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
        <div class="contact-container">
            <div class="welcome-section">
                <h1>Contact Us</h1>
                <p>We'd love to hear from you. Get in touch with us!</p>
            </div>

            <div class="contact-content">
                <div class="contact-info">
                    <h2>Get in Touch</h2>
                    <p>Have a question about a book? Need help with your order? Or just want to share your latest reading experience? We're here to help!</p>
                    
                    <div class="contact-methods">
                        <div class="contact-method">
                            <h3>📍 Visit Our Store</h3>
                            <p><strong>Address:</strong><br>
                            123 Book Street<br>
                            Literature District<br>
                            Colombo 07, Sri Lanka</p>
                        </div>
                        
                        <div class="contact-method">
                            <h3>📞 Call Us</h3>
                            <p><strong>Phone:</strong> +94 11 234 5678<br>
                            <strong>Mobile:</strong> +94 77 123 4567</p>
                        </div>
                        
                        <div class="contact-method">
                            <h3>📧 Email Us</h3>
                            <p><strong>General:</strong> info@chandranibookshop.lk<br>
                            <strong>Orders:</strong> orders@chandranibookshop.lk<br>
                            <strong>Support:</strong> support@chandranibookshop.lk</p>
                        </div>
                        
                        <div class="contact-method">
                            <h3>🕒 Business Hours</h3>
                            <p><strong>Monday - Saturday:</strong><br>
                            9:00 AM - 8:00 PM<br>
                            <strong>Sunday:</strong><br>
                            10:00 AM - 6:00 PM<br>
                            <strong>Public Holidays:</strong> Closed</p>
                        </div>
                    </div>
                </div>

                <div class="contact-form-section">
                    <h2>Send us a Message</h2>
                    
                    <?php echo $message; ?>
                    
                    <form method="POST" class="contact-form">
                        <div class="form-group">
                            <label for="name">Your Name:</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject:</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="General Inquiry">General Inquiry</option>
                                <option value="Book Request">Book Request</option>
                                <option value="Order Issue">Order Issue</option>
                                <option value="Delivery Question">Delivery Question</option>
                                <option value="Book Recommendation">Book Recommendation</option>
                                <option value="Feedback">Feedback</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea id="message" name="message" rows="6" placeholder="Tell us how we can help you..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-primary">Send Message</button>
                    </form>
                </div>
            </div>

            <div class="contact-additional">
                <div class="faq-section">
                    <h2>Frequently Asked Questions</h2>
                    <div class="faq-grid">
                        <div class="faq-item">
                            <h4>How can I track my order?</h4>
                            <p>Once your order is confirmed, you'll receive an email with tracking information. You can also contact our support team for updates.</p>
                        </div>
                        <div class="faq-item">
                            <h4>What is your return policy?</h4>
                            <p>We accept returns within 7 days of delivery for books in original condition. Please contact us for return authorization.</p>
                        </div>
                        <div class="faq-item">
                            <h4>Do you offer book recommendations?</h4>
                            <p>Absolutely! Our staff loves helping customers find their next great read. Contact us with your preferences and we'll suggest books you'll love.</p>
                        </div>
                        <div class="faq-item">
                            <h4>Can I request specific books?</h4>
                            <p>Yes! If we don't have a book you're looking for, we can try to order it for you. Just send us the book details and we'll check availability.</p>
                        </div>
                    </div>
                </div>
                
                <div class="social-section">
                    <h2>Follow Us</h2>
                    <p>Stay connected with us on social media for book recommendations, author spotlights, and special offers!</p>
                    <div class="social-links">
                        <a href="#" class="social-link">📘 Facebook</a>
                        <a href="#" class="social-link">📸 Instagram</a>
                        <a href="#" class="social-link">🐦 Twitter</a>
                        <a href="#" class="social-link">💼 LinkedIn</a>
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
