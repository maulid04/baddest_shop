<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
    echo json_encode($orders);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load orders']);
}
?>