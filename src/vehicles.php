<?php
$page_title = "My Vehicles";
include 'layout_header.php';
require 'db.php';

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT id, name, model_year, tank_capacity FROM vehicles WHERE user_id = ? ORDER BY name ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$vehicles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container">
    <div class="welcome-section">
        <h1>🚗 Your Vehicles</h1>
        <p>Manage your vehicles and view their individual fuel logs.</p>
    </div>

    <div class="grid">
        <?php if (count($vehicles) > 0): ?>
            <?php foreach ($vehicles as $vehicle): ?>
                <div class="card">
                    <h2 class="card-title"><?= htmlspecialchars($vehicle['name']) ?></h2>
                    <p>
                        <strong>Model:</strong> <?= $vehicle['model_year'] ?><br>
                        <strong>Tank Capacity:</strong> <?= $vehicle['tank_capacity'] ?> L
                    </p>
                    <div class="card-actions">
                        <a href="vehicle_dashboard.php?vid=<?= $vehicle['id'] ?>" class="btn">View Logs</a>
                        <a href="delete_vehicle.php?vid=<?= $vehicle['id'] ?>" class="btn btn-danger" onclick="showConfirmModal(event)">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <p>You haven't added any vehicles yet. Add your first one below!</p>
            </div>
        <?php endif; ?>
    </div>

    <hr style="margin: 2.5rem 0; border: none; border-top: 1px solid #e5e7eb;">

    <div class="card">
        <h2 class="card-title">Add a New Vehicle</h2>
        <form action="add_vehicle.php" method="POST" class="form-grid">
            <div>
                <label for="name">Vehicle Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label for="model_year">Model Year *</label>
                <input type="number" id="model_year" name="model_year" min="1900" max="<?= date("Y") ?>" required>
            </div>
            <div>
                <label for="tank_capacity">Tank Capacity (in Liters) *</label>
                <input type="number" id="tank_capacity" name="tank_capacity" step="0.1" required>
            </div>
            <div class="form-full-width">
                <input type="submit" value="Add Vehicle" class="btn">
            </div>
        </form>
    </div>
</div>

<div id="confirm-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h2 class="modal-title">Delete Vehicle</h2>
        <p>Are you sure? Deleting this vehicle will also permanently delete all of its fuel logs.</p>
        <div class="modal-actions">
            <button id="modal-cancel-btn" class="btn btn-outline">Cancel</button>
            <a id="modal-confirm-btn" href="#" class="btn btn-delete">Yes, Delete</a>
        </div>
    </div>
</div>

<style>
.form-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
@media (min-width: 768px) { .form-grid { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); } }
.form-full-width { grid-column: 1 / -1; }
.form-grid label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary-text); }
.form-grid input[type="text"], .form-grid input[type="number"] {
    width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;
}
.form-grid input[type="submit"] { width: 100%; border: none; cursor: pointer; }
.card-actions { margin-top: 1.5rem; display: flex; gap: 0.5rem; }
.btn-danger { background-color: #e53e3e; }
.btn-danger:hover { background-color: #c53030; }
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

<?php include 'layout_footer.php'; ?>