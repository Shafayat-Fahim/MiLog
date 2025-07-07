<?php
include 'db.php';

$vehicle = $_POST['vehicle'];
$odometer = $_POST['odometer'];
$fuel = $_POST['fuel'];
$date = $_POST['date'];

$stmt = $conn->prepare("INSERT INTO fuel_logs (vehicle_name, odometer_km, fuel_liters, date_added) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdds", $vehicle, $odometer, $fuel, $date);

if ($stmt->execute()) {
    header("Location: index.php");
} else {
    echo "Error: " . $conn->error;
}
?>
