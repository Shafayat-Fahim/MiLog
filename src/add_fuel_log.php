<?php
require 'auth.php';
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vehicle_id = (int) $_POST['vehicle_id'];
    $odometer = (int) $_POST['odometer'];
    $fuel_price = (float) $_POST['price_per_litre'];

    $litres = isset($_POST['litres']) ? (float) $_POST['litres'] : null;
    $fuel_cost = isset($_POST['fuel_cost']) ? (float) $_POST['fuel_cost'] : null;

    if (is_null($litres) && is_null($fuel_cost)) {
    die("❌ Error: You must provide either litres or fuel cost.");
    }


    if (!$litres && $fuel_cost) {
        $litres = $fuel_cost / $fuel_price;
    } elseif (!$fuel_cost && $litres) {
        $fuel_cost = $litres * $fuel_price;
    }

    if (is_null($fuel_cost)) $fuel_cost = 0;
    if (is_null($litres)) $litres = 0;

    $fuel_type = $_POST['fuel_type'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $note = $_POST['note'];

    if (empty($fuel_price) || empty($odometer) || empty($fuel_type) || empty($date)) {
        die("❌ Error: Required fields are missing.");
    }

    $stmt = $conn->prepare("INSERT INTO fuel_logs (vehicle_id, odometer, fuel_price, litres, fuel_cost, fuel_type, date, location, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidddssss", $vehicle_id, $odometer, $fuel_price, $litres, $fuel_cost, $fuel_type, $date, $location, $note);

    if (!$stmt->execute()) {
        die("❌ Database insert failed: " . $stmt->error);
    }

    header("Location: vehicle_dashboard.php?vid=$vehicle_id");
    exit;
}
?>
