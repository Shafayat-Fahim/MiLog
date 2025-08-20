<?php
require 'auth.php';
require 'db.php';
require('fpdf/fpdf.php');

$user_id = $_SESSION["user_id"];
$vehicle_id = isset($_GET['vid']) ? (int)$_GET['vid'] : 0;

if ($vehicle_id <= 0) {
    die("Invalid vehicle specified.");
}

$vehicle_stmt = $conn->prepare("SELECT name, model_year FROM vehicles WHERE id = ? AND user_id = ?");
$vehicle_stmt->bind_param("ii", $vehicle_id, $user_id);
$vehicle_stmt->execute();
$vehicle_result = $vehicle_stmt->get_result();

if ($vehicle_result->num_rows !== 1) {
    die("Error: Vehicle not found or you do not have permission to access it.");
}
$vehicle = $vehicle_result->fetch_assoc();
$vehicle_name = $vehicle['name'];
$model_year = $vehicle['model_year'];
$vehicle_stmt->close();

$logs_stmt = $conn->prepare("SELECT filled_at, odometer, fuel_liters, fuel_cost FROM fuel_logs WHERE vehicle_id = ? ORDER BY filled_at ASC");
$logs_stmt->bind_param("i", $vehicle_id);
$logs_stmt->execute();
$logs_result = $logs_stmt->get_result();
$logs = $logs_result->fetch_all(MYSQLI_ASSOC);
$logs_stmt->close();


class PDF extends FPDF
{
    function Header()
    {
        global $vehicle_name, $model_year;
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Fuel Log Report', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, $vehicle_name . ' (' . $model_year . ')', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function LogTable($header, $data)
    {
        $w = array(40, 45, 45, 45);
        $this->SetFont('Arial', 'B', 12);
        for($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        }
        $this->Ln();
        $this->SetFont('Arial', '', 12);
        foreach($data as $row) {
            $this->Cell($w[0], 6, date("M j, Y", strtotime($row['filled_at'])), 'LR');
            $this->Cell($w[1], 6, number_format($row['odometer']) . ' km', 'LR');
            $this->Cell($w[2], 6, number_format($row['fuel_liters'], 2) . ' L', 'LR');
            $this->Cell($w[3], 6, '$' . number_format($row['fuel_cost'], 2), 'LR');
            $this->Ln();
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$header = array('Date', 'Odometer', 'Fuel Volume', 'Total Cost');

$pdf->LogTable($header, $logs);

$filename = "fuel_logs_" . strtolower(str_replace(' ', '_', $vehicle_name)) . ".pdf";
$pdf->Output('D', $filename);

exit;
?>