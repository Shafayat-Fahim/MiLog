<?php
require 'auth.php';
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vehicle_id = (int) $_POST['vehicle_id'];
    $odometer = (int) $_POST['odometer'];
    $fuel_price = isset($_POST['fuel_price']) ? (float) $_POST['fuel_price'] : 0.0;

    // Properly check litres and cost (even if 0 is entered accidentally)
    $litres = isset($_POST['litres']) && $_POST['litres'] !== '' ? (float) $_POST['litres'] : null;
    $fuel_cost = isset($_POST['fuel_cost']) && $_POST['fuel_cost'] !== '' ? (float) $_POST['fuel_cost'] : null;

    // Error if both missing
    if ($litres === null && $fuel_cost === null) {
        die("❌ Error: Please provide either Litres or Fuel Cost.");
    }

    // Auto calculate
    if ($litres === null && $fuel_cost !== null && $fuel_price > 0) {
        $litres = $fuel_cost / $fuel_price;
    }
    if ($fuel_cost === null && $litres !== null && $fuel_price > 0) {
        $fuel_cost = $litres * $fuel_price;
    }

    // Final check
    if (!$vehicle_id || !$odometer || !$fuel_price || !$litres || !$fuel_cost) {
        die("❌ Error: One or more required values are invalid.");
    }

    $fuel_type = $_POST['fuel_type'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $note = $_POST['note'];

    $stmt = $conn->prepare("INSERT INTO fuel_logs (vehicle_id, odometer, fuel_price, litres, fuel_cost, fuel_type, date, location, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidddssss", $vehicle_id, $odometer, $fuel_price, $litres, $fuel_cost, $fuel_type, $date, $location, $note);
    $stmt->execute();

    header("Location: vehicle_dashboard.php?vid=$vehicle_id");
    exit;
}
?>
