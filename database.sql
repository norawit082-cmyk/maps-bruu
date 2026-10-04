CREATE DATABASE IF NOT EXISTS shoeshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shoeshop;
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(150) NOT NULL, brand VARCHAR(80) NOT NULL,
  category VARCHAR(50), sizes VARCHAR(100), color VARCHAR(50), price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0, description TEXT, image VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY, customer_name VARCHAR(120) NOT NULL, phone VARCHAR(20) NOT NULL,
  address TEXT NOT NULL, total DECIMAL(10,2) NOT NULL, status VARCHAR(30) NOT NULL DEFAULT 'รอชำระเงิน',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, product_id INT NOT NULL,
  product_name VARCHAR(150) NOT NULL, size VARCHAR(10) NOT NULL DEFAULT '', price DECIMAL(10,2) NOT NULL, qty INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE);
INSERT INTO products (name,brand,category,sizes,color,price,stock,description) VALUES
('Air Runner 2','Nike','วิ่ง','40,41,42,43','ขาว-ดำ',3290,12,'รองเท้าวิ่งน้ำหนักเบา พื้นนุ่ม'),
('Court Classic','Adidas','ผ้าใบ','38,39,40,41','ขาว',2490,8,'ทรงคลาสสิก ใส่ได้ทุกวัน');
