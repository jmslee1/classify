DROP DATABASE IF EXISTS classify_db;
CREATE DATABASE classify_db;
USE classify_db;

-- 1. Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Categories Table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL,
    parent_category_id INT DEFAULT NULL,
    FOREIGN KEY (parent_category_id) REFERENCES categories(category_id)
);

-- 3. Ads Table (Updated with listing_type for Haves/Wants)
CREATE TABLE ads (
    ad_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    post_title VARCHAR(100) NOT NULL,
    post_detail TEXT,
    price DECIMAL(10, 2) NOT NULL,
    listing_type ENUM('OFFER', 'WANTED', 'EXCHANGE') DEFAULT 'OFFER', -- NEW COLUMN
    create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- 4. Images Table
CREATE TABLE images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_main_image BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (ad_id) REFERENCES ads(ad_id) ON DELETE CASCADE
);

-- 5. Pre-fill some Categories (Optional but helpful)
INSERT INTO categories (category_name) VALUES 
('Textbooks'), ('Electronics'), ('Furniture'), ('Clothing'), ('Services'), ('Vehicles');