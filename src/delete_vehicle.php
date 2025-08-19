<?php
require 'auth.php';
require 'db.php';

$user_id = $_SESSION["user_id"];
$vehicle_id_to_delete = isset($_GET['vid']) ? (int)$_GET['vid'] : 0;

if ($vehicle_id_to_delete > 0) {
    $stmt = $conn->prepare("SELECT id FROM vehicles WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $vehicle_id_to_delete, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $delete_stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
        $delete_stmt->bind_param("i", $vehicle_id_to_delete);
        $delete_stmt->execute();
        $delete_stmt->close();
    }
    $stmt->close();
}

header("Location: vehicles.php");
exit;
?>