<?php
$page_title = "Vehicle Dashboard";
include 'layout_header.php';
require 'db.php';

$user_id = $_SESSION["user_id"];
$vehicle_id = isset($_GET['vid']) ? (int)$_GET['vid'] : 0;

if ($vehicle_id <= 0) {
    die("<div class='container'><div class='card'>Error: Invalid vehicle ID provided.</div></div>");
}

$stmt = $conn->prepare("SELECT id, name, model_year FROM vehicles WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $vehicle_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("<div class='container'><div class='card'>Error: Vehicle not found or you do not have permission to view it.</div></div>");
}
$vehicle = $result->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT id, odometer, fuel_liters, fuel_cost, fuel_price FROM fuel_logs WHERE vehicle_id = ? ORDER BY filled_at ASC, odometer ASC");
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$logs_result = $stmt->get_result();
$all_logs = $logs_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total_logs = count($all_logs);
$total_fuel_liters = 0;
$total_fuel_cost = 0;
$average_mileage = 0;

if ($total_logs > 0) {
    $total_fuel_liters = array_sum(array_column($all_logs, 'fuel_liters'));
    $total_fuel_cost = array_sum(array_column($all_logs, 'fuel_cost'));
}

if ($total_logs > 1) {
    $first_log = $all_logs[0];
    $last_log = end($all_logs);
    $total_distance = $last_log['odometer'] - $first_log['odometer'];
    $fuel_consumed = $total_fuel_liters - $last_log['fuel_liters'];
    if ($fuel_consumed > 0) {
        $average_mileage = $total_distance / $fuel_consumed;
    }
}

$display_logs = array_reverse($all_logs);
?>

<div class="container">
    <div class="welcome-section">
        <h1>📊 Analytics for <?= htmlspecialchars($vehicle['name']) ?></h1>
        <p>Model Year: <?= htmlspecialchars($vehicle['model_year']) ?></p>
        <a href="vehicles.php" class="back-link">← Back to All Vehicles</a>
    </div>

    <div class="grid stats-grid">
        <div class="card stat-card">
            <div class="stat-value">Tk<?= number_format($total_fuel_cost, 2) ?></div>
            <div class="stat-label">Total Fuel Expenses</div>
        </div>
        <div class="card stat-card">
            <div class="stat-value"><?= number_format($total_fuel_liters, 2) ?> L</div>
            <div class="stat-label">Total Fuel Purchased</div>
        </div>
        <div class="card stat-card">
            <div class="stat-value">
                <?= $average_mileage > 0 ? number_format($average_mileage, 2) . ' km/L' : 'N/A' ?>
            </div>
            <div class="stat-label">Average Mileage</div>
        </div>
        <div class="card stat-card">
            <div class="stat-value"><?= $total_logs ?></div>
            <div class="stat-label">Total Logs</div>
        </div>
    </div>

    <div class="card form-card">
        <h2 class="card-title">Add New Fuel Log</h2>
        <form action="add_fuel_log.php" method="POST" class="form-grid">
            <input type="hidden" name="vehicle_id" value="<?= $vehicle['id'] ?>">
            <div>
                <label for="odometer">Odometer (km)</label>
                <input type="number" id="odometer" name="odometer" step="0.1" required>
            </div>
            <div>
                <label for="date">Date Filled</label>
                <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div>
                <label for="fuel_price">Price per Liter</label>
                <input type="number" id="fuel_price" name="fuel_price" step="0.01" required oninput="calculateValues()">
            </div>
            <div>
                <label for="fuel_liters">Fuel (Liters)</label>
                <input type="number" id="fuel_liters" name="fuel_liters" step="0.01" oninput="calculateValues('liters')">
            </div>
            <div>
                <label for="fuel_cost">Total Cost</label>
                <input type="number" id="fuel_cost" name="fuel_cost" step="0.01" oninput="calculateValues('cost')">
            </div>
            <div class="form-full-width">
                <input type="submit" value="Add Log" class="btn">
            </div>
        </form>
    </div>

    <div class="card">
        <h2 class="card-title">Log History</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Odometer</th>
                        <th>Liters</th>
                        <th>Price/L</th>
                        <th>Total Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($display_logs) > 0): ?>
                        <?php foreach ($display_logs as $index => $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($log['odometer']) ?> km</td>
                                <td><?= number_format($log['fuel_liters'], 2) ?> L</td>
                                <td>Tk<?= number_format($log['fuel_price'], 2) ?></td>
                                <td>Tk<?= number_format($log['fuel_cost'], 2) ?></td>
                                <td>
                                    <?php if ($index === 0): ?>
                                        <a href="edit_log.php?log_id=<?= $log['id'] ?>" class="action-link">Edit</a>
                                        <a href="delete_log.php?log_id=<?= $log['id'] ?>" class="action-link delete-link" onclick="showConfirmModal(event)">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 1rem;">No fuel logs found. Add one above to get started!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="confirm-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h2 class="modal-title">Delete Log</h2>
        <p>Are you sure you want to permanently delete this log entry?</p>
        <div class="modal-actions">
            <button id="modal-cancel-btn" class="btn btn-outline">Cancel</button>
            <a id="modal-confirm-btn" href="#" class="btn btn-delete">Delete</a>
        </div>
    </div>
</div>

<style>
.back-link { color: var(--primary-color); text-decoration: none; font-weight: 500; }
.stats-grid { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 2rem; }
.stat-card { text-align: center; padding: 2rem 1rem; }
.stat-value { font-size: 2.25rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.5rem; }
.stat-label { font-size: 1rem; color: var(--secondary-text); }
.form-card { margin-bottom: 2rem; }
.form-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
@media (min-width: 768px) { .form-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); } }
.form-full-width { grid-column: 1 / -1; }
.form-grid label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary-text); }
.form-grid input {
    width: 100%; padding: 0.75rem; border: 1px solid #d1d5db;
    border-radius: 0.375rem; font-size: 1rem;
}
.form-grid input[type="submit"] { width: 100%; border: none; cursor: pointer; }
.table-container { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
th { background-color: #f9fafb; font-weight: 600; }
tbody tr:last-child td { border-bottom: none; }
.action-link { text-decoration: none; color: var(--primary-color); font-weight: 500; margin-right: 0.5rem; }
.delete-link { color: #dc2626; }
.modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.6); display: flex;
    justify-content: center; align-items: center; z-index: 2000;
}
.modal-content {
    background-color: var(--card-bg); padding: 2rem; border-radius: 0.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3); width: 90%; max-width: 400px; text-align: center;
}
.modal-title { font-size: 1.5rem; margin-bottom: 1rem; }
.modal-actions { margin-top: 2rem; display: flex; justify-content: center; gap: 1rem; }
.btn-delete { background-color: #dc2626; }
.btn-delete:hover { background-color: #b91c1c; }
.btn-outline { border: 1px solid #d1d5db; background-color: transparent; color: var(--secondary-text); }
.btn-outline:hover { background-color: #f9fafb; }
</style>

<script>
function calculateValues(source) {
    const price = parseFloat(document.getElementById('fuel_price').value) || 0;
    const liters = parseFloat(document.getElementById('fuel_liters').value) || 0;
    const cost = parseFloat(document.getElementById('fuel_cost').value) || 0;

    if (price > 0) {
        if (source === 'liters' && liters > 0) {
            document.getElementById('fuel_cost').value = (liters * price).toFixed(2);
        } else if (source === 'cost' && cost > 0) {
            document.getElementById('fuel_liters').value = (cost / price).toFixed(2);
        }
    }
}

const modal = document.getElementById('confirm-modal');
const confirmBtn = document.getElementById('modal-confirm-btn');
const cancelBtn = document.getElementById('modal-cancel-btn');

function showConfirmModal(event) {
    event.preventDefault();
    const deleteUrl = event.target.href;
    confirmBtn.href = deleteUrl;
    modal.style.display = 'flex';
}

cancelBtn.onclick = function() {
    modal.style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<?php
include 'layout_footer.php';
?>