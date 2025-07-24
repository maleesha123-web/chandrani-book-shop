// Banner/Slideshow functionality
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    let currentSlide = 0;

    if (slides.length > 0) {
        // Show first slide initially
        slides[0].classList.add('active');

        // Auto slideshow
        setInterval(function() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 4000); // Change slide every 4 seconds
    }
});

// Cart functionality using localStorage
class Cart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('cart')) || [];
    }

    addItem(book) {
        const existingItem = this.items.find(item => item.id === book.id);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({
                id: book.id,
                title: book.title,
                author: book.author,
                price: book.price,
                quantity: 1
            });
        }
        this.save();
        this.updateCartDisplay();
        
        // Show success message with option to go to cart
        const message = `✅ "${book.title}" has been added to your cart!\n\n🛒 Click OK to continue shopping or Cancel to view your cart.`;
        if (confirm(message)) {
            // User wants to continue shopping
            return;
        } else {
            // User wants to go to cart
            window.location.href = 'cart.php';
        }
    }

    removeItem(bookId) {
        this.items = this.items.filter(item => item.id !== bookId);
        this.save();
        this.updateCartDisplay();
    }

    updateQuantity(bookId, quantity) {
        const item = this.items.find(item => item.id === bookId);
        if (item) {
            item.quantity = Math.max(0, quantity);
            if (item.quantity === 0) {
                this.removeItem(bookId);
            } else {
                this.save();
                this.updateCartDisplay();
            }
        }
    }

    getTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    save() {
        localStorage.setItem('cart', JSON.stringify(this.items));
    }

    clear() {
        this.items = [];
        this.save();
        this.updateCartDisplay();
    }

    updateCartDisplay() {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            const totalItems = this.items.reduce((total, item) => total + item.quantity, 0);
            cartCount.textContent = totalItems;
        }
    }
}

// Initialize cart
const cart = new Cart();

// Add to cart functionality
function addToCart(bookId, title, author, price) {
    const book = {
        id: bookId,
        title: title,
        author: author,
        price: parseFloat(price)
    };
    cart.addItem(book);
}

// Update cart display on page load
document.addEventListener('DOMContentLoaded', function() {
    if (typeof cart !== 'undefined') {
        cart.updateCartDisplay();
    }
});

// Form validation
function validateRegistration() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return false;
    }
    
    if (password.length < 6) {
        alert('Password must be at least 6 characters long!');
        return false;
    }
    
    return true;
}

// Print functionality for reports
function printReport() {
    window.print();
}
