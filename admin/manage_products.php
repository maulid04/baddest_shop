<?php
session_start();
require_once __DIR__ . '/../db/connection.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        if ($product_id > 0) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
        }
        http_response_code(204);
        exit;
    }

    $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;
    $category = trim($_POST['category'] ?? '');

    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'assets/images/' . $filename;
        }
    }

    if ($product_id) {
        if ($image_path) {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_path = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $category, $image_path, $product_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $category, $product_id]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $category, $image_path]);
        $new_id = $pdo->lastInsertId();
        if ($new_id) {
            $pdo->prepare("INSERT INTO inventory (product_id, quantity) VALUES (?, 0)")->execute([$new_id]);
        }
    }

    header('Location: manage_products.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Baddest Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_products.php" class="active">Manage Products</a></li>
                <li><a href="manage_inventory.php">Manage Inventory</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="sales_reports.php">Sales Reports</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1>Manage Products</h1>
            <button class="btn-primary" onclick="showAddForm()">Add New Product</button>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="products-table">
                </tbody>
            </table>

            <div id="product-form" style="display: none; margin-top: 2rem; background: #f8f8f8; padding: 1rem; border-radius: 8px;"> 
                <h2 id="form-title">Add Product</h2>
                <form action="manage_products.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" id="product_id" name="product_id">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" step="0.01" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <input type="text" id="category" name="category">
                    </div>
                    <div class="form-group">
                        <label for="image">Image:</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    <button type="submit" class="btn-primary">Save</button>
                    <button type="button" class="btn-secondary" onclick="hideForm()">Cancel</button>
                </form>
            </div>
        </main>
    </div>

    <script src="../assets/js/scripts.js"></script>
    <script>
        let products = [];

        async function loadProducts() {
            try {
                const response = await fetch('get_products.php');
                products = await response.json();
                displayProducts(products);
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }

        function displayProducts(products) {
            const tbody = document.getElementById('products-table');
            tbody.innerHTML = '';

            products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>$${product.price}</td>
                    <td>${product.category}</td>
                    <td>
                        <a href="#" onclick="editProduct(${product.id})">Edit</a> |
                        <a href="#" onclick="deleteProduct(${product.id})">Delete</a>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function showAddForm() {
            document.getElementById('form-title').textContent = 'Add Product';
            document.getElementById('product_id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('price').value = '';
            document.getElementById('category').value = '';
            document.getElementById('product-form').style.display = 'block';
        }

        function editProduct(id) {
            const product = products.find(p => p.id == id);
            if (product) {
                document.getElementById('form-title').textContent = 'Edit Product';
                document.getElementById('product_id').value = product.id;
                document.getElementById('name').value = product.name;
                document.getElementById('description').value = product.description;
                document.getElementById('price').value = product.price;
                document.getElementById('category').value = product.category;
                document.getElementById('product-form').style.display = 'block';
            }
        }

        function hideForm() {
            document.getElementById('product-form').style.display = 'none';
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                fetch('manage_products.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete&product_id=${id}`
                }).then(() => loadProducts());
            }
        }

        document.addEventListener('DOMContentLoaded', loadProducts);
    </script>
</body>
