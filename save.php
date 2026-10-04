<?php require 'header.php';
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0); $p = [];
if ($id) { $st = $pdo->prepare("SELECT * FROM products WHERE id=:id"); $st->execute([':id' => $id]); $p = $st->fetch();
  if (!$p) { echo '<div class="alert alert-warning">ไม่พบสินค้า</div>'; require 'footer.php'; exit; } }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $d = [':name' => trim($_POST['name']), ':brand' => trim($_POST['brand']), ':category' => trim($_POST['category']),
      ':sizes' => trim($_POST['sizes']), ':color' => trim($_POST['color']), ':price' => $_POST['price'],
      ':stock' => (int)$_POST['stock'], ':description' => trim($_POST['description'])];
    if ($d[':name'] === '' || $d[':brand'] === '') throw new Exception('กรอกชื่อและยี่ห้อ');
    if (!is_numeric($d[':price']) || $d[':price'] < 0 || $d[':stock'] < 0) throw new Exception('ราคา/จำนวนไม่ถูกต้อง');
    $img = upload_image('image');
    if ($id) {
      $sql = "UPDATE products SET name=:name,brand=:brand,category=:category,sizes=:sizes,color=:color,price=:price,stock=:stock,description=:description".($img?",image=:image":"")." WHERE id=:id";
      $d[':id'] = $id; if ($img) { $d[':image'] = $img; rm_image($p['image']); }
    } else {
      $sql = "INSERT INTO products (name,brand,category,sizes,color,price,stock,description,image) VALUES (:name,:brand,:category,:sizes,:color,:price,:stock,:description,:image)";
      $d[':image'] = $img;
    }
    $pdo->prepare($sql)->execute($d);
    header('Location: products.php?msg='.urlencode($id ? 'แก้ไขแล้ว' : 'เพิ่มสินค้าแล้ว')); exit;
  } catch (Exception $ex) { $error = $ex->getMessage(); $p = array_merge($p, $_POST); }
}
$v = fn($k, $d = '') => e($p[$k] ?? $d); ?>
<h2 class="fw-bold mb-3"><?= $id ? 'แก้ไขสินค้า' : 'เพิ่มสินค้า' ?></h2>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if (!empty($p['image'])): ?><img class="mini mb-2" style="width:120px;height:120px" src="uploads/<?= e($p['image']) ?>"><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded-3 shadow-sm" style="max-width:640px"><input type="hidden" name="id" value="<?= $id ?>">
<div class="mb-3"><label class="form-label">ชื่อสินค้า</label><input name="name" class="form-control" required value="<?= $v('name') ?>"></div>
<div class="row"><div class="col mb-3"><label class="form-label">ยี่ห้อ</label><input name="brand" class="form-control" required value="<?= $v('brand') ?>"></div>
<div class="col mb-3"><label class="form-label">ประเภท</label><input name="category" class="form-control" placeholder="วิ่ง / ผ้าใบ" value="<?= $v('category') ?>"></div></div>
<div class="row"><div class="col mb-3"><label class="form-label">ไซส์ (คั่นด้วย , )</label><input name="sizes" class="form-control" placeholder="39,40,41" value="<?= $v('sizes') ?>"></div>
<div class="col mb-3"><label class="form-label">สี</label><input name="color" class="form-control" value="<?= $v('color') ?>"></div></div>
<div class="row"><div class="col mb-3"><label class="form-label">ราคา (บาท)</label><input type="number" step="0.01" min="0" name="price" class="form-control" required value="<?= $v('price') ?>"></div>
<div class="col mb-3"><label class="form-label">คงเหลือ</label><input type="number" min="0" name="stock" class="form-control" required value="<?= $v('stock','0') ?>"></div></div>
<div class="mb-3"><label class="form-label">รายละเอียด</label><textarea name="description" rows="4" class="form-control"><?= $v('description') ?></textarea></div>
<div class="mb-3"><label class="form-label">รูปภาพ (JPG/PNG/WEBP ≤ 2MB)</label><input type="file" name="image" accept="image/*" class="form-control"></div>
<button class="btn btn-sig btn-lg">บันทึก</button> <a href="products.php" class="btn btn-outline-dark btn-lg">ยกเลิก</a></form>
<?php require 'footer.php'; ?>
