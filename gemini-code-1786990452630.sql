USE `myshop_[รหัสนักศึกษา]`;

-- เพิ่มข้อมูลสินค้า 8 รายการ
INSERT INTO `products` (`name`, `price`, `stock`, `category`) VALUES
('เสื้อยืดคอกลม สีขาว', 250.00, 50, 'Clothing'),
('กางเกงยีนส์ทรงกระบอก', 890.00, 20, 'Clothing'),
('หูฟังไร้สาย Bluetooth', 1290.00, 15, 'Electronics'),
('เมาส์เกมมิ่ง RGB', 750.00, 30, 'Electronics'),
('คีย์บอร์ดกลไก Mechanical', 2490.00, 10, 'Electronics'),
('รองเท้าผ้าใบ Sneaker', 1590.00, 25, 'Shoes'),
('กระเป๋าเป้เดินทาง', 990.00, 40, 'Bags'),
('แก้วน้ำเก็บความเย็น', 350.00, 100, 'Home & Living');

-- เพิ่มข้อมูลสมาชิก 3 คน
INSERT INTO `members` (`username`, `password`, `email`) VALUES
('somchai_a', 'pass1234', 'somchai@email.com'),
('saree_b', 'pass5678', 'saree@email.com'),
('ananda_c', 'pass9012', 'ananda@email.com');

-- เพิ่มข้อมูลคำสั่งซื้อตัวอย่าง
INSERT INTO `orders` (`member_id`, `total`) VALUES
(1, 1540.00),
(2, 2490.00),
(1, 350.00);