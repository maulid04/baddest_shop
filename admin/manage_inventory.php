<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory - Baddest Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="manage_inventory.php" class="active">Manage Inventory</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="sales_reports.php">Sales Reports</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1>Manage Inventory</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Current Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="inventory-table">
                </tbody>
            </table>
        </main>
    </div>

    <script src="../assets/js/scripts.js"></script>
    <script>
        async function loadInventory() {
            try {
                const response = await fetch('get_inventory.php');
                const inventory = await response.json();
                displayInventory(inventory);
            } catch (error) {
                console.error('Error loading inventory:', error);
            }
        }

        function displayInventory(inventory) {
            const tbody = document.getElementById('inventory-table');
            tbody.innerHTML = '';

            inventory.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.product_id}</td>
                    <td>${item.name}</td>
                    <td><input type="number" value="${item.quantity}" onchange="updateQuantity(${item.product_id}, this.value)"></td>
                    <td><button class="btn-primary" onclick="saveQuantity(${item.product_id})">Save</button></td>
                `;
                tbody.appendChild(row);
            });
        }

        function updateQuantity(productId, quantity) {
            this[productId] = quantity;
        }

        function saveQuantity(productId) {
            const quantity = this[productId];
            if (quantity !== undefined) {
                fetch('manage_inventory.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${productId}&quantity=${quantity}`
                }).then(() => loadInventory());
            }
        }

        document.addEventListener('DOMContentLoaded', loadInventory);
    </script>
</body>
</html>

<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $stmt = $pdo->prepare("UPDATE inventory SET quantity = ? WHERE product_id = ?");
    $stmt->execute([$quantity, $product_id]);
    exit;
}
?>