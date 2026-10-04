<?php
require 'header.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php'); exit;
}

$product_ids = array_unique(array_column($cart, 'id'));
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$st = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$st->execute($product_ids);
$products = $st->fetchAll(PDO::FETCH_UNIQUE);

$total_price = 0;
$cart_items = [];

foreach ($cart as $item) {
    $p = $products[$item['id']] ?? null;
    if ($p) {
        $subtotal = $p['price'] * $item['qty'];
        $total_price += $subtotal;
        $cart_items[] = [
            'product' => $p,
            'qty' => $item['qty'],
            'size' => $item['size'],
            'subtotal' => $subtotal
        ];
    }
}
?>

<div class="container my-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-credit-card-fill"></i> ชำระเงิน & กรอกที่อยู่จัดส่ง</h2>

    <!-- เพิ่ม enctype="multipart/form-data" เพื่อให้แนบไฟล์สลิปได้ -->
    <form action="save_order.php" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <div class="col-md-7">
                <!-- ส่วนที่ 1: กรอกที่อยู่ -->
                <div class="card p-4 border-0 shadow-sm rounded-3 bg-white mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-danger"></i> ข้อมูลผู้รับสินค้า</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ชื่อ-นามสกุล *</label>
                        <input type="text" name="customer_name" class="form-control" required placeholder="นาย สมชาย ใจดี">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">เบอร์โทรศัพท์ *</label>
                        <input type="tel" name="phone" class="form-control" required placeholder="0812345678">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ที่อยู่จัดส่งสินค้าอย่างละเอียด *</label>
                        <textarea name="address" class="form-control" rows="3" required placeholder="บ้านเลขที่ ซอย ถนน ตำบล/แขวง อำเภอ/เขต จังหวัด รหัสไปรษณีย์"></textarea>
                    </div>
                </div>

                <!-- ส่วนที่ 2: ช่องทางการชำระเงิน -->
                <div class="card p-4 border-0 shadow-sm rounded-3 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-qr-code"></i> ชำระเงินผ่านการโอนธนาคาร</h5>
                    <div class="alert alert-light border rounded-3 p-3 mb-3">
                        <div class="fw-bold text-primary">ธนาคารกสิกรไทย (KBANK)</div>
                        <div>เลขที่บัญชี: <strong>123-4-56789-0</strong></div>
                        <div>ชื่อบัญชี: <strong>บจก. โซล สตรีท (SOLE STREET)</strong></div>
                        <div class="mt-2 text-danger fw-bold">ยอดที่ต้องโอน: <?= baht($total_price) ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">แนบหลักฐานการโอนเงิน (สลิป/Slip) *</label>
                        <input type="file" name="payment_slip" class="form-control" accept="image/*" required>
                        <small class="text-muted">รองรับไฟล์ JPG, PNG, WEBP ขนาดไม่เกิน 2MB</small>
                    </div>
                </div>
            </div>

            <!-- สรุปรายการสั่งซื้อ -->
            <div class="col-md-5">
                <div class="card p-4 border-0 shadow-sm rounded-3 bg-white sticky-top" style="top: 20px;">
                    <h5 class="fw-bold mb-3">สรุปรายการสั่งซื้อ</h5>
                    <ul class="list-group list-group-flush mb-3">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                <div>
                                    <div class="fw-bold"><?= e($item['product']['name']) ?></div>
                                    <small class="text-muted">
                                        <?= $item['size'] ? 'ไซส์: '.e($item['size']).' | ' : '' ?>จำนวน: <?= $item['qty'] ?>
                                    </small>
                                </div>
                                <span class="fw-bold"><?= baht($item['subtotal']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fs-5 fw-bold">ยอดชำระสุทธิ:</span>
                        <span class="fs-4 text-danger fw-bold"><?= baht($total_price) ?></span>
                    </div>
                    <button type="submit" class="btn btn-sig btn-lg w-100 fw-bold">
                        <i class="bi bi-check-circle-fill me-1"></i> ยืนยันการสั่งซื้อและชำระเงิน
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require 'footer.php'; ?>