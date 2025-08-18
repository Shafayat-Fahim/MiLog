<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>MiLog - Fuel Mileage Tracker</title>
</head>
<body>
    <h2>Fuel Records</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th><th>Vehicle</th><th>Odometer</th><th>Fuel (L)</th><th>Date</th><th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM fuel_logs ORDER BY filled_at DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['vehicle_id']}</td>
                    <td>{$row['odometer']}</td>
                    <td>{$row['fuel_liters']}</td>
                    <td>{$row['filled_at']}</td>
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
