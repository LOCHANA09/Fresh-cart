CREATE DATABASE IF NOT EXISTS freshcart_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE freshcart_db;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  category VARCHAR(60) NOT NULL,
  description VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  emoji VARCHAR(10) NOT NULL DEFAULT '🛒',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(120) NOT NULL,
  phone VARCHAR(25) NOT NULL,
  address TEXT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(120) NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO products (name, category, description, price, emoji) VALUES
('Fresh Tomatoes','Vegetables','Juicy everyday cooking tomatoes.',35.00,'🍅'),
('Carrots','Vegetables','Crunchy fresh carrots.',50.00,'🥕'),
('Bananas','Fruits','Naturally sweet ripe bananas.',45.00,'🍌'),
('Apples','Fruits','Crisp apples for a healthy snack.',140.00,'🍎'),
('Milk 500 ml','Dairy','Everyday pasteurised milk.',32.00,'🥛'),
('Brown Bread','Bakery','Soft sliced bread for breakfast.',45.00,'🍞'),
('Rice 1 kg','Essentials','Daily-use rice pack.',65.00,'🍚'),
('Potatoes','Vegetables','Versatile kitchen potatoes.',30.00,'🥔');
