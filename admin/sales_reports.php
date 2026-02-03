<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports - Baddest Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="manage_inventory.php">Manage Inventory</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="sales_reports.php" class="active">Sales Reports</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1>Sales Reports</h1>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div style="background: #f8f8f8; padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: #001f3f; margin-bottom: 0.5rem;">Total Orders</h3>
                    <p style="font-size: 2rem; color: #2ecc71; font-weight: bold;" id="total-orders">0</p>
                </div>
                <div style="background: #f8f8f8; padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: #001f3f; margin-bottom: 0.5rem;">Total Sales</h3>
                    <p style="font-size: 2rem; color: #2ecc71; font-weight: bold;" id="total-sales">$0.00</p>
                </div>
                <div style="background: #f8f8f8; padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: #001f3f; margin-bottom: 0.5rem;">Average Order Value</h3>
                    <p style="font-size: 2rem; color: #2ecc71; font-weight: bold;" id="average-order">$0.00</p>
                </div>
            </div>

            <h2>All Orders</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="reports-table">
                </tbody>
            </table>
        </main>
    </div>

    <script src="../assets/js/scripts.js"></script>
    <script>
        async function loadSalesReports() {
            try {
                const response = await fetch('get_sales_data.php');
                const data = await response.json();
                displayReports(data);
                updateStatistics(data);
            } catch (error) {
                console.error('Error loading sales reports:', error);
            }
        }

        function displayReports(orders) {
            const tbody = document.getElementById('reports-table');
            tbody.innerHTML = '';

            if (orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem;">No orders found</td></tr>';
                return;
            }

            orders.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${order.id}</td>
                    <td>${order.username}</td>
                    <td>$${parseFloat(order.total_amount).toFixed(2)}</td>
                    <td>${order.status}</td>
                    <td>${new Date(order.created_at).toLocaleDateString()}</td>
                `;
                tbody.appendChild(row);
            });
        }

        function updateStatistics(orders) {
            const totalOrders = orders.length;
            const totalSales = orders.reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
            const averageOrder = totalOrders > 0 ? totalSales / totalOrders : 0;

            document.getElementById('total-orders').textContent = totalOrders;
            document.getElementById('total-sales').textContent = '$' + totalSales.toFixed(2);
            document.getElementById('average-order').textContent = '$' + averageOrder.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadSalesReports();
            setInterval(loadSalesReports, 30000);
        });
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
?>