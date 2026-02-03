<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, name, description, price, category, image_path FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
    echo json_encode($products);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load products']);
}
?>