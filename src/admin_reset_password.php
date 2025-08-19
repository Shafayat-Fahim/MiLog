<?php
require 'admin_auth.php';
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id_to_update = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $new_password = $_POST['new_password'];

    if ($user_id_to_update <= 0 || empty($new_password)) {
        die("Invalid input.");
    }
    
    if ($user_id_to_update === $_SESSION['user_id']) {
        die("Admins cannot reset their own password here.");
    }

    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->bind_param("si", $password_hash, $user_id_to_update);
    
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?success=1");
        exit;
    } else {
        die("Error: Could not update the password.");
    }
} else {
    header("Location: admin_dashboard.php");
    exit;
}
?>