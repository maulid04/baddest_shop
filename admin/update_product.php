<?php
session_start();
require_once __DIR__ . '/../db/connection.php';

header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

/**
 * Updates a product in the database with secure validation and image handling
 *
 * @param PDO $pdo Database connection
 * @param int $product_id Product ID to update
 * @param array $data Product data (name, description, price, category)
 * @param array $file Image file data from $_FILES['image']
 * @return array Response array with success status and message
 */
function updateProduct($pdo, $product_id, $data, $file = null) {
    $response = [
        'success' => false,
        'message' => ''
    ];

    // Validate product ID
    $product_id = (int)$product_id;
    if ($product_id <= 0) {
        $response['message'] = 'Invalid product ID';
        return $response;
    }

    // Get current product to fetch old image path
    $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        $response['message'] = 'Product not found';
        return $response;
    }

    // Validate required fields
    $name = trim($data['name'] ?? '');
    $description = trim($data['description'] ?? '');
    $price = (float)($data['price'] ?? 0);
    $category = trim($data['category'] ?? '');

    if (empty($name)) {
        $response['message'] = 'Product name is required';
        return $response;
    }

    if ($price <= 0) {
        $response['message'] = 'Product price must be greater than 0';
        return $response;
    }

    // Handle image upload if provided
    $image_path = null;
    $old_image_path = $product['image_path'];

    if ($file && $file['error'] === UPLOAD_ERR_OK && $file['size'] > 0) {
        // Validate image file
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $response['message'] = 'Invalid image format. Allowed types: JPG, PNG, GIF, WebP';
            return $response;
        }

        // Check file size (max 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $max_size) {
            $response['message'] = 'Image file is too large. Maximum size is 5MB';
            return $response;
        }

        // Create upload directory if it doesn't exist
        $upload_dir = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $response['message'] = 'Failed to create image directory';
                return $response;
            }
        }

        // Generate unique filename
        $filename = uniqid('product_', true) . '.' . $file_extension;
        $target_file = $upload_dir . $filename;

        // Move uploaded file to target location
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $image_path = 'assets/images/' . $filename;

            // Delete old image if it exists
            if (!empty($old_image_path) && file_exists($upload_dir . basename($old_image_path))) {
                @unlink($upload_dir . basename($old_image_path));
            }
        } else {
            $response['message'] = 'Failed to upload image file';
            return $response;
        }
    }

    // Update product in database
    try {
        if ($image_path) {
            // Update with new image
            $stmt = $pdo->prepare("
                UPDATE products 
                SET name = ?, description = ?, price = ?, category = ?, image_path = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $success = $stmt->execute([$name, $description, $price, $category, $image_path, $product_id]);
        } else {
            // Update without changing image
            $stmt = $pdo->prepare("
                UPDATE products 
                SET name = ?, description = ?, price = ?, category = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $success = $stmt->execute([$name, $description, $price, $category, $product_id]);
        }

        if ($success) {
            $response['success'] = true;
            $response['message'] = 'Product updated successfully';
            $response['product_id'] = $product_id;
        } else {
            $response['message'] = 'Failed to update product in database';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }

    return $response;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $data = [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'price' => $_POST['price'] ?? 0,
        'category' => $_POST['category'] ?? ''
    ];

    $file = $_FILES['image'] ?? null;

    $response = updateProduct($pdo, $product_id, $data, $file);
    echo json_encode($response);
    exit;
}

// If not POST request
http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
?>
