<?php
require 'auth.php';
require 'db.php';

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $log_id = (int)$_POST['log_id'];
    $vehicle_id = (int)$_POST['vehicle_id'];

    $stmt = $conn->prepare("SELECT id FROM vehicles WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $vehicle_id, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        die("Error: You do not have permission to edit logs for this vehicle.");
    }
    $stmt->close();

    $odometer = (float)$_POST['odometer'];
    $fuel_price = (float)$_POST['fuel_price'];
    $fuel_liters = (float)$_POST['fuel_liters'];
    $filled_at = $_POST['date'];

    $fuel_cost = $fuel_price * $fuel_liters;

    if ($log_id > 0 && $odometer > 0 && $fuel_price > 0 && $fuel_liters > 0) {
        $update_stmt = $conn->prepare(
            "UPDATE fuel_logs SET odometer = ?, fuel_price = ?, fuel_liters = ?, fuel_cost = ?, filled_at = ? WHERE id = ?"
        );
        $update_stmt->bind_param("ddddsi", $odometer, $fuel_price, $fuel_liters, $fuel_cost, $filled_at, $log_id);
        
        if ($update_stmt->execute()) {
            header("Location: vehicle_dashboard.php?vid=" . $vehicle_id);
            exit;
        } else {
            die("Error: Could not update the fuel log.");
        }
    } else {
        die("Error: Invalid data provided.");
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>