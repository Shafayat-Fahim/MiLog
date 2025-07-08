<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>MiLog - Fuel Mileage Tracker</title>
</head>
<body>
    <h2>Add Fuel Data</h2>
    <form method="POST" action="add.php">
        Vehicle Name: <input type="text" name="vehicle" required><br>
        Odometer (km): <input type="number" step="0.1" name="odometer" required><br>
        Fuel Liters: <input type="number" step="0.01" name="fuel" required><br>
        Date: <input type="date" name="date" required><br>
        <input type="submit" value="Add">
    </form>

    <h2>Fuel Records</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th><th>Vehicle</th><th>Odometer</th><th>Fuel (L)</th><th>Date</th><th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM fuel_logs ORDER BY date DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['vehicle_id']}</td>
                    <td>{$row['odometer']}</td>
                    <td>{$row['litres']}</td>
                    <td>{$row['date']}</td>
                    <td>
                        <a href='update.php?id={$row['id']}'>Edit</a> | 
                        <a href='delete.php?id={$row['id']}'>Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>
