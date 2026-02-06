<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Baddest Shop</title>
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
                <li><a href="manage_users.php" class="active">Manage Users</a></li>
                <li><a href="sales_reports.php">Sales Reports</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1>Manage Users</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table">
                </tbody>
            </table>
        </main>
    </div>

    <script src="../assets/js/scripts.js"></script>
    <script>
        async function loadUsers() {
            try {
                const response = await fetch('get_users.php');
                const users = await response.json();
                displayUsers(users);
            } catch (error) {
                console.error('Error loading users:', error);
            }
        }

        function displayUsers(users) {
            const tbody = document.getElementById('users-table');
            tbody.innerHTML = '';

            users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.role_name}</td>
                    <td>
                        <a href="#" onclick="editUser(${user.id})">Edit</a> |
                        <a href="#" onclick="deleteUser(${user.id})">Delete</a>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function editUser(id) {
            alert('Edit user ' + id);
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                fetch('manage_users.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete&user_id=${id}`
                }).then(() => loadUsers());
            }
        }

        document.addEventListener('DOMContentLoaded', loadUsers);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $user_id = $_POST['user_id'];
    $pdo->prepare("DELETE FROM users WHERE id = ? AND role_id != 2")->execute([$user_id]);
    exit;
}
?>