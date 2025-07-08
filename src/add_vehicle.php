<?php
require 'auth.php';
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $model_year = (int) $_POST["model_year"];
    $tank_capacity = (float) $_POST["tank_capacity"];
    $user_id = $_SESSION["user_id"];

    if ($name && $model_year && $tank_capacity) {
        $stmt = $conn->prepare("INSERT INTO vehicles (user_id, name, model_year, tank_capacity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isid", $user_id, $name, $model_year, $tank_capacity);
        $stmt->execute();
        header("Location: vehicles.php");
        exit;
    } else {
        echo "❌ Invalid input.";
    }
}
?>
