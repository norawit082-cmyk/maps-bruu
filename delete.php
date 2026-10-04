<?php require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)$_POST['id']; $s = $pdo->prepare("SELECT image FROM products WHERE id=:id"); $s->execute([':id' => $id]);
  if ($r = $s->fetch()) { $pdo->prepare("DELETE FROM products WHERE id=:id")->execute([':id' => $id]); rm_image($r['image']); }
}
header('Location: products.php?msg='.urlencode('ลบสินค้าแล้ว')); exit;
