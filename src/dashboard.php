<?php
require 'auth.php';
require 'db.php';

$user_id = $_SESSION["user_id"];
$result = $conn->query("SELECT nickname FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();
?>

<h2>Welcome, <?= htmlspecialchars($user['nickname']) ?>!</h2>
<p>This is your vehicle dashboard (coming soon).</p>
<a href="logout.php">Logout</a>
