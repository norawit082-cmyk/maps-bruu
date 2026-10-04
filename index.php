<?php require 'header.php';
$q = trim($_GET['q'] ?? '');
$st = $pdo->prepare("SELECT * FROM products WHERE name LIKE :q1 OR brand LIKE :q2 ORDER BY id DESC");
$st->execute([':q1' => "%$q%", ':q2' => "%$q%"]); $rows = $st->fetchAll(); ?>
<section class="hero"><div><span class="hero-tag">NEW ARRIVALS</span>
  <h1>STEP INTO<br>YOUR STYLE</h1><p>สนีกเกอร์ รองเท้าวิ่ง ผ้าใบ คัดมาแล้วทุกคู่</p>
  <a href="#list" class="btn btn-sig btn-lg">เลือกซื้อเลย <i class="bi bi-arrow-down"></i></a></div>
  <i class="bi bi-lightning-charge-fill hero-bolt"></i></section>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3" id="list">
  <h2 class="fw-bold m-0">รองเท้าทั้งหมด</h2>
  <form class="d-flex gap-2"><input name="q" class="form-control" value="<?= e($q) ?>" placeholder="ค้นหาชื่อ / ยี่ห้อ"><button class="btn btn-dark">ค้นหา</button></form></div>
<div class="row g-3">
<?php foreach ($rows as $p): ?>
  <div class="col-6 col-md-4 col-lg-3"><div class="shoe-card h-100">
    <a href="product.php?id=<?= $p['id'] ?>" class="shoe-img">
      <?php if ($p['image']): ?><img src="uploads/<?= e($p['image']) ?>" alt=""><?php else: ?><i class="bi bi-bag"></i><?php endif; ?>
      <?php if ($p['stock'] < 1): ?><span class="sold">สินค้าหมด</span><?php elseif ($p['stock'] <= 3): ?><span class="low">เหลือ <?= $p['stock'] ?> คู่</span><?php endif; ?></a>
    <div class="p-3"><span class="tag"><?= e($p['brand']) ?></span><h3 class="shoe-name"><?= e($p['name']) ?></h3>
      <div class="d-flex justify-content-between align-items-center mt-2"><span class="shoe-price"><?= baht($p['price']) ?></span>
        <a class="btn btn-sig btn-sm <?= $p['stock']<1?'disabled':'' ?>" href="product.php?id=<?= $p['id'] ?>"><i class="bi bi-cart-plus"></i> สั่งซื้อ</a></div></div>
  </div></div>
<?php endforeach; if (!$rows): ?><p class="text-center text-muted">ไม่พบสินค้า</p><?php endif; ?></div>
<?php require 'footer.php'; ?>
