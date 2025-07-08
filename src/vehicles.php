<?php
require 'auth.php';
require 'db.php';

$user_id = $_SESSION["user_id"];

// Fetch user's vehicles
$stmt = $conn->prepare("SELECT id, name, model_year, tank_capacity FROM vehicles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>🚗 Your Vehicles</h2>

<ul>
<?php while ($row = $result->fetch_assoc()): ?>
  <li>
    <?= htmlspecialchars($row['name']) ?> (<?= $row['model_year'] ?>)
    - Tank: <?= $row['tank_capacity'] ?> L
    - <a href="vehicle_dashboard.php?vid=<?= $row['id'] ?>">View</a>
  </li>
<?php endwhile; ?>
</ul>

<hr>

<h3>Add a New Vehicle</h3>
<form action="add_vehicle.php" method="POST">
  Name: <input type="text" name="name" required><br>
  Model Year: <input type="number" name="model_year" min="1900" max="<?= date("Y") ?>" required><br>
  Tank Capacity (L): <input type="number" name="tank_capacity" step="0.1" required><br>
  <input type="submit" value="Add Vehicle">
</form>

<a href="dashboard.php">← Back to Dashboard</a> |
<a href="logout.php">Logout</a>
