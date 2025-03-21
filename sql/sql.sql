-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS shoebiz_db;
USE shoevibe_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    customization_data JSON NOT NULL DEFAULT '{}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    customization_data JSON NOT NULL DEFAULT '{}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the products table
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255) NOT NULL
);

-- Insert sample data into the products table*4////////***-
INSERT INTO products (product_name, price, image_url) VALUES
('Nike Air Max', 5999.99, '../image/s1.png'),
('Customizable Air-Force Zeros Low Top', 4000.00, '../image/s2.png'),
('Customizable Premium Synthetic Leather Shoes', 3999.00, '../image/s3.png'),
('Customizable High-Top Synthetic Leather Sneakers', 4500.00, '../image/16.png');
('Customizable Lightweight Breathable Running Sneakers', 4200.00, '../image/16.png');
('Customizable Eco Vegan Leather Boots', 500.00, '../image/16.png');



-- Create the orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    size VARCHAR(10) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL,
    barangay VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    province VARCHAR(255) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);


-- create the reviews table
CREATE TABLE reviews (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    rating INT(11) NOT NULL,
    review_title VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    review TEXT COLLATE utf8mb4_general_ci NOT NULL,
    image VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shoe2_reviews (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    rating INT(11) NOT NULL,
    review_title VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    review TEXT COLLATE utf8mb4_general_ci NOT NULL,
    image VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shoe3_reviews (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    rating INT(11) NOT NULL,
    review_title VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    review TEXT COLLATE utf8mb4_general_ci NOT NULL,
    image VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shoe4_reviews (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    rating INT(11) NOT NULL,
    review_title VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    review TEXT COLLATE utf8mb4_general_ci NOT NULL,
    image VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shoe5_reviews (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    rating INT(11) NOT NULL,
    review_title VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    review TEXT COLLATE utf8mb4_general_ci NOT NULL,
    image VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shoe6_reviews (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    rating INT(11) NOT NULL,
    review_title VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    review TEXT COLLATE utf8mb4_general_ci NOT NULL,
    image VARCHAR(255) COLLATE utf8mb4_general_ci NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert default admin user with password 'Admin123' (stored in plain text)
INSERT INTO admin (username, password) VALUES 
('admin123', 'Admin123');
