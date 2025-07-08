<?php
session_start();
require '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        echo "Invalid login.";
    }
}
?>
<!-- HTML Form -->
<link rel="stylesheet" href="../style.css">

<div class="auth-container">
    <h2>🔐 Login</h2>
    <form method="POST" action="login.php">
        <input type='text'name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
    </form>
    <div class="link">
        New user? <a href="register.php">Register here</a>
    </div>
</div>
