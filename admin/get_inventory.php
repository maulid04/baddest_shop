<?php
session_start();
require_once __DIR__ . '/../db/connection.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

// Ensure specific newly added products have correct starting quantities
// without overwriting existing non-zero quantities.
$fixedQuantities = [
    'system case'   => 85,
    'television'    => 73,
    'digital camera'=> 44,
];

try {
    // For each named product, insert or update inventory only if currently missing or zero
    $getProductStmt = $pdo->prepare("SELECT id FROM products WHERE name = ? LIMIT 1");
    $getInventoryStmt = $pdo->prepare("SELECT quantity FROM inventory WHERE product_id = ? LIMIT 1");
    $insertInventoryStmt = $pdo->prepare("INSERT INTO inventory (product_id, quantity) VALUES (?, ?)");
    $updateInventoryStmt = $pdo->prepare("UPDATE inventory SET quantity = ? WHERE product_id = ?");

    foreach ($fixedQuantities as $productName => $desiredQty) {
        $getProductStmt->execute([$productName]);
        $product = $getProductStmt->fetch();
        if ($product && !empty($product['id'])) {
            $productId = (int)$product['id'];
            $getInventoryStmt->execute([$productId]);
            $inv = $getInventoryStmt->fetch();
            if ($inv === false) {
                // No inventory row: insert with desired quantity
                $insertInventoryStmt->execute([$productId, $desiredQty]);
            } else {
                $currentQty = (int)$inv['quantity'];
                if ($currentQty === 0) {
                    // Only update if current quantity is zero to avoid overwriting real counts
                    $updateInventoryStmt->execute([$desiredQty, $productId]);
                }
            }
        }
    }

    // Now return the full inventory (joined with product names)
    $stmt = $pdo->query("SELECT i.product_id, p.name, i.quantity FROM inventory i JOIN products p ON i.product_id = p.id ORDER BY p.id");
    $inventory = $stmt->fetchAll();
    echo json_encode($inventory);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load inventory']);
}
