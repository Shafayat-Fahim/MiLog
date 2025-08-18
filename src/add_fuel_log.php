<?php
require 'auth.php';
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vehicle_id = (int) $_POST['vehicle_id'];
    $odometer   = (int) $_POST['odometer'];
    $fuel_price = isset($_POST['fuel_price']) ? (float) $_POST['fuel_price'] : 0.0;

    // Get inputs safely
    $fuel_liters    = ($_POST['fuel_liters'] !== '') ? (float) $_POST['fuel_liters'] : null;
    $fuel_cost = ($_POST['fuel_cost'] !== '') ? (float) $_POST['fuel_cost'] : null;

    // ❌ Error if both are empty
    if ($fuel_liters === null && $fuel_cost === null) {
        die("❌ Error: Please provide either Liters or Fuel Cost.");
    }

    // ✅ Calculate missing values
    if ($fuel_liters === null && $fuel_cost !== null) {
        if ($fuel_price <= 0) {
            die("❌ Error: Fuel Price must be greater than 0 to calculate litres.");
        }
        $fuel_liters = $fuel_cost / $fuel_price;
    }
    if ($fuel_cost === null && $fuel_liters !== null) {
        if ($fuel_price <= 0) {
            die("❌ Error: Fuel Price must be greater than 0 to calculate cost.");
        }
        $fuel_cost = $fuel_liters * $fuel_price;
    }

    // ✅ Final validations
    if (!$vehicle_id || !$odometer || $fuel_price <= 0 || $fuel_liters <= 0 || $fuel_cost <= 0) {
        die("❌ Error: Invalid input values.");
    }

    // Other fields
    $fuel_type = $_POST['fuel_type'];
    $filled_at = !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d');
    $location  = $_POST['location'];
    $note      = $_POST['note'];

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO fuel_logs 
        (vehicle_id, odometer, fuel_price, fuel_liters, fuel_cost, fuel_type, filled_at, location, note) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "iidddssss", 
        $vehicle_id, $odometer, $fuel_price, $fuel_liters, $fuel_cost, 
        $fuel_type, $filled_at, $location, $note
    );
    $stmt->execute();

    // Redirect back
    header("Location: vehicle_dashboard.php?vid=$vehicle_id");
    exit;
}
?>
