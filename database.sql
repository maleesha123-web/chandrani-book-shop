-- Create database
CREATE DATABASE IF NOT EXISTS chandrani_bookshop;
USE chandrani_bookshop;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'customer') DEFAULT 'customer',
    profile_picture VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    category VARCHAR(50) DEFAULT 'General',
    cover_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table (for online orders)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    qty INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    billing_address TEXT,
    phone VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- Supplier orders table
CREATE TABLE IF NOT EXISTS supplier_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    supplier_name VARCHAR(100) NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Offline orders table
CREATE TABLE IF NOT EXISTS offline_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    contact VARCHAR(100) NOT NULL,
    book_title VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payment details table
CREATE TABLE IF NOT EXISTS payment_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    card_type VARCHAR(20) NOT NULL,
    cardholder_name VARCHAR(100) NOT NULL,
    card_number VARCHAR(20) NOT NULL,
    expiry_month VARCHAR(2) NOT NULL,
    expiry_year VARCHAR(4) NOT NULL,
    cvv VARCHAR(4) NOT NULL,
    billing_address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert admin user (email: adminbook@gmail.com, password: adminbook)
INSERT INTO users (name, email, password, user_type) VALUES 
('Admin', 'adminbook@gmail.com', '$2y$10$7Q8X9KtKjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Alternative method: Create admin with plain password that will be hashed by PHP
-- DELETE FROM users WHERE email = 'adminbook@gmail.com';
-- INSERT INTO users (name, email, password, user_type) VALUES ('Admin', 'adminbook@gmail.com', 'adminbook', 'admin');

-- Add cover_image column to existing books table (run this if updating existing database)
-- ALTER TABLE books ADD COLUMN cover_image VARCHAR(255) DEFAULT NULL;

-- Insert sample books with categories
INSERT INTO books (title, author, price, stock, category) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 2999.00, 25, 'Classic Literature'),
('To Kill a Mockingbird', 'Harper Lee', 3499.00, 30, 'Classic Literature'),
('1984', 'George Orwell', 3999.00, 20, 'Dystopian Fiction'),
('Pride and Prejudice', 'Jane Austen', 2799.00, 35, 'Romance'),
('The Catcher in the Rye', 'J.D. Salinger', 3299.00, 15, 'Coming of Age'),
('Lord of the Rings', 'J.R.R. Tolkien', 5999.00, 18, 'Fantasy'),
('Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', 4499.00, 40, 'Fantasy'),
('The Hobbit', 'J.R.R. Tolkien', 3799.00, 22, 'Fantasy'),
('One Hundred Years of Solitude', 'Gabriel García Márquez', 4299.00, 12, 'Magical Realism'),
('Brave New World', 'Aldous Huxley', 3599.00, 28, 'Dystopian Fiction'),
('The Chronicles of Narnia', 'C.S. Lewis', 6999.00, 16, 'Fantasy'),
('Jane Eyre', 'Charlotte Brontë', 3199.00, 20, 'Gothic Romance'),
('Wuthering Heights', 'Emily Brontë', 2999.00, 15, 'Gothic Romance'),
('The Adventures of Sherlock Holmes', 'Arthur Conan Doyle', 2599.00, 30, 'Mystery'),
('Crime and Punishment', 'Fyodor Dostoevsky', 4599.00, 14, 'Psychological Fiction'),
('The Da Vinci Code', 'Dan Brown', 3899.00, 28, 'Thriller'),
('Angels and Demons', 'Dan Brown', 3799.00, 22, 'Thriller'),
('The Alchemist', 'Paulo Coelho', 2499.00, 35, 'Philosophy'),
('Life of Pi', 'Yann Martel', 3399.00, 18, 'Adventure'),
('The Kite Runner', 'Khaled Hosseini', 3699.00, 25, 'Historical Fiction');

-- Insert sample customer users
INSERT INTO users (name, email, password, user_type) VALUES 
('John Smith', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('Jane Doe', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('Alice Johnson', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Insert sample offline orders
INSERT INTO offline_orders (customer_name, contact, book_title, quantity) VALUES 
('Robert Wilson', '9876543210', 'The Da Vinci Code', 2),
('Sarah Brown', 'sarah.brown@email.com', 'Angels and Demons', 1),
('Michael Davis', '9876543211', 'Digital Fortress', 3);

-- Insert sample supplier orders
INSERT INTO supplier_orders (book_title, author, quantity, supplier_name) VALUES 
('The Alchemist', 'Paulo Coelho', 50, 'Penguin Random House'),
('Life of Pi', 'Yann Martel', 30, 'HarperCollins Publishers'),
('The Kite Runner', 'Khaled Hosseini', 25, 'Penguin Random House');

-- Create feedback table for customer reviews
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_email VARCHAR(255) NOT NULL,
    customer_name VARCHAR(255),
    description TEXT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
);
