<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Baddest Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Customer Dashboard</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1>My Profile</h1>
            <form action="profile.php" method="post" id="profile-form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address"></textarea>
                </div>
                <button type="submit" class="btn-primary">Update Profile</button>
            </form>
        </main>
    </div>

    <script src="../assets/js/scripts.js"></script>
    <script>
        async function loadProfile() {
            try {
                const response = await fetch('get_profile.php');
                const profile = await response.json();
                document.getElementById('username').value = profile.username;
                document.getElementById('email').value = profile.email;
                document.getElementById('first_name').value = profile.first_name;
                document.getElementById('last_name').value = profile.last_name;
                document.getElementById('phone').value = profile.phone;
                document.getElementById('address').value = profile.address;
            } catch (error) {
                console.error('Error loading profile:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', loadProfile);
    </script>
</body>
</html>

<?php
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    try {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$username, $email, $first_name, $last_name, $phone, $address, $user_id]);
        echo "<script>showMessage('Profile updated successfully!');</script>";
    } catch (Exception $e) {
        echo "<script>showMessage('Update failed: " . $e->getMessage() . "');</script>";
    }
}
?>