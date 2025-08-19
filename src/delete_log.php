<?php
require 'auth.php';
require 'db.php';

$user_id = $_SESSION["user_id"];
$log_id = isset($_GET['log_id']) ? (int)$_GET['log_id'] : 0;

if ($log_id > 0) {
    $stmt = $conn->prepare(
        "SELECT fl.id, fl.vehicle_id FROM fuel_logs fl " .
        "JOIN vehicles v ON fl.vehicle_id = v.id " .
        "WHERE fl.id = ? AND v.user_id = ?"
    );
    $stmt->bind_param("ii", $log_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $log = $result->fetch_assoc();
        $vehicle_id = $log['vehicle_id'];

        $delete_stmt = $conn->prepare("DELETE FROM fuel_logs WHERE id = ?");
        $delete_stmt->bind_param("i", $log_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        header("Location: vehicle_dashboard.php?vid=" . $vehicle_id);
        exit;
    }
}

header("Location: dashboard.php");
exit;
?>