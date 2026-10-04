<?php require 'header.php'; $rows = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(); ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h2 class="fw-bold m-0">จัดการสินค้า (<?= count($rows) ?>)</h2>
<a href="save.php" class="btn btn-sig"><i class="bi bi-plus-lg"></i> เพิ่มสินค้า</a></div>
<?php if (isset($_GET['msg'])): ?><div class="alert alert-success"><?= e($_GET['msg']) ?></div><?php endif; ?>
<div class="table-responsive bg-white rounded-3 shadow-sm"><table class="table align-middle mb-0">
<tr><th>รูป</th><th>ชื่อ</th><th>ยี่ห้อ</th><th>ไซส์</th><th>ราคา</th><th>คงเหลือ</th><th></th></tr>
<?php foreach ($rows as $p): ?><tr><td><?php if ($p['image']): ?><img class="mini" src="uploads/<?= e($p['image']) ?>"><?php endif; ?></td>
<td><?= e($p['name']) ?></td><td><?= e($p['brand']) ?></td><td><?= e($p['sizes']) ?></td><td><?= baht($p['price']) ?></td><td><?= $p['stock'] ?></td>
<td class="text-nowrap"><a class="btn btn-sm btn-dark" href="save.php?id=<?= $p['id'] ?>"><i class="bi bi-pencil"></i> แก้ไข</a>
<form method="post" action="delete.php" class="d-inline" onsubmit="return confirm('ลบสินค้านี้?')"><input type="hidden" name="id" value="<?= $p['id'] ?>">
<button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> ลบ</button></form></td></tr><?php endforeach; ?></table></div>
<?php require 'footer.php'; ?>
