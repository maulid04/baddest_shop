<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Baddest Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Customer Dashboard</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="orders.php" class="active">My Orders</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1>My Orders</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="orders-table">
                </tbody>
            </table>
        </main>
    </div>

    <script src="../assets/js/scripts.js"></script>
    <script>
        async function loadOrders() {
            try {
                const response = await fetch('get_orders.php');
                const orders = await response.json();
                displayOrders(orders);
            } catch (error) {
                console.error('Error loading orders:', error);
            }
        }

        function displayOrders(orders) {
            const tbody = document.getElementById('orders-table');
            tbody.innerHTML = '';

            orders.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${order.id}</td>
                    <td>${order.created_at}</td>
                    <td>$${order.total_amount}</td>
                    <td>${order.status}</td>
                `;
                tbody.appendChild(row);
            });
        }

        document.addEventListener('DOMContentLoaded', loadOrders);
    </script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}
?>