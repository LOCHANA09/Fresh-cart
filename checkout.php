<?php
require __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed.'], 405);
if (empty($_SESSION['cart'])) json_response(['error' => 'Your cart is empty.'], 400);
$data = json_input();
$name = trim($data['customer_name'] ?? '');
$phone = trim($data['phone'] ?? '');
$address = trim($data['address'] ?? '');
if ($name === '' || mb_strlen($name) > 120 || $phone === '' || mb_strlen($phone) > 25 || $address === '') json_response(['error' => 'Please enter valid delivery details.'], 400);
$ids = array_map('intval', array_keys($_SESSION['cart']));
$marks = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($marks)"); $stmt->execute($ids); $products = $stmt->fetchAll();
$total = 0.0;
foreach ($products as $p) $total += (float)$p['price'] * (int)$_SESSION['cart'][$p['id']];
try {
    $pdo->beginTransaction();
    $order = $pdo->prepare('INSERT INTO orders (customer_name,phone,address,total) VALUES (?,?,?,?)');
    $order->execute([$name,$phone,$address,$total]); $orderId = (int)$pdo->lastInsertId();
    $line = $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_name,quantity,unit_price) VALUES (?,?,?,?,?)');
    foreach ($products as $p) $line->execute([$orderId,$p['id'],$p['name'],(int)$_SESSION['cart'][$p['id']],$p['price']]);
    $pdo->commit(); $_SESSION['cart'] = [];
    json_response(['order_id'=>$orderId,'customer_name'=>$name,'total'=>$total]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(['error'=>'Could not save the order. Check the database tables.'], 500);
}
