# Chandrani Book Shop Website

A complete bookshop management system with customer and admin interfaces built using PHP, MySQL, HTML, CSS, and JavaScript.

## Features

### Customer Features
- **User Registration & Login**: Secure user authentication system
- **Browse Books**: View available books with details and categories
- **Search & Filter**: Search books by title/author and filter by category
- **Shopping Cart**: Add books to cart with localStorage persistence
- **Online Checkout**: Complete purchase with billing information
- **Payment Details**: Save payment information for future purchases
- **User Profile**: Update personal information and view saved payments
- **Responsive Design**: Modern, mobile-friendly interface
- **LKR Pricing**: All prices displayed in Sri Lankan Rupees

### Admin Features
- **Admin Dashboard**: Overview of sales, inventory, and customers
- **Sales Reporting**: View and export sales data
- **Inventory Management**: Add new books to the system
- **Supplier Orders**: Place orders for new books from suppliers
- **Offline Orders**: Record in-store customer orders
- **Receipt Generation**: Print receipts for offline orders

## Technology Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **Styling**: Custom CSS with responsive design
- **Session Management**: PHP sessions for authentication

## Installation & Setup

### Prerequisites
- XAMPP/WAMP/LAMP server
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser

### Steps

1. **Start your local server** (Apache & MySQL)

2. **Copy the project files** to your web server directory:
   ```
   xampp/htdocs/chandrani-book-shop/  (for XAMPP)
   ```

3. **Create the database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the `database.sql` file to create the database and tables
   - Or run the SQL commands manually in phpMyAdmin

4. **Configure database connection**:
   - Update `includes/db.php` if needed (default settings work with XAMPP)
   ```php
   $host = 'localhost';
   $dbname = 'chandrani_bookshop';
   $username = 'root';
   $password = '';
   ```

5. **Access the website**:
   - Open your browser and go to: `http://localhost/chandrani-book-shop`

## Default Login Credentials

### Admin Access
- **Email**: adminbook@gmail.com
- **Password**: adminbook

### Sample Customer Accounts
- **Email**: john@example.com, **Password**: secret
- **Email**: jane@example.com, **Password**: secret

*Note: All sample customer passwords are "secret" (hashed in database)*

## Project Structure

```
chandrani-book-shop/
│
├── css/
│   └── style.css              # Main stylesheet
├── js/
│   └── script.js              # Client-side JavaScript
├── includes/
│   ├── db.php                 # Database connection
│   └── functions.php          # Helper functions
├── home.php                   # Landing page
├── shop.php                   # Books catalog with search/filter
├── cart.php                   # Shopping cart
├── checkout.php               # Checkout process
├── profile.php                # User profile management
├── about.php                  # About us page
├── contact.php                # Contact page with form
├── login.php                  # User login
├── register.php               # User registration
├── logout.php                 # Logout handler
├── admin_dashboard.php        # Admin main page
├── sales_report.php           # Sales reporting
├── order_book_supplier.php    # Supplier orders
├── offline_orders.php         # Offline order management
├── add_book.php               # Add new books (AJAX)
├── database.sql               # Database schema
└── README.md                  # This file
```

## Database Schema

### Tables
- **users**: Store user accounts (customers and admin)
- **books**: Book inventory with titles, authors, prices, and stock
- **orders**: Online customer orders
- **supplier_orders**: Orders placed with book suppliers
- **offline_orders**: In-store customer orders

## Usage Guide

### For Customers
1. Visit the homepage to browse the bookshop
2. Register a new account or login
3. Browse books in the Shop section
4. Add books to your cart
5. Proceed to checkout to complete purchase

### For Admin
1. Login with admin credentials
2. Use the dashboard to view statistics
3. Manage inventory by adding new books
4. View sales reports and export data
5. Record offline orders from in-store customers
6. Place orders with suppliers for new inventory

## Key Features Implementation

### Shopping Cart
- Uses JavaScript localStorage for persistence
- Maintains cart across browser sessions
- Real-time cart updates and quantity management

### Authentication
- Secure password hashing using PHP's password_hash()
- Session-based user management
- Role-based access control (admin/customer)

### Admin Dashboard
- Real-time statistics and analytics
- Comprehensive reporting system
- Multi-functional management interface

### Responsive Design
- Mobile-friendly interface
- Cross-browser compatibility
- Clean and modern UI

## Customization

### Adding New Features
1. Create new PHP files for additional pages
2. Update navigation in existing files
3. Add corresponding database tables if needed
4. Update the CSS for styling

### Styling
- Modify `css/style.css` for appearance changes
- All colors and layouts are defined in CSS variables
- Responsive breakpoints are included

### Database
- Add new fields to existing tables as needed
- Create migration scripts for production deployments
- Update the includes/db.php for different environments

## Troubleshooting

### Common Issues
1. **Database connection failed**: Check MySQL is running and credentials are correct
2. **Session errors**: Ensure PHP sessions are enabled
3. **File permissions**: Make sure web server can read all files
4. **JavaScript errors**: Check browser console for client-side issues

### Development Tips
- Enable PHP error reporting during development
- Use browser developer tools for debugging
- Test with different user roles and scenarios
- Validate all forms and inputs

## Security Features
- SQL injection prevention using prepared statements
- XSS protection with htmlspecialchars()
- Password hashing for user authentication
- Session management for secure access control

## Future Enhancements
- Payment gateway integration
- Email notifications for orders
- Book search and filtering
- Customer order history
- Advanced reporting and analytics
- Multi-language support

## Support
For issues or questions about this project, please refer to the code comments or create detailed bug reports with:
- PHP and MySQL versions
- Browser information
- Error messages
- Steps to reproduce the issue

---

**Project**: Chandrani Book Shop Website  
**Version**: 1.0  
**Last Updated**: January 2024  
**License**: Educational/Personal Use
