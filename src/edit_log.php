<?php
$page_title = "Edit Fuel Log";
include 'layout_header.php';
require 'db.php';

$user_id = $_SESSION["user_id"];
$log_id = isset($_GET['log_id']) ? (int)$_GET['log_id'] : 0;

if ($log_id <= 0) {
    die("<div class='container card'>Invalid log ID.</div>");
}

$stmt = $conn->prepare(
    "SELECT fl.id, fl.vehicle_id, fl.odometer, fl.fuel_price, fl.fuel_liters, fl.fuel_cost, fl.filled_at " .
    "FROM fuel_logs fl JOIN vehicles v ON fl.vehicle_id = v.id " .
    "WHERE fl.id = ? AND v.user_id = ?"
);
$stmt->bind_param("ii", $log_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("<div class='container card'>Log not found or you don't have permission to edit it.</div>");
}
$log = $result->fetch_assoc();
$stmt->close();
?>

<div class="container">
    <div class="card">
        <h1 class="card-title">Edit Fuel Log</h1>
        <form action="update_log.php" method="POST" class="form-grid">
            <input type="hidden" name="log_id" value="<?= $log['id'] ?>">
            <input type="hidden" name="vehicle_id" value="<?= $log['vehicle_id'] ?>">

            <div>
                <label for="odometer">Odometer (km)</label>
                <input type="number" id="odometer" name="odometer" step="0.1" value="<?= htmlspecialchars($log['odometer']) ?>" required>
            </div>
            <div>
                <label for="date">Date Filled</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($log['filled_at']) ?>" required>
            </div>
            <div>
                <label for="fuel_price">Price per Liter</label>
                <input type="number" id="fuel_price" name="fuel_price" step="0.01" value="<?= htmlspecialchars($log['fuel_price']) ?>" required>
            </div>
            <div>
                <label for="fuel_liters">Fuel (Liters)</label>
                <input type="number" id="fuel_liters" name="fuel_liters" step="0.01" value="<?= htmlspecialchars($log['fuel_liters']) ?>" required>
            </div>
            <div class="form-full-width">
                <input type="submit" value="Update Log" class="btn">
                <a href="vehicle_dashboard.php?vid=<?= $log['vehicle_id'] ?>" class="btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.form-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
@media (min-width: 768px) { .form-grid { grid-template-columns: 1fr 1fr; } }
.form-full-width { grid-column: 1 / -1; }
.form-grid label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary-text); }
.form-grid input {
    width: 100%; padding: 0.75rem; border: 1px solid #d1d5db;
    border-radius: 0.375rem; font-size: 1rem;
}
.form-grid input[type="submit"] { border: none; cursor: pointer; }
.btn-outline {
    display: inline-block; padding: 0.75rem 1.5rem; background-color: transparent; color: var(--secondary-text);
    text-decoration: none; border-radius: 0.375rem; text-align: center;
    border: 1px solid #d1d5db; margin-left: 1rem;
}
</style>

<?php include 'layout_footer.php'; ?>