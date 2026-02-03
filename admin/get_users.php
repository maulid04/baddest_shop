<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT u.id, u.username, u.email, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id");
    $users = $stmt->fetchAll();
    echo json_encode($users);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load users']);
}
?>