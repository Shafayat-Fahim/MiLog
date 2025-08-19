<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require 'db.php';
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->bind_result($is_admin);
$stmt->fetch();
$stmt->close();

if (!$is_admin) {
    header("Location: dashboard.php");
    exit;
}

$_SESSION['is_admin'] = true;
?>