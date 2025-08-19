<?php
require 'auth.php';
require 'db.php';

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vehicle_id = (int)$_POST['vehicle_id'];

    $stmt = $conn->prepare("SELECT id FROM vehicles WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $vehicle_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("Error: You do not have permission to add logs to this vehicle.");
    }
    $stmt->close();

    $odometer = (float)$_POST['odometer'];
    $fuel_price = isset($_POST['fuel_price']) ? (float)$_POST['fuel_price'] : 0.0;
    $fuel_liters = !empty($_POST['fuel_liters']) ? (float)$_POST['fuel_liters'] : null;
    $fuel_cost = !empty($_POST['fuel_cost']) ? (float)$_POST['fuel_cost'] : null;

    if ($fuel_liters === null && $fuel_cost === null) {
        die("Error: Please provide either Liters or Total Cost.");
    }

    if ($fuel_liters === null && $fuel_cost !== null && $fuel_price > 0) {
        $fuel_liters = $fuel_cost / $fuel_price;
    }
    if ($fuel_cost === null && $fuel_liters !== null && $fuel_price > 0) {
        $fuel_cost = $fuel_liters * $fuel_price;
    }

    $filled_at = !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d');
    
    $note = trim($_POST['note'] ?? '');

    if ($vehicle_id > 0 && $odometer > 0 && $fuel_price > 0 && $fuel_liters > 0 && $fuel_cost > 0) {
        $stmt = $conn->prepare(
            "INSERT INTO fuel_logs (vehicle_id, odometer, fuel_price, fuel_liters, fuel_cost, filled_at, note) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iddddss", $vehicle_id, $odometer, $fuel_price, $fuel_liters, $fuel_cost, $filled_at, $note);
        
        if ($stmt->execute()) {
            header("Location: vehicle_dashboard.php?vid=" . $vehicle_id);
            exit;
        } else {
            die("Error: Could not save the fuel log.");
        }
    } else {
        die("Error: Invalid data provided for fuel log.");
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>