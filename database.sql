-- Tiffin Route database (import in phpMyAdmin -> Import)
CREATE DATABASE IF NOT EXISTS tiffin_route CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tiffin_route;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  phone VARCHAR(15) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  category ENUM('Breakfast','Meals','Curries','Beverages','Desserts') NOT NULL,
  description VARCHAR(255) DEFAULT '',
  price DECIMAL(8,2) NOT NULL,
  emoji VARCHAR(8) DEFAULT '🍽️',
  status ENUM('active','draft') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_code VARCHAR(20) DEFAULT NULL,
  user_id INT NOT NULL,
  customer_name VARCHAR(100) NOT NULL,
  address TEXT NOT NULL,
  payment_method VARCHAR(30) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  tax DECIMAL(10,2) NOT NULL,
  delivery_fee DECIMAL(10,2) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  status ENUM('Pending','Accepted','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  item_id INT NULL,
  item_name VARCHAR(100) NOT NULL,
  price DECIMAL(8,2) NOT NULL,
  qty INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO menu_items (name, category, description, price, emoji, status) VALUES
('Ghee Podi Idli (4 pcs)','Breakfast','Steamed rice cakes tossed in gunpowder podi and ghee, served with sambar & coconut chutney.',120,'🍘','active'),
('Masala Dosa','Breakfast','Crisp golden dosa stuffed with spiced potato masala.',110,'🥞','active'),
('Pongal & Vada','Breakfast','Creamy ghee pongal with a crunchy medu vada.',95,'🍚','active'),
('South Indian Thali','Meals','Rice, sambar, rasam, two poriyals, curd, appalam and sweet.',180,'🍛','active'),
('Curd Rice Bowl','Meals','Cooling curd rice tempered with mustard and curry leaves.',90,'🥣','active'),
('Lemon Rice Combo','Meals','Tangy lemon rice with potato fry and papad.',100,'🍋','active'),
('Chettinad Chicken Curry','Curries','Slow-cooked chicken in a fiery Chettinad-style masala.',230,'🍲','active'),
('Kathirikai Gothsu','Curries','Tangy brinjal & tamarind curry, a classic tiffin side.',95,'🍆','active'),
('Filter Coffee','Beverages','Strong South Indian decoction coffee with frothy milk.',40,'☕','draft'),
('Rose Milk','Beverages','Chilled rose-flavoured milk.',50,'🥛','active'),
('Payasam','Desserts','Warm vermicelli payasam with cashews and raisins.',70,'🍮','active'),
('Kesari','Desserts','Saffron-coloured semolina sweet with ghee.',60,'🍥','active');

-- Admin account is created by setup_admin.php (see README) -> admin@tiffinroute.com / admin123
