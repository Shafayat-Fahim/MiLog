<?php
include 'db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle = $_POST['vehicle'];
    $odometer = $_POST['odometer'];
    $fuel = $_POST['fuel'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE fuel_logs SET vehicle_name=?, odometer_km=?, fuel_liters=?, date_added=? WHERE id=?");
    $stmt->bind_param("sddsi", $vehicle, $odometer, $fuel, $date, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch current data to show in form
$result = $conn->query("SELECT * FROM fuel_logs WHERE id=$id");
if ($result->num_rows === 0) {
    echo "Record not found.";
    exit();
}
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Fuel Record</title>
</head>
<body>
    <h2>Edit Fuel Record</h2>
    <form method="POST">
        Vehicle Name: <input type="text" name="vehicle" value="<?= htmlspecialchars($row['vehicle_name']) ?>" required><br>
        Odometer (km): <input type="number" step="0.1" name="odometer" value="<?= htmlspecialchars($row['odometer_km']) ?>" required><br>
        Fuel Liters: <input type="number" step="0.01" name="fuel" value="<?= htmlspecialchars($row['fuel_liters']) ?>" required><br>
        Date: <input type="date" name="date" value="<?= htmlspecialchars($row['date_added']) ?>" required><br>
        <input type="submit" value="Update">
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>
