<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = new PDO("mysql:host=localhost;dbname=shoeshop;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);

function e($s) { 
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); 
}

function baht($n) { 
    return '฿' . number_format($n, 2); 
}

function cart_count() {
    $count = 0;
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            if (is_array($item)) {
                $count += (int)($item['qty'] ?? 1);
            } elseif (is_numeric($item)) {
                $count += (int)$item;
            }
        }
    }
    return $count;
}

function upload_image($f) {
    if (empty($_FILES[$f]['tmp_name'])) return null;
    $x = $_FILES[$f];
    if ($x['size'] > 2*1024*1024) throw new Exception('ไฟล์ต้องไม่เกิน 2 MB');
    $ext = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'][mime_content_type($x['tmp_name'])] ?? null;
    if (!$ext) throw new Exception('รองรับเฉพาะ JPG, PNG, WEBP');
    $n = bin2hex(random_bytes(8)) . "." . $ext;
    move_uploaded_file($x['tmp_name'], __DIR__ . "/uploads/$n");
    return $n;
}

function rm_image($n) { 
    if ($n) @unlink(__DIR__ . '/uploads/' . basename($n)); 
}