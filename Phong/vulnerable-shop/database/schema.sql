-- Xóa database nếu đã tồn tại
DROP DATABASE IF EXISTS vulnerable_shop;
-- Tạo database
CREATE DATABASE vulnerable_shop;
-- Sử dụng database
USE vulnerable_shop;

-- Bảng users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    avatar VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    balance DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng products
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Bảng comments
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    user_id INT,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Bảng transactions
CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    amount DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    type ENUM('purchase', 'deposit') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Thêm dữ liệu mẫu

-- Bảng users
-- Mật khẩu được hash bằng MD5 cho đơn giản (ví dụ: 'password' -> '5f4dcc3b5aa765d61d8327deb882cf99')
INSERT INTO users (username, email, password, full_name, role, balance) VALUES
('admin', 'admin@example.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'Admin User', 'admin', 1000.00),
('user1', 'user1@example.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'Regular User', 'user', 500.00);

-- Bảng products
INSERT INTO products (name, description, price, image, created_by) VALUES
('Laptop', 'High-performance laptop', 999.99, 'laptop.jpg', 1),
('Smartphone', 'Latest model smartphone', 699.99, 'smartphone.jpg', 1),
('Headphones', 'Wireless noise-canceling headphones', 199.99, 'headphones.jpg', 1);

-- Bảng comments
INSERT INTO comments (product_id, user_id, content) VALUES
(1, 2, 'Great laptop, worth the price!'),
(1, 1, 'Admin comment for testing.'),
(2, 2, 'Amazing phone, love the camera.');

-- Bảng transactions
INSERT INTO transactions (user_id, product_id, amount, type) VALUES
(2, 1, 999.99, 'purchase'),
(2, 3, 199.99, 'purchase'),
(1, NULL, 500.00, 'deposit');