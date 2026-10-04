<?php require 'header.php';
$st = $pdo->prepare("SELECT * FROM products WHERE id=:id"); $st->execute([':id' => (int)($_GET['id'] ?? 0)]); $p = $st->fetch();
if (!$p) { echo '<div class="alert alert-warning">ไม่พบสินค้า</div>'; require 'footer.php'; exit; }
$sizes = array_values(array_filter(array_map('trim', explode(',', $p['sizes'])))); ?>
<div class="row g-4"><div class="col-md-6"><div class="detail-img">
  <?php if ($p['image']): ?><img src="uploads/<?= e($p['image']) ?>" alt=""><?php else: ?><i class="bi bi-bag"></i><?php endif; ?></div></div>
<div class="col-md-6"><span class="tag"><?= e($p['brand']) ?></span> <span class="tag alt"><?= e($p['category']) ?></span>
  <h1 class="h2 fw-bold mt-2"><?= e($p['name']) ?></h1><div class="shoe-price fs-2"><?= baht($p['price']) ?></div>
  <p class="text-muted mb-1">สี: <?= e($p['color'] ?: '-') ?> · คงเหลือ <?= $p['stock'] ?> คู่</p><p><?= nl2br(e($p['description'])) ?></p>
  <?php if ($p['stock'] > 0): ?>
  <form method="post" action="cart.php"><input type="hidden" name="add" value="<?= $p['id'] ?>">
    <?php if ($sizes): ?><div class="fw-semibold mb-2">เลือกไซส์</div><div class="size-group mb-3">
      <?php foreach ($sizes as $i => $s): ?><input type="radio" class="btn-check" name="size" id="s<?= $i ?>" value="<?= e($s) ?>" required>
      <label class="size-chip" for="s<?= $i ?>"><?= e($s) ?></label><?php endforeach; ?></div><?php endif; ?>
    <div class="mb-3" style="max-width:140px"><label class="fw-semibold">จำนวน</label>
      <input type="number" name="qty" value="1" min="1" max="<?= $p['stock'] ?>" class="form-control"></div>
    <button class="btn btn-sig btn-lg"><i class="bi bi-bag-plus-fill"></i> ใส่ตะกร้า</button>
    <a href="index.php" class="btn btn-outline-dark btn-lg">กลับ</a></form>
  <?php else: ?><div class="alert alert-secondary">สินค้าหมดชั่วคราว</div><?php endif; ?></div></div>
<?php require 'footer.php'; ?>
