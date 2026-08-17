-- 1. สร้างฐานข้อมูลและตาราง (schema.sql)
-- ** อย่าลืมเปลี่ยน [รหัสนักศึกษา] เป็นรหัสจริง เช่น myshop_6601234567 **

CREATE DATABASE IF NOT EXISTS `myshop_[รหัสนักศึกษา]` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `myshop_[รหัสนักศึกษา]`;

-- สร้างตาราง products (สินค้า)
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `category` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- สร้างตาราง members (สมาชิก)
CREATE TABLE `members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- สร้างตาราง orders (คำสั่งซื้อ) พร้อมเชื่อม Foreign Key
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `member_id` INT NOT NULL,
  `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `total` DECIMAL(10,2) NOT NULL,
  CONSTRAINT `fk_orders_members` 
    FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
