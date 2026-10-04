<?php
require 'header.php';

$order_id = (int)($_GET['order_id'] ?? 0);

if ($order_id <= 0) {
    header('Location: index.php');
    exit;
}

// 1. ดึงข้อมูลคำสั่งซื้อด้วย $pdo
$st = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$st->execute([$order_id]);
$order = $st->fetch();

if (!$order) {
    echo "<div class='container my-5'><div class='alert alert-danger text-center'>ไม่พบรายการสั่งซื้อ</div></div>";
    require 'footer.php';
    exit;
}

// 2. ดึงรายการสินค้าในคำสั่งซื้อนี้
$st_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$st_items->execute([$order_id]);
$items = $st_items->fetchAll();
?>

<div class="container my-5">
    <div class="card shadow border-0 rounded-3 max-w-75 mx-auto">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-success"><i class="bi bi-check-circle-fill"></i> สั่งซื้อเรียบร้อยแล้ว</h2>
                <p class="text-muted">ขอบคุณที่ร่วมสั่งซื้อสินค้ากับ Sole Street</p>
            </div>

            <hr class="my-4">

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold text-secondary">ข้อมูลคำสั่งซื้อ</h6>
                    <div><strong>เลขที่สั่งซื้อ:</strong> #<?= sprintf('%05d', $order['id']) ?></div>
                    <div><strong>วันที่สั่งซื้อ:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'] ?? 'now')) ?></div>
                    <div><strong>สถานะ:</strong> <span class="badge bg-warning text-dark"><?= e($order['status'] ?? 'รอตรวจสอบ') ?></span></div>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-secondary">ข้อมูลผู้สั่งซื้อ</h6>
                    <div><strong>ชื่อ:</strong> <?= e($order['customer_name']) ?></div>
                    <div><strong>เบอร์โทรศัพท์:</strong> <?= e($order['phone']) ?></div>
                    <div><strong>ที่อยู่จัดส่ง:</strong> <?= nl2br(e($order['address'])) ?></div>
                </div>
            </div>

            <h6 class="fw-bold text-secondary mb-3">รายการสินค้า</h6>
            <div class="table-responsive mb-4">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>สินค้า</th>
                            <th class="text-center">ไซส์</th>
                            <th class="text-center">ราคา</th>
                            <th class="text-center">จำนวน</th>
                            <th class="text-end">รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= e($item['product_name']) ?></td>
                                <td class="text-center"><?= e($item['size'] ?: '-') ?></td>
                                <td class="text-center"><?= baht($item['price']) ?></td>
                                <td class="text-center"><?= $item['qty'] ?></td>
                                <td class="text-end fw-bold"><?= baht($item['price'] * $item['qty']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold fs-5">ยอดรวมสุทธิ:</td>
                            <td class="text-end fw-bold fs-5 text-danger"><?= baht($order['total']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary px-4"><i class="bi bi-house-door"></i> กลับหน้าหลัก</a>
                <button onclick="window.print()" class="btn btn-outline-secondary px-4 ms-2"><i class="bi bi-printer"></i> พิมพ์ใบเสร็จ</button>
            </div>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>