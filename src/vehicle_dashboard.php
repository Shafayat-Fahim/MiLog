<?php
require 'auth.php';
require 'db.php';

$user_id = $_SESSION['user_id'];
$vehicle_id = (int) $_GET['vid'];

$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $vehicle_id, $user_id);
$stmt->execute();
$vehicle = $stmt->get_result()->fetch_assoc();

if (!$vehicle) {
    echo "❌ Vehicle not found.";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM fuel_logs WHERE vehicle_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$logs = $stmt->get_result();

$total_litres = $total_cost = $total_kmpl = $best_kmpl = $last_kmpl = 0;
$prev_odo = null;
$count = 0;

$fuel_log_data = [];

while ($log = $logs->fetch_assoc()) {
    $fuel_log_data[] = $log;

    $total_litres += $log['litres'];
    $total_cost += $log['litres'] * $log['price_per_litre'];

    if ($prev_odo !== null) {
        $distance = $prev_odo - $log['odometer'];
        $kmpl = $distance / $log['litres'];
        $total_kmpl += $kmpl;
        $best_kmpl = max($best_kmpl, $kmpl);
        if ($count === 0) $last_kmpl = $kmpl;
        $count++;
    }

    $prev_odo = $log['odometer'];
}

$avg_kmpl = $count > 0 ? round($total_kmpl / $count, 2) : 0;
$last_kmpl = round($last_kmpl, 2);
$best_kmpl = round($best_kmpl, 2);
?>

<h2>📊 <?= htmlspecialchars($vehicle['name']) ?> Dashboard</h2>
<p>
    Average: <?= $avg_kmpl ?> km/l<br>
    Last: <?= $last_kmpl ?> km/l<br>
    Best: <?= $best_kmpl ?> km/l<br>
    Total Litres: <?= $total_litres ?><br>
    Total Cost: ৳<?= $total_cost ?><br>
    Total Logs: <?= count($fuel_log_data) ?><br>
</p>

<h3>➕ Add Fuel Log</h3>
<form action="add_fuel_log.php" method="POST">
    <input type="hidden" name="vehicle_id" value="<?= $vehicle_id ?>">
    Odometer: <input type="number" name="odometer" required><br>
    Price per Litre: <input type="number" step="0.01" name="price_per_litre" required><br>
    Litres: <input type="number" step="0.01" name="litres"><br>
    Cost (optional): <input type="number" step="0.01" name="cost"><br>
    Fuel Type: <input type="text" name="fuel_type"><br>
    Date: <input type="date" name="date" required><br>
    Location: <input type="text" name="location"><br>
    Note: <input type="text" name="note"><br>
    <input type="submit" value="Add Log">
</form>

<h3>📋 Fuel Logs (latest first)</h3>
<ol>
<?php foreach ($fuel_log_data as $i => $log): ?>
    <li>
        <?= $log['date'] ?> | <?= $log['odometer'] ?> km | <?= $log['litres'] ?> L @ <?= $log['price_per_litre'] ?>  
        - <?= $log['location'] ?> 
        - <?= $log['note'] ?>
        <?php if ($i === 0): ?>
            <form action="delete_fuel_log.php" method="POST" style="display:inline">
                <input type="hidden" name="log_id" value="<?= $log['id'] ?>">
                <input type="hidden" name="vehicle_id" value="<?= $vehicle_id ?>">
                <input type="submit" value="❌ Delete">
            </form>
            <a href="update_fuel_log.php?log_id=<?= $log['id'] ?>&vid=<?= $vehicle_id ?>">✏️ Edit</a>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ol>

<a href="vehicles.php">← Back to Vehicle List</a> |
<a href="logout.php">Logout</a>
