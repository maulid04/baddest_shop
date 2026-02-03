<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - Baddest Shop</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo"><a href="index.php">Baddest Shop</a></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="customer/login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div style="max-width: 600px; margin: 4rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2 style="text-align: center; margin-bottom: 2rem; color: #001f3f;">Place Order</h2>
                <div id="product-info" style="margin-bottom: 2rem;">
                </div>
                <form action="place_order.php" method="post">
                    <input type="hidden" id="product_id" name="product_id">
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_address">Shipping Address:</label>
                        <textarea id="shipping_address" name="shipping_address" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%;">Place Order</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Baddest Shop Inventory Management System. All rights reserved.</p>
            <p>Contact: abdallahmaulid789@gmail.com</p>
        </div>
    </footer>

    <script src="assets/js/scripts.js"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('product_id');

        if (productId) {
            document.getElementById('product_id').value = productId;
            loadProduct(productId);
            loadUserAddress();
        }

        async function loadProduct(id) {
            try {
                const response = await fetch('get_product.php?id=' + id);
                const product = await response.json();
                document.getElementById('product-info').innerHTML = `
                    <h3>${product.name}</h3>
                    <p>${product.description}</p>
                    <p>Price: $${product.price}</p>
                    <img src="${product.image_path}" alt="${product.name}" style="max-width: 200px;">
                `;
            } catch (error) {
                console.error('Error loading product:', error);
            }
        }

        async function loadUserAddress() {
            try {
                const response = await fetch('customer/get_profile.php');
                const profile = await response.json();
                document.getElementById('shipping_address').value = profile.address || '';
            } catch (error) {
            }
        }
    </script>
</body>
</html>

<?php
session_start();
require_once 'db/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: customer/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $shipping_address = trim($_POST['shipping_address']);
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    $price = $product['price'];
    $total = $price * $quantity;

    $stmt = $pdo->prepare("SELECT quantity FROM inventory WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $inv = $stmt->fetch();
    if ($inv['quantity'] < $quantity) {
        echo "<script>showMessage('Insufficient inventory.');</script>";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $total, $shipping_address]);
    $order_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$order_id, $product_id, $quantity, $price]);

    $pdo->prepare("UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?")->execute([$quantity, $product_id]);

    echo "<script>showMessage('Order placed successfully!'); window.location.href='customer/orders.php';</script>";
}
?>