<?php
session_start();
require_once __DIR__ . '/../db/connection.php';

// Check admin authentication
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

header('Content-Type: application/json');

$product_id = (int)($_GET['id'] ?? 0);

if (!$product_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.id, 
            p.name, 
            p.description, 
            p.price, 
            p.image_path, 
            p.category,
            p.created_at,
            p.updated_at,
            i.quantity
        FROM products p
        LEFT JOIN inventory i ON p.id = i.product_id
        WHERE p.id = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
