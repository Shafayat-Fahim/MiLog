<?php
$page_title = "Dashboard";
include 'layout_header.php';
require 'db.php';

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT nickname FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nickname);
$stmt->fetch();
$stmt->close();

$vehicles_stmt = $conn->prepare("SELECT id, name FROM vehicles WHERE user_id = ? ORDER BY name ASC");
$vehicles_stmt->bind_param("i", $user_id);
$vehicles_stmt->execute();
$vehicles_result = $vehicles_stmt->get_result();
$vehicles = $vehicles_result->fetch_all(MYSQLI_ASSOC);
$vehicles_stmt->close();

$vehicles_with_stats = [];
foreach ($vehicles as $vehicle) {
    $logs_stmt = $conn->prepare("SELECT odometer, fuel_liters, fuel_cost, filled_at FROM fuel_logs WHERE vehicle_id = ? ORDER BY filled_at ASC, odometer ASC");
    $logs_stmt->bind_param("i", $vehicle['id']);
    $logs_stmt->execute();
    $logs_result = $logs_stmt->get_result();
    $all_logs = $logs_result->fetch_all(MYSQLI_ASSOC);
    $logs_stmt->close();

    $stats = [
        'total_spent' => 0,
        'avg_mileage' => 'N/A',
        'last_refuel' => 'N/A'
    ];
    $log_count = count($all_logs);
    if ($log_count > 0) {
        $stats['total_spent'] = array_sum(array_column($all_logs, 'fuel_cost'));
        $last_log = end($all_logs);
        $stats['last_refuel'] = date("M j, Y", strtotime($last_log['filled_at']));
    }
    if ($log_count > 1) {
        $first_log = $all_logs[0];
        $total_distance = $last_log['odometer'] - $first_log['odometer'];
        $total_fuel_liters = array_sum(array_column($all_logs, 'fuel_liters'));
        $fuel_consumed = $total_fuel_liters - $last_log['fuel_liters'];
        if ($fuel_consumed > 0) {
            $stats['avg_mileage'] = number_format($total_distance / $fuel_consumed, 2) . ' km/L';
        }
    }
    $vehicle['stats'] = $stats;
    $vehicles_with_stats[] = $vehicle;
}

$recent_logs_stmt = $conn->prepare(
    "SELECT fl.filled_at, fl.fuel_cost, v.name AS vehicle_name " .
    "FROM fuel_logs fl JOIN vehicles v ON fl.vehicle_id = v.id " .
    "WHERE v.user_id = ? ORDER BY fl.filled_at DESC, fl.id DESC LIMIT 4"
);
$recent_logs_stmt->bind_param("i", $user_id);
$recent_logs_stmt->execute();
$recent_logs_result = $recent_logs_stmt->get_result();
$recent_logs = $recent_logs_result->fetch_all(MYSQLI_ASSOC);
$recent_logs_stmt->close();
?>

<div class="container">
    <div class="welcome-section">
        <h1>👋 Welcome back, <?= htmlspecialchars($nickname) ?>!</h1>
        <p>Here's a summary of your vehicles' performance.</p>
    </div>

    <div class="grid">
        <div class="card vehicle-summary-card">
            <div class="card-icon">🚗</div>
            <h2 class="card-title">My Vehicles</h2>
            
            <?php if (count($vehicles_with_stats) > 0): ?>
                <?php foreach ($vehicles_with_stats as $vehicle): ?>
                    <div class="vehicle-stats-block">
                        <h3 class="vehicle-name"><?= htmlspecialchars($vehicle['name']) ?></h3>
                        <p><strong>Total Spent:</strong> $<?= number_format($vehicle['stats']['total_spent'], 2) ?></p>
                        <p><strong>Avg. Mileage:</strong> <?= $vehicle['stats']['avg_mileage'] ?></p>
                        <p><strong>Last Refuel:</strong> <?= $vehicle['stats']['last_refuel'] ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="card-text">You haven't added any vehicles yet.</p>
            <?php endif; ?>
            
            <a href="vehicles.php" class="btn">Manage Vehicles</a>
        </div>

        <div class="card">
            <div class="card-icon">⛽</div>
            <h2 class="card-title">Recent Activity</h2>
            
            <?php if (count($recent_logs) > 0): ?>
                <div class="activity-feed">
                    <?php foreach ($recent_logs as $log): ?>
                        <div class="activity-item">
                            <div class="activity-details">
                                <span class="activity-vehicle"><?= htmlspecialchars($log['vehicle_name']) ?></span>
                                <span class="activity-date"><?= date("M j, Y", strtotime($log['filled_at'])) ?></span>
                            </div>
                            <div class="activity-cost">$<?= number_format($log['fuel_cost'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="card-text">No fuel logs have been added yet.</p>
            <?php endif; ?>
            
            <a href="vehicles.php" class="btn">View All Logs</a>
        </div>
    </div>
</div>

<style>
.vehicle-summary-card .btn { margin-top: 1.5rem; }
.vehicle-stats-block { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; }
.vehicle-stats-block:first-of-type { margin-top: 1rem; padding-top: 0; border-top: none; }
.vehicle-name { font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; }
.vehicle-stats-block p { color: var(--secondary-text); margin-bottom: 0.25rem; }
.activity-feed { margin-top: 1rem; display: flex; flex-direction: column; gap: 1rem; }
.activity-item {
    display: flex; justify-content: space-between; align-items: center;
    padding-bottom: 1rem; border-bottom: 1px solid #f3f4f6;
}
.activity-item:last-child { border-bottom: none; padding-bottom: 0; }
.activity-details { display: flex; flex-direction: column; }
.activity-vehicle { font-weight: 600; color: var(--text-color); }
.activity-date { font-size: 0.875rem; color: var(--secondary-text); }
.activity-cost { font-size: 1.125rem; font-weight: 600; color: var(--primary-color); }
.card .btn { margin-top: 1.5rem; }
</style>

<?php include 'layout_footer.php'; ?>