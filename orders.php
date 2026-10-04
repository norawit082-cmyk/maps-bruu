<?php require 'header.php';
$sts = ['รอชำระเงิน', 'ชำระแล้ว', 'จัดส่งแล้ว', 'ยกเลิก'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['status'] ?? '', $sts, true)) {
  $pdo->prepare("UPDATE orders SET status=:s WHERE id=:id")->execute([':s' => $_POST['status'], ':id' => (int)$_POST['id']]);
  header('Location: orders.php'); exit; }
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll(); $it = $pdo->prepare("SELECT * FROM order_items WHERE order_id=:id"); ?>
<h2 class="fw-bold mb-3">คำสั่งซื้อทั้งหมด</h2>
<?php foreach ($orders as $o): $it->execute([':id' => $o['id']]); ?><div class="bg-white p-3 rounded-3 shadow-sm mb-3">
<div class="d-flex justify-content-between flex-wrap gap-2"><div><b>#<?= $o['id'] ?></b> · <?= e($o['customer_name']) ?> · <?= e($o['phone']) ?><br>
<small class="text-muted"><?= e($o['address']) ?> · <?= e($o['created_at']) ?></small></div>
<form method="post" class="d-flex gap-2 align-items-start"><input type="hidden" name="id" value="<?= $o['id'] ?>">
<select name="status" class="form-select form-select-sm"><?php foreach ($sts as $s): ?><option <?= $o['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select>
<button class="btn btn-sm btn-dark">บันทึก</button></form></div>
<ul class="mb-1 mt-2"><?php foreach ($it as $r): ?><li><?= e($r['product_name']) ?> <?= $r['size']?'(ไซส์ '.e($r['size']).')':'' ?> × <?= $r['qty'] ?></li><?php endforeach; ?></ul>
<div class="text-end fw-bold">รวม <?= baht($o['total']) ?></div></div><?php endforeach; ?>
<?php if (!$orders): ?><p class="text-muted text-center">ยังไม่มีคำสั่งซื้อ</p><?php endif; ?>
<?php require 'footer.php'; ?>
