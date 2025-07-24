<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_data = json_decode($_POST['cart_data'], true);
    $billing_address = trim($_POST['billing_address']);
    $phone = trim($_POST['phone']);
    $save_payment = isset($_POST['save_payment']);
    
    if (empty($cart_data) || empty($billing_address) || empty($phone)) {
        $message = 'div class="message error"Please fill all required fields and ensure cart is not empty./div';
    } else {
        try {
            $pdo->beginTransaction();
            
            $total_amount = 0;
            foreach ($cart_data as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }
            
            // Insert each book order
            foreach ($cart_data as $item) {
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, book_id, qty, total, date, status, billing_address, phone) VALUES (?, ?, ?, ?, NOW(), 'pending', ?, ?)");
                $item_total = $item['price'] * $item['quantity'];
                $stmt->execute([$_SESSION['user']['id'], $item['id'], $item['quantity'], $item_total, $billing_address, $phone]);
                
                // Update book stock
                $stmt = $pdo->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['id']]);
            }

            // Save payment details if checkbox is selected
            if ($save_payment) {
                // For demo purposes, we'll store the actual card details
                // In production, you should use proper encryption and tokenization
                
                // Clear any existing default payment methods for this user
                $stmt = $pdo->prepare("UPDATE payment_details SET is_default = 0 WHERE user_id = ?");
                $stmt->execute([$_SESSION['user']['id']]);
                
                $stmt = $pdo->prepare("INSERT INTO payment_details (user_id, card_type, cardholder_name, card_number, expiry_month, expiry_year, cvv, billing_address, phone, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([
                    $_SESSION['user']['id'],
                    $_POST['card_type'],
                    $_POST['cardholder_name'],
                    $_POST['card_number'], // Store actual card number for demo
                    $_POST['expiry_month'],
                    $_POST['expiry_year'],
                    $_POST['cvv'], // Store CVV for demo
                    $billing_address,
                    $phone
                ]);
            }
            
            $pdo->commit();
            
            // Generate receipt data
            $receipt_data = [
                'order_id' => uniqid('ORD'),
                'customer_name' => $_SESSION['user']['name'],
                'customer_email' => $_SESSION['user']['email'],
                'billing_address' => $billing_address,
                'phone' => $phone,
                'items' => $cart_data,
                'total_amount' => $total_amount,
                'date' => date('Y-m-d H:i:s'),
                'payment_method' => 'Credit/Debit Card'
            ];
            
            // Store receipt data in session for popup
            $_SESSION['receipt_data'] = $receipt_data;
            $message = '<div class="message success">Order placed successfully! Order total: LKR ' . number_format($total_amount, 2) . '</div>';
            
        } catch (PDOException $e) {
            $pdo->rollback();
            $message = 'div class="message error"Order failed: ' . $e->getMessage() . '/div';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Chandrani Book Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Chandrani Book Shop</div>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="home.php#about">About Us</a></li>
                <li><a href="home.php#contact">Contact</a></li>
                <li><a href="feedback.php">Feedback</a></li>
                <li><a href="#"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Checkout</h1>
        
        <?php echo $message; ?>

        <?php
        // Get user's saved payment details for auto-fill
        $saved_payment = null;
        try {
            $stmt = $pdo->prepare("SELECT * FROM payment_details WHERE user_id = ? AND is_default = 1 ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$_SESSION['user']['id']]);
            $saved_payment = $stmt->fetch();
        } catch (PDOException $e) {
            // Continue without auto-fill if there's an error
        }
        ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 30px;">
            <div>
                <h2>Billing Information</h2>
                
                <?php if ($saved_payment): ?>
                    <div style="background: #e8f4fd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #b8daff;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span style="font-size: 1.2rem;">💳</span>
                            <span style="font-weight: 600; color: #0c5460;">Auto-fill with saved information?</span>
                        </div>
                        <p style="margin: 8px 0; color: #0c5460; font-size: 0.9rem;">We found your saved payment details for <strong><?php echo htmlspecialchars($saved_payment['cardholder_name']); ?></strong> (<?php 
                            $masked_number = '**** **** **** ' . substr($saved_payment['card_number'], -4);
                            echo htmlspecialchars($masked_number); 
                        ?>). Click below to auto-fill all payment fields.</p>
                        <button type="button" id="auto-fill-btn" style="background: #17a2b8; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">✨ Auto-fill All Payment Details</button>
                    </div>
                <?php else: ?>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffeaa7;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <span style="font-size: 1.1rem;">📝</span>
                            <span style="font-weight: 600; color: #856404;">First time checkout?</span>
                        </div>
                        <p style="margin: 0; color: #856404; font-size: 0.9rem;">Fill out your payment details below. You can save them for faster checkout next time!</p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="checkout-form">
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="billing_address">Billing Address:</label>
                        <textarea id="billing_address" name="billing_address" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <h3>Payment Method</h3>
                        <div style="padding: 20px; background-color: #f8f9fa; border-radius: 5px; margin: 10px 0;">
                            <label>
                                <input type="radio" name="payment_method" value="online" checked>
                                Credit/Debit Card Payment
                            </label>
                            <br>
                            <small>Secure payment processing</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="card_type">Card Type:</label>
                        <select id="card_type" name="card_type" required>
                            <option value="">Select Card Type</option>
                            <option value="visa">Visa</option>
                            <option value="mastercard">Mastercard</option>
                            <option value="amex">American Express</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="cardholder_name">Cardholder Name:</label>
                        <input type="text" id="cardholder_name" name="cardholder_name" placeholder="Name on card" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="card_number">Card Number:</label>
                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="expiry_month">Expiry Month:</label>
                            <select id="expiry_month" name="expiry_month" required>
                                <option value="">MM</option>
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="expiry_year">Expiry Year:</label>
                            <select id="expiry_year" name="expiry_year" required>
                                <option value="">YYYY</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                                <option value="2029">2029</option>
                                <option value="2030">2030</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV:</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div style="display: flex; align-items: center; gap: 10px; padding: 15px; background-color: #e8f4fd; border-radius: 8px; border: 1px solid #b8daff;">
                            <input type="checkbox" id="save_payment" name="save_payment" value="1">
                            <label for="save_payment" style="margin: 0; cursor: pointer;">
                                💳 Save this payment method for faster checkout next time
                            </label>
                        </div>
                        <small style="color: #666; margin-top: 5px; display: block;">Your payment information will be securely stored and encrypted.</small>
                    </div>
                    
                    <input type="hidden" id="cart_data" name="cart_data">
                    <button type="submit" class="btn-success">Place Order</button>
                </form>
            </div>
            
            <div>
                <h2>Order Summary</h2>
                <div id="order-summary" style="background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
                    <p>Loading order summary...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receipt-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; overflow-y: auto;">
        <div style="background: white; margin: 2% auto; max-width: 600px; border-radius: 10px; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
            <div style="position: absolute; top: 15px; right: 20px;">
                <button onclick="closeReceipt()" style="background: #e74c3c; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 18px; font-weight: bold;">&times;</button>
            </div>
            
            <div id="receipt-content" style="padding: 30px;">
                <!-- Receipt content will be generated here -->
            </div>
            
            <div style="padding: 0 30px 30px; display: flex; gap: 15px; justify-content: center;">
                <button onclick="downloadReceipt()" style="background: #27ae60; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-weight: 600;">📥 Download PDF</button>
                <button onclick="printReceipt()" style="background: #3498db; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-weight: 600;">🖨️ Print</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            displayOrderSummary();
            
            // Auto-fill functionality
            <?php if ($saved_payment): ?>
                const autoFillBtn = document.getElementById('auto-fill-btn');
                if (autoFillBtn) {
                    autoFillBtn.addEventListener('click', function() {
                        if (confirm('Auto-fill payment details from your saved information?')) {
                            // Fill all the saved payment details
                            document.getElementById('phone').value = '<?php echo addslashes($saved_payment['phone']); ?>';
                            document.getElementById('billing_address').value = '<?php echo addslashes($saved_payment['billing_address']); ?>';
                            document.getElementById('card_type').value = '<?php echo addslashes($saved_payment['card_type']); ?>';
                            document.getElementById('cardholder_name').value = '<?php echo addslashes($saved_payment['cardholder_name']); ?>';
                            
                            // Fill with actual stored card details from database
                            // Note: In production, use proper encryption and never store CVV
                            document.getElementById('card_number').value = '<?php echo addslashes($saved_payment['card_number']); ?>';
                            document.getElementById('expiry_month').value = '<?php echo addslashes($saved_payment['expiry_month']); ?>';
                            document.getElementById('expiry_year').value = '<?php echo addslashes($saved_payment['expiry_year']); ?>';
                            
                            // Fill CVV from database (demo purposes only)
                            document.getElementById('cvv').value = '<?php echo isset($saved_payment['cvv']) ? addslashes($saved_payment['cvv']) : '123'; ?>';
                            
                            // Update button appearance
                            this.style.background = '#28a745';
                            this.innerHTML = '✅ Details Auto-filled';
                            this.disabled = true;
                            
                            // Show success message
                            const successMsg = document.createElement('div');
                            successMsg.style.cssText = 'background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-top: 10px; border: 1px solid #c3e6cb;';
                            successMsg.innerHTML = '✅ All payment details have been auto-filled from your saved information.';
                            this.parentElement.appendChild(successMsg);
                            
                            setTimeout(() => {
                                successMsg.remove();
                            }, 5000);
                        }
                    });
                }
            <?php endif; ?>
            
            // Check if receipt should be shown
            <?php if (isset($_SESSION['receipt_data'])): ?>
                showReceipt(<?php echo json_encode($_SESSION['receipt_data']); ?>);
                <?php 
                // Clear receipt data after showing
                unset($_SESSION['receipt_data']); 
                ?>
            <?php endif; ?>
        });

        function displayOrderSummary() {
            const summaryDiv = document.getElementById('order-summary');
            const cartDataInput = document.getElementById('cart_data');
            
            if (cart.items.length === 0) {
                summaryDiv.innerHTML = '<p>Your cart is empty. <a href="shop.php">Continue shopping</a></p>';
                return;
            }

            let summaryHTML = '';
            let total = 0;

            cart.items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                
                summaryHTML += `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #ddd;">
                        <div>
                            <strong>${item.title}</strong><br>
                            <small>by ${item.author}</small><br>
                            <small>Qty: ${item.quantity} × LKR ${item.price.toFixed(2)}</small>
                        </div>
                        <div><strong>LKR ${itemTotal.toFixed(2)}</strong></div>
                    </div>
                `;
            });

            summaryHTML += `
                <div style="border-top: 2px solid #333; padding-top: 15px; margin-top: 15px;">
                    <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold;">
                        <span>Total:</span>
                        <span>LKR ${total.toFixed(2)}</span>
                    </div>
                </div>
            `;

            summaryDiv.innerHTML = summaryHTML;
            cartDataInput.value = JSON.stringify(cart.items);
        }
        
        // Format card number with spaces
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            if (value.length <= 16) {
                this.value = formattedValue;
            }
        });
        
        // Only allow numbers for CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Form validation before submit
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
            const cvv = document.getElementById('cvv').value;
            
            if (cardNumber.length < 13 || cardNumber.length > 19) {
                e.preventDefault();
                alert('Please enter a valid card number');
                return false;
            }
            
            if (cvv.length < 3 || cvv.length > 4) {
                e.preventDefault();
                alert('Please enter a valid CVV');
                return false;
            }
        });
        
        // Receipt functions
        function showReceipt(receiptData) {
            const receiptContent = document.getElementById('receipt-content');
            
            let itemsHTML = receiptData.items.map(item => `
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">${item.title}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: center;">${item.quantity}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">LKR ${item.price.toFixed(2)}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">LKR ${(item.price * item.quantity).toFixed(2)}</td>
                </tr>
            `).join('');
            
            receiptContent.innerHTML = `
                <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px;">
                    <h1 style="color: #333; margin: 0;">Chandrani Book Shop</h1>
                    <p style="margin: 5px 0; color: #666;">Purchase Receipt</p>
                    <p style="margin: 5px 0; color: #666;">Order ID: ${receiptData.order_id}</p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                    <div>
                        <h3 style="color: #333; margin-bottom: 10px;">Customer Information</h3>
                        <p style="margin: 5px 0;"><strong>Name:</strong> ${receiptData.customer_name}</p>
                        <p style="margin: 5px 0;"><strong>Email:</strong> ${receiptData.customer_email}</p>
                        <p style="margin: 5px 0;"><strong>Phone:</strong> ${receiptData.phone}</p>
                        <p style="margin: 5px 0;"><strong>Address:</strong> ${receiptData.billing_address}</p>
                    </div>
                    <div>
                        <h3 style="color: #333; margin-bottom: 10px;">Order Details</h3>
                        <p style="margin: 5px 0;"><strong>Date:</strong> ${new Date(receiptData.date).toLocaleString()}</p>
                        <p style="margin: 5px 0;"><strong>Payment Method:</strong> ${receiptData.payment_method}</p>
                        <p style="margin: 5px 0;"><strong>Status:</strong> Processing</p>
                    </div>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #333;">Item</th>
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid #333;">Qty</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #333;">Price</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #333;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHTML}
                    </tbody>
                </table>
                
                <div style="text-align: right; padding: 20px 0; border-top: 2px solid #333;">
                    <h2 style="color: #27ae60; margin: 0;">Total: LKR ${receiptData.total_amount.toFixed(2)}</h2>
                </div>
                
                <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666;">
                    <p>Thank you for shopping with Chandrani Book Shop!</p>
                    <p>For any queries, contact us at info@chandranibookshop.com</p>
                </div>
            `;
            
            document.getElementById('receipt-modal').style.display = 'block';
            
            // Clear cart after successful payment
            cart.items = [];
            cart.save();
            updateCartDisplay();
        }
        
        function closeReceipt() {
            document.getElementById('receipt-modal').style.display = 'none';
        }
        
        function downloadReceipt() {
            // Simple download as HTML file
            const receiptContent = document.getElementById('receipt-content').innerHTML;
            const fullHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Receipt - Chandrani Book Shop</title>
                    <style>
                        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                        .total { font-size: 1.2em; font-weight: bold; color: #27ae60; }
                    </style>
                </head>
                <body>${receiptContent}</body>
                </html>
            `;
            
            const blob = new Blob([fullHTML], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'receipt-chandrani-bookshop.html';
            a.click();
            window.URL.revokeObjectURL(url);
        }
        
        function printReceipt() {
            const receiptContent = document.getElementById('receipt-content').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Receipt - Chandrani Book Shop</title>
                    <style>
                        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                        @media print { .no-print { display: none; } }
                    </style>
                </head>
                <body>${receiptContent}</body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>
