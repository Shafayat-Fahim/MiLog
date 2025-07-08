<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $hash);
    
    if ($stmt->fetch() && password_verify($password, $hash)) {
        $_SESSION["user_id"] = $user_id;
        header("Location: dashboard.php");
        exit;
    } else {
        echo "❌ Invalid credentials.";
    }
}
?>

<h2>Login</h2>
<form method="post">
  Email: <input type="email" name="email" required><br>
  Password: <input type="password" name="password" required><br>
  <input type="submit" value="Log In">
</form>
<a href="register.php">Don't have an account? Register</a>
