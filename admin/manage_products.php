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
            // Get product image path to delete the file
            $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if ($product && !empty($product['image_path'])) {
                $image_file = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $product['image_path'];
                if (file_exists($image_file)) {
                    @unlink($image_file);
                }
            }
            
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
        }
        http_response_code(204);
        exit;
    }

    // Handle product creation (add new product)
    $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    
    if (!$product_id) {
        // Create new product
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $category = trim($_POST['category'] ?? '');

        if (empty($name) || $price <= 0) {
            header('Location: manage_products.php');
            exit;
        }

        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $filename = uniqid('product_', true) . '.' . strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $target_file = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'assets/images/' . $filename;
            }
        }

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
                <div id="form-message" style="display: none; margin-bottom: 1rem; padding: 0.75rem; border-radius: 4px;"></div>
                <form id="product-form-element" enctype="multipart/form-data">
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
                        <small style="display: block; margin-top: 0.25rem; color: #666;">Max file size: 5MB. Allowed formats: JPG, PNG, GIF, WebP</small>
                    </div>
                    <button type="submit" class="btn-primary" id="save-btn">Save</button>
                    <button type="button" class="btn-secondary" onclick="hideForm()">Cancel</button>
                </form>
            </div>
        </main>
    </div>

    <script src="../assets/js/scripts.js"></script>
    <script>
        let products = [];
        let isAddingProduct = false;

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
                    <td>${product.category || 'N/A'}</td>
                    <td>
                        <a href="#" onclick="editProduct(${product.id}); return false;">Edit</a> |
                        <a href="#" onclick="deleteProduct(${product.id}); return false;">Delete</a>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function showAddForm() {
            isAddingProduct = true;
            document.getElementById('form-title').textContent = 'Add Product';
            document.getElementById('product_id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('price').value = '';
            document.getElementById('category').value = '';
            document.getElementById('image').value = '';
            document.getElementById('form-message').style.display = 'none';
            document.getElementById('product-form').style.display = 'block';
            document.getElementById('save-btn').textContent = 'Save';
        }

        async function editProduct(id) {
            const product = products.find(p => p.id == id);
            if (product) {
                isAddingProduct = false;
                document.getElementById('form-title').textContent = 'Edit Product';
                document.getElementById('product_id').value = product.id;
                document.getElementById('name').value = product.name;
                document.getElementById('description').value = product.description || '';
                document.getElementById('price').value = product.price;
                document.getElementById('category').value = product.category || '';
                document.getElementById('image').value = ''; // Clear file input
                document.getElementById('form-message').style.display = 'none';
                document.getElementById('product-form').style.display = 'block';
                document.getElementById('save-btn').textContent = 'Update';

                // Fetch full product details if needed
                try {
                    const response = await fetch('get_product.php?id=' + id);
                    const productData = await response.json();
                    if (productData) {
                        document.getElementById('description').value = productData.description || '';
                    }
                } catch (error) {
                    console.error('Error fetching product details:', error);
                }
            }
        }

        function hideForm() {
            document.getElementById('product-form').style.display = 'none';
            document.getElementById('form-message').style.display = 'none';
        }

        function showFormMessage(message, isSuccess) {
            const messageEl = document.getElementById('form-message');
            messageEl.textContent = message;
            messageEl.style.display = 'block';
            messageEl.style.backgroundColor = isSuccess ? '#d4edda' : '#f8d7da';
            messageEl.style.color = isSuccess ? '#155724' : '#721c24';
            messageEl.style.borderLeft = '3px solid ' + (isSuccess ? '#28a745' : '#dc3545');
        }

        async function submitProductForm(event) {
            event.preventDefault();

            const productId = document.getElementById('product_id').value;
            const name = document.getElementById('name').value;
            const description = document.getElementById('description').value;
            const price = document.getElementById('price').value;
            const category = document.getElementById('category').value;
            const image = document.getElementById('image').files[0];

            // Validate fields
            if (!name) {
                showFormMessage('Product name is required', false);
                return;
            }
            if (!price || parseFloat(price) <= 0) {
                showFormMessage('Product price must be greater than 0', false);
                return;
            }

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('name', name);
            formData.append('description', description);
            formData.append('price', price);
            formData.append('category', category);
            if (image) {
                formData.append('image', image);
            }

            try {
                const endpoint = isAddingProduct ? 'manage_products.php' : 'update_product.php';
                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: isAddingProduct ? new URLSearchParams(formData) : formData
                });

                if (isAddingProduct) {
                    // For adding, redirect after success
                    loadProducts();
                    hideForm();
                    showFormMessage('Product added successfully', true);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    // For updating, use JSON response
                    const jsonResponse = await response.json();
                    if (jsonResponse.success) {
                        showFormMessage('Product updated successfully!', true);
                        setTimeout(() => {
                            hideForm();
                            loadProducts();
                        }, 1500);
                    } else {
                        showFormMessage(jsonResponse.message || 'Failed to update product', false);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showFormMessage('An error occurred: ' + error.message, false);
            }
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                fetch('manage_products.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete&product_id=${id}`
                }).then(() => {
                    showFormMessage('Product deleted successfully', true);
                    loadProducts();
                }).catch(error => {
                    console.error('Error deleting product:', error);
                    showFormMessage('Failed to delete product', false);
                });
            }
        }

        // Form submission handler
        document.addEventListener('DOMContentLoaded', () => {
            loadProducts();
            const form = document.getElementById('product-form-element');
            if (form) {
                form.addEventListener('submit', submitProductForm);
            }
        });
    </script>
</body>
