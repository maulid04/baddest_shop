<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Baddest Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Customer Dashboard</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1>Welcome to Your Dashboard</h1>
            <p>Manage your orders and profile here.</p>
            <!-- Add recent orders or stats -->
        </main>
    </div>

    <script src="../assets/js/scripts.js"></script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}
?>