<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

// คำนวณจำนวนสินค้าในตะกร้าแบบปลอดภัย
$cart_count = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item)) {
            $cart_count += (int)($item['qty'] ?? 1);
        } elseif (is_numeric($item)) {
            $cart_count += (int)$item;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOLE STREET</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="index.php"><i class="bi bi-lightning-charge-fill"></i> SOLE STREET</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <a class="nav-link" href="products.php"><i class="bi bi-list-task"></i> จัดการสินค้า</a>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> หน้าแรก</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="manage_products.php"><i class="bi bi-list-task"></i> จัดการสินค้า</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="orders.php"><i class="bi bi-receipt"></i> คำสั่งซื้อ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="sales_report.php"><i class="bi bi-bar-chart-line"></i> รายงานยอดขาย</a>
        </li>
      </ul>
      
      <a href="cart.php" class="btn btn-outline-light position-relative">
        <i class="bi bi-bag-fill"></i> ตะกร้า
        <?php if ($cart_count > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $cart_count ?>
          </span>
        <?php endif; ?>
      </a>
    </div>
  </div>
</nav>