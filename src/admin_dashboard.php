<?php
$page_title = "Admin - Reset Passwords";
include 'layout_header.php'; 
require 'admin_auth.php';
require 'db.php';

$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, nickname, email FROM users WHERE id != ? ORDER BY nickname ASC");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

$success_message = isset($_GET['success']) ? 'Password updated successfully!' : '';
?>

<div class="container">
    <div class="welcome-section">
        <h1>🔑 Admin: Reset User Password</h1>
        <p>Set a new temporary password for users who have lost access to their accounts.</p>
    </div>

    <?php if ($success_message): ?>
        <div class="success-message"><?= $success_message ?></div>
    <?php endif; ?>

    <div class="card">
        <h2 class="card-title">User Accounts</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nickname</th>
                        <th>Email</th>
                        <th style="width: 350px;">Set New Password</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['nickname']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <form action="admin_reset_password.php" method="POST" class="reset-form">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="text" name="new_password" placeholder="Enter new password" required>
                                        <button type="submit" class="btn btn-small">Reset</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 1rem;">No other users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.table-container { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
th { background-color: #f9fafb; font-weight: 600; }
.reset-form { display: flex; gap: 0.5rem; }
.reset-form input[type="text"] {
    flex-grow: 1; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;
}
.btn-small { padding: 0.5rem 1rem; }
.success-message {
    background-color: #dcfce7; color: #16a34a; padding: 1rem;
    border-radius: 0.5rem; margin-bottom: 2rem; text-align: center; font-weight: 500;
}
</style>

<?php include 'layout_footer.php'; ?>