<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT report_date, total_sales, total_orders FROM sales_reports ORDER BY report_date DESC");
    $reports = $stmt->fetchAll();
    echo json_encode($reports);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load reports']);
}
?>