<?php
require __DIR__ . '/config.php';
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_input(); $id = filter_var($data['add_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) json_response(['error' => 'Invalid product.'], 400);
    $stmt = $pdo->prepare('SELECT id FROM products WHERE id=?'); $stmt->execute([$id]);
    if (!$stmt->fetch()) json_response(['error' => 'Product not found.'], 404);
    $_SESSION['cart'][$id] = min(99, (int)($_SESSION['cart'][$id] ?? 0) + 1);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_input(); $id = filter_var($data['remove_id'] ?? null, FILTER_VALIDATE_INT);
    if ($id) unset($_SESSION['cart'][$id]);
} elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['error' => 'Method not allowed.'], 405);
}
$items = []; $total = 0.0;
if ($_SESSION['cart']) {
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id,name,price FROM products WHERE id IN ($marks)"); $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $p) {
        $p['quantity'] = (int)$_SESSION['cart'][$p['id']];
        $p['line_total'] = $p['quantity'] * (float)$p['price'];
        $total += $p['line_total']; $items[] = $p;
    }
}
json_response(['items' => $items, 'total' => $total, 'count' => array_sum($_SESSION['cart'])]);
