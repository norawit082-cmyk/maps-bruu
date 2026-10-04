<?php
require 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
    $name = trim($_POST['customer_name'] ?? $_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? $_POST['customer_phone'] ?? '');
    $address = trim($_POST['address'] ?? $_POST['customer_address'] ?? '');

    $cart = $_SESSION['cart'];

    // 1. ดึง ID สินค้าออกมาอย่างแม่นยำ
    $product_ids = [];
    foreach ($cart as $key => $value) {
        $id = 0;
        if (is_array($value) && !empty($value['id'])) {
            $id = (int)$value['id'];
        } elseif (is_numeric($key)) {
            $id = (int)$key;
        } else {
            $parts = explode('_', (string)$key);
            $id = (int)$parts[0];
        }

        if ($id > 0) {
            $product_ids[] = $id;
        }
    }
    $product_ids = array_unique($product_ids);

    if (empty($product_ids)) {
        echo "<script>alert('ไม่มีรายการสินค้าในตะกร้า'); window.location.href='index.php';</script>";
        exit;
    }

    // 2. ดึงข้อมูลสินค้าจากตาราง products
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $st = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $st->execute($product_ids);
    $products = $st->fetchAll(PDO::FETCH_UNIQUE);

    // 3. เตรียมข้อมูลรายการสั่งซื้อ
    $total_price = 0;
    $order_items_data = [];

    foreach ($cart as $key => $item) {
        $p_id = 0;
        if (is_array($item) && !empty($item['id'])) {
            $p_id = (int)$item['id'];
        } elseif (is_numeric($key)) {
            $p_id = (int)$key;
        } else {
            $parts = explode('_', (string)$key);
            $p_id = (int)$parts[0];
        }

        $qty = is_array($item) ? (int)($item['qty'] ?? 1) : (int)$item;
        $size = is_array($item) ? ($item['size'] ?? '') : '';

        // ต้องมีสินค้าในฐานข้อมูล และ p_id ต้องไม่เป็น 0 หรือ null
        if ($p_id > 0 && isset($products[$p_id])) {
            $p = $products[$p_id];
            $subtotal = $p['price'] * $qty;
            $total_price += $subtotal;

            $order_items_data[] = [
                'product_id'   => (int)$p['id'],
                'product_name' => $p['name'],
                'size'         => $size,
                'price'        => $p['price'],
                'qty'          => $qty
            ];
        }
    }

    if (empty($order_items_data)) {
        echo "<script>alert('เกิดข้อผิดพลาดกับข้อมูลสินค้า กรุณาเลือกสินค้าใหม่อีกครั้ง'); window.location.href='cart.php';</script>";
        exit;
    }

    // 4. จัดการอัปโหลดสลิป
    $slip_image = '';
    if (isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
        $slip_image = upload_image('payment_slip');
    }

    try {
        $pdo->beginTransaction();

        // 5. บันทึกลงตาราง orders
        $st_order = $pdo->prepare("INSERT INTO orders (customer_name, phone, address, total, payment_slip, status) VALUES (?, ?, ?, ?, ?, 'รอตรวจสอบการชำระเงิน')");
        $st_order->execute([$name, $phone, $address, $total_price, $slip_image]);
        $order_id = $pdo->lastInsertId();

        // 6. บันทึกลงตาราง order_items และตัดสต็อก
        $st_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, size, price, qty) VALUES (?, ?, ?, ?, ?, ?)");
        $st_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($order_items_data as $item) {
            $st_item->execute([
                $order_id,
                $item['product_id'],
                $item['product_name'],
                $item['size'],
                $item['price'],
                $item['qty']
            ]);

            // ตัดสต็อก
            $st_stock->execute([$item['qty'], $item['product_id']]);
        }

        $pdo->commit();

        // เคลียร์ตะกร้าสินค้า
        unset($_SESSION['cart']);

        // ไปหน้าใบเสร็จ
        header("Location: receipt.php?order_id=$order_id");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกคำสั่งซื้อ: " . addslashes($e->getMessage()) . "'); history.back();</script>";
        exit;
    }
}

header('Location: index.php');
exit;