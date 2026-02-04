<?php
session_start();
require_once '../db/connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role_id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'Account not found.';
        } elseif (!password_verify($password, $user['password_hash'])) {
            $error = 'Invalid username or password.';
        } elseif ($user['role_id'] != 1) {
            // If user exists but is not a customer, do not allow login
            $error = 'Invalid username or password.';
        } else {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'customer';
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - Baddest Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo"><a href="../index.php">Baddest Shop</a></h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div style="max-width: 400px; margin: 4rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2 style="text-align: center; margin-bottom: 2rem; color: #001f3f;">Customer Login</h2>
                <?php if (!empty($error)): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #f5c6cb;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="username">Username or Email:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%;">Login</button>
                </form>
                <p style="text-align: center; margin-top: 1rem;">Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Baddest Shop Inventory Management System. All rights reserved.</p>
            <p>Contact: info@baddestshop.com</p>
        </div>
    </footer>

    <script src="../assets/js/scripts.js"></script>
</body>
</html>

