-- Baddest Shop Inventory Management System Database
-- MySQL dump

CREATE DATABASE IF NOT EXISTS baddest_inventory;
USE baddest_inventory;

-- Roles table
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inventory table
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL UNIQUE,
    quantity INT NOT NULL DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Sales reports table (for daily summaries)
CREATE TABLE sales_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    report_date DATE NOT NULL UNIQUE,
    total_sales DECIMAL(10,2) DEFAULT 0,
    total_orders INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data

-- Roles
INSERT INTO roles (name) VALUES ('customer'), ('admin');

-- Users
INSERT INTO users (username, email, password_hash, role_id, first_name, last_name, phone, address) VALUES
('admin', 'admin@baddestshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Admin', 'User', '123-456-7890', '123 Admin St'),
('customer1', 'customer1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'John', 'Doe', '987-654-3210', '456 Customer Ave'),
('customer2', 'customer2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Jane', 'Smith', '555-123-4567', '789 Shop Blvd');

-- Products
INSERT INTO products (name, description, price, image_path, category) VALUES
('Laptop', 'High-performance laptop for work and gaming', 999.99, 'assets/images/product1.jpg', 'Electronics'),
('Mouse', 'Wireless ergonomic mouse', 29.99, 'assets/images/product2.jpg', 'Accessories'),
('Keyboard', 'Mechanical gaming keyboard', 79.99, 'assets/images/product3.jpg', 'Accessories'),
('Monitor', '27-inch 4K monitor', 349.99, 'assets/images/product4.jpg', 'Electronics'),
('Headphones', 'Noise-cancelling wireless headphones', 199.99, 'assets/images/product5.jpg', 'Audio');

-- Inventory
INSERT INTO inventory (product_id, quantity) VALUES
(1, 50), (2, 100), (3, 75), (4, 25), (5, 60);

-- Sample order
INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES
(2, 1029.98, 'delivered', '456 Customer Ave');

INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 999.99), (1, 2, 1, 29.99);

-- Sales report
INSERT INTO sales_reports (report_date, total_sales, total_orders) VALUES
('2023-01-01', 1029.98, 1);