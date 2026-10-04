<?php
require 'header.php';

// 1. จัดการการเพิ่มสินค้าเข้าตะกร้าแบบ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['add'] ?? $_POST['product_id'] ?? $_POST['id'] ?? 0);
    $qty = max(1, (int)($_POST['qty'] ?? $_POST['quantity'] ?? 1));
    $size = trim($_POST['size'] ?? '');

    if ($id > 0) {
        $cartKey = $id . ($size ? "_$size" : '');
        
        if (!isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey] = ['id' => $id, 'qty' => 0, 'size' => $size];
        }
        $_SESSION['cart'][$cartKey]['qty'] += $qty;
        
        header('Location: cart.php');
        exit;
    }
}

// 2. จัดการการเพิ่ม/ลด/ลบ รายการแบบ GET
$action = $_GET['action'] ?? '';
$key = $_GET['key'] ?? '';

if ($action === 'add' && isset($_SESSION['cart'][$key])) {
    $_SESSION['cart'][$key]['qty']++;
    header('Location: cart.php'); exit;
}
if ($action === 'reduce' && isset($_SESSION['cart'][$key])) {
    $_SESSION['cart'][$key]['qty']--;
    if ($_SESSION['cart'][$key]['qty'] <= 0) unset($_SESSION['cart'][$key]);
    header('Location: cart.php'); exit;
}
if ($action === 'remove' && isset($_SESSION['cart'][$key])) {
    unset($_SESSION['cart'][$key]);
    header('Location: cart.php'); exit;
}

// 3. ดึงข้อมูลสินค้ามาแสดงผล
$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$total_price = 0;

if (!empty($cart)) {
    $product_ids = [];
    foreach ($cart as $k => $v) {
        $product_ids[] = is_array($v) ? (int)$v['id'] : (int)$k;
    }
    $product_ids = array_unique(array_filter($product_ids));

    if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $st = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $st->execute($product_ids);
        $products = $st->fetchAll(PDO::FETCH_UNIQUE);

        foreach ($cart as $key => $item) {
            $p_id = is_array($item) ? $item['id'] : $key;
            $qty = is_array($item) ? $item['qty'] : $item;
            $size = is_array($item) ? ($item['size'] ?? '') : '';

            if (isset($products[$p_id])) {
                $p = $products[$p_id];
                $subtotal = $p['price'] * $qty;
                $total_price += $subtotal;

                $cart_items[] = [
                    'key' => $key,
                    'product' => $p,
                    'qty' => $qty,
                    'size' => $size,
                    'subtotal' => $subtotal
                ];
            }
        }
    }
}
?>

<div class="container my-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-cart-fill"></i> ตะกร้าสินค้า</h2>

    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info text-center py-5 rounded-3 shadow-sm">
            <h4 class="mb-3">ไม่มีสินค้าในตะกร้า</h4>
            <a href="index.php" class="btn btn-primary">กลับไปเลือกซื้อสินค้า</a>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40%;">สินค้า</th>
                            <th class="text-center">ราคา</th>
                            <th class="text-center" style="width: 150px;">จำนวน</th>
                            <th class="text-end">รวม</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($item['product']['image'])): ?>
                                            <img src="uploads/<?= e($item['product']['image']) ?>" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold"><?= e($item['product']['name']) ?></div>
                                            <?php if (!empty($item['size'])): ?>
                                                <small class="text-muted">ไซส์: <?= e($item['size']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><?= baht($item['product']['price']) ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="cart.php?action=reduce&key=<?= urlencode($item['key']) ?>" class="btn btn-outline-secondary">-</a>
                                        <span class="btn btn-light px-3 disabled text-dark fw-bold"><?= $item['qty'] ?></span>
                                        <a href="cart.php?action=add&key=<?= urlencode($item['key']) ?>" class="btn btn-outline-secondary">+</a>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-danger"><?= baht($item['subtotal']) ?></td>
                                <td class="text-center">
                                    <a href="cart.php?action=remove&key=<?= urlencode($item['key']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบรายการนี้?')">
                                        <i class="bi bi-trash"></i> ลบ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 p-4 bg-light">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h4 class="fw-bold mb-3 mb-md-0">ราคารวมทั้งหมด: <span class="text-danger"><?= baht($total_price) ?></span></h4>
                <div>
                    <a href="index.php" class="btn btn-outline-secondary me-2">เลือกซื้อสินค้าต่อ</a>
                    <a href="checkout.php" class="btn btn-success btn-lg fw-bold">ไปที่หน้าชำระเงิน</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require 'footer.php'; ?>