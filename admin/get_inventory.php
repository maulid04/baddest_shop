<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT i.product_id, p.name, i.quantity FROM inventory i JOIN products p ON i.product_id = p.id ORDER BY p.id");
    $inventory = $stmt->fetchAll();
    echo json_encode($inventory);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load inventory']);
}
?>