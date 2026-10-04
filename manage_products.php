<?php
require_once 'config.php';
require_once 'header.php';

// ดึงข้อมูลสินค้าทั้งหมดจากฐานข้อมูล
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-list-task"></i> จัดการสินค้า</h2>
        <a href="add_product.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> เพิ่มสินค้าใหม่</a>
    </div>

    <div class="table-responsive bg-white shadow-sm rounded p-3">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>รูปภาพ</th>
                    <th>ชื่อสินค้า</th>
                    <th>ราคา</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">ยังไม่มีข้อมูลสินค้า</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= e($p['id']) ?></td>
                            <td>
                                <?php if (!empty($p['image'])): ?>
                                    <img src="uploads/<?= e($p['image']) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                <?php else: ?>
                                    <span class="badge bg-secondary">ไม่มีรูป</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($p['name'] ?? $p['title'] ?? '') ?></td>
                            <td><?= baht($p['price'] ?? 0) ?></td>
                            <td>
                                <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> แก้ไข</a>
                                <a href="delete_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบสินค้า?')"><i class="bi bi-trash"></i> ลบ</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap 5 JavaScript Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>