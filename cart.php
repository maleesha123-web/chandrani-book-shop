<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Chandrani Book Shop</title>
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
        <h1>Shopping Cart</h1>
        
        <div id="cart-items">
            <p>Your cart is empty. <a href="shop.php">Continue shopping</a></p>
        </div>

        <div id="cart-total" class="cart-total" style="display: none;">
            Total: LKR <span id="total-amount">0.00</span>
            <br>
            <?php if (isset($_SESSION['user'])): ?>
                <button onclick="window.location.href='checkout.php'" class="btn-primary" style="margin-top: 20px;">
                    Proceed to Checkout
                </button>
            <?php else: ?>
                <div class="message" style="margin-top: 20px;">
                    <p><a href="login.php">Login</a> to proceed with checkout</p>
                </div>
            <?php endif; ?>
            <button onclick="clearCart()" class="btn-danger" style="margin-top: 10px;">
                Clear Cart
            </button>
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
        // Display cart items on page load
        document.addEventListener('DOMContentLoaded', function() {
            displayCartItems();
        });

        function displayCartItems() {
            const cartItemsDiv = document.getElementById('cart-items');
            const cartTotalDiv = document.getElementById('cart-total');
            const totalAmountSpan = document.getElementById('total-amount');
            
            if (cart.items.length === 0) {
                cartItemsDiv.innerHTML = '<p>Your cart is empty. <a href="shop.php">Continue shopping</a></p>';
                cartTotalDiv.style.display = 'none';
                return;
            }

            let itemsHTML = '';
            let total = 0;

            cart.items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                
                itemsHTML += `
                    <div class="cart-item">
                        <div>
                            <strong>${item.title}</strong><br>
                            <small>by ${item.author}</small><br>
                            LKR ${item.price.toFixed(2)} each
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div>
                                <button onclick="updateCartQuantity(${item.id}, ${item.quantity - 1})" class="btn-warning">-</button>
                                <span style="margin: 0 10px;">${item.quantity}</span>
                                <button onclick="updateCartQuantity(${item.id}, ${item.quantity + 1})" class="btn-warning">+</button>
                            </div>
                            <div><strong>LKR ${itemTotal.toFixed(2)}</strong></div>
                            <button onclick="removeFromCart(${item.id})" class="btn-danger">Remove</button>
                        </div>
                    </div>
                `;
            });

            cartItemsDiv.innerHTML = itemsHTML;
            totalAmountSpan.textContent = total.toFixed(2);
            cartTotalDiv.style.display = 'block';
        }

        function updateCartQuantity(bookId, newQuantity) {
            cart.updateQuantity(bookId, newQuantity);
            displayCartItems();
        }

        function removeFromCart(bookId) {
            cart.removeItem(bookId);
            displayCartItems();
        }

        function clearCart() {
            if (confirm('Are you sure you want to clear your cart?')) {
                cart.clear();
                displayCartItems();
            }
        }
    </script>
</body>
</html>
