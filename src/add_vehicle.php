<?php
require 'auth.php';
require 'db.php';

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $model_year = (int) $_POST["model_year"];
    $tank_capacity = (float) $_POST["tank_capacity"];

    if ($name && $model_year > 1900 && $tank_capacity > 0) {
        $stmt = $conn->prepare("INSERT INTO vehicles (user_id, name, model_year, tank_capacity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isid", $user_id, $name, $model_year, $tank_capacity);
        
        if ($stmt->execute()) {
            header("Location: vehicles.php");
            exit;
        } else {
            die("Error: Could not save the vehicle.");
        }
    } else {
        die("Error: Invalid input provided.");
    }
} else {
    header("Location: vehicles.php");
    exit;
}
?>