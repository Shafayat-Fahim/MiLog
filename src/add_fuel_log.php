<?php
require 'auth.php';
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vehicle_id = (int) $_POST['vehicle_id'];
    $odometer = (int) $_POST['odometer'];
    $price = (float) $_POST['price_per_litre'];
    $litres = isset($_POST['litres']) ? (float) $_POST['litres'] : null;
    $cost = isset($_POST['cost']) ? (float) $_POST['cost'] : null;

    if (!$litres && $cost) $litres = $cost / $price;
    if (!$cost && $litres) $cost = $litres * $price;

    $fuel_type = $_POST['fuel_type'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $note = $_POST['note'];

    $stmt = $conn->prepare("INSERT INTO fuel_logs (vehicle_id, odometer, price_per_litre, litres, fuel_type, date, location, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiddssss", $vehicle_id, $odometer, $price, $litres, $fuel_type, $date, $location, $note);
    $stmt->execute();

    header("Location: vehicle_dashboard.php?vid=$vehicle_id");
    exit;
}
?>
