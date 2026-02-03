<?php
require_once 'db/connection.php';

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name, description, price, image_path FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    if ($product) {
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load product']);
}
?>