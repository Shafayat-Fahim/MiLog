<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nickname = trim($_POST["nickname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password !== $confirm) {
        die("❌ Passwords do not match.");
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (nickname, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nickname, $email, $password_hash);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        header("Location: dashboard.php");
        exit;
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>

<h2>Register</h2>
<form method="post">
  Nickname: <input type="text" name="nickname" required><br>
  Email: <input type="email" name="email" required><br>
  Password: <input type="password" name="password" required><br>
  Confirm Password: <input type="password" name="confirm" required><br>
  <input type="submit" value="Register">
</form>
<a href="login.php">Already have an account? Log in</a>
