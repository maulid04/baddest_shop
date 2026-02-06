<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT 
            o.id,
            o.total_amount,
            o.status,
            o.created_at,
            u.username
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
    ");
    
    $orders = $stmt->fetchAll();
    echo json_encode($orders);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load sales data', 'message' => $e->getMessage()]);
}
?>