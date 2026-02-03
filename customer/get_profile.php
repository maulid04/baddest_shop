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
    $stmt = $pdo->prepare("SELECT username, email, first_name, last_name, phone, address FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch();
    echo json_encode($profile);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load profile']);
}
?>