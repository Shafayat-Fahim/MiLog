<?php
require 'auth.php';
require 'db.php';

$user_id = $_SESSION["user_id"];

// Fetch user info
$stmt = $conn->prepare("SELECT nickname FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nickname);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>MiLog Dashboard</title>
</head>
<body>
    <h2>👋 Welcome, <?= htmlspecialchars($nickname) ?>!</h2>
    <p>This is your personal dashboard.</p>

    <ul>
        <li><a href="vehicles.php">🚗 Manage Vehicles</a></li>
        <li><a href="logout.php">🔓 Logout</a></li>
    </ul>
</body>
</html>
