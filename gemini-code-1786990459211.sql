USE `myshop_[รหัสนักศึกษา]`;

-- แบบที่ 1: ดูข้อมูลทั้งหมด (SELECT All)
SELECT * FROM products;

-- แบบที่ 2: กรองข้อมูลด้วย WHERE (เช่น ราคาสินค้ามากกว่า 1,000 บาท)
SELECT * FROM products 
WHERE price > 1000;

-- แบบที่ 3: ค้นหาข้อมูลด้วย LIKE (เช่น ค้นหาสินค้าที่มีคำว่า "เมาส์")
SELECT * FROM products 
WHERE name LIKE '%เมาส์%';

-- แบบที่ 4: เรียงลำดับด้วย ORDER BY (เช่น เรียงราคาสินค้าจากมากไปน้อย)
SELECT * FROM products 
ORDER BY price DESC;