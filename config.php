<?php
session_start();
$host = 'localhost';
$dbname = 'freshcart_db';
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Database connection failed. Import database.sql and check php/config.php.']);
    exit;
}
function json_input(): array {
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}
function json_response(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
