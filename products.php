<?php
require __DIR__ . '/config.php';
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? 'All');
$sql = 'SELECT id,name,category,description,price,emoji FROM products WHERE (name LIKE :q OR description LIKE :q)';
$params = [':q' => '%' . $q . '%'];
if ($category !== 'All') { $sql .= ' AND category = :category'; $params[':category'] = $category; }
$sql .= ' ORDER BY category,name';
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$categories = $pdo->query('SELECT DISTINCT category FROM products ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
json_response(['products' => $stmt->fetchAll(), 'categories' => $categories]);
