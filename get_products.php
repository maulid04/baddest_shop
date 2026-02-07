<?php
require_once 'db/connection.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, name, price, image_path FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll();
    echo json_encode($products);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load products']);
}
?>