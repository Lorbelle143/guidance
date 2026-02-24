<?php
require('../libs/fpdf/fpdf.php');
require_once '../config/database.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Student Inventory List',0,1,'C');
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial','B',10);
$pdf->Cell(30,8,'Student ID',1);
$pdf->Cell(40,8,'Last Name',1);
$pdf->Cell(40,8,'First Name',1);
$pdf->Cell(50,8,'Email',1);
$pdf->Cell(30,8,'Course',1);
$pdf->Ln();

// Table Body
$pdf->SetFont('Arial','',10);
$result = mysqli_query($conn,"SELECT * FROM students");
while($row=mysqli_fetch_assoc($result)){
    $pdf->Cell(30,8,$row['student_id'],1);
    $pdf->Cell(40,8,$row['last_name'],1);
    $pdf->Cell(40,8,$row['first_name'],1);
    $pdf->Cell(50,8,$row['email'],1);
    $pdf->Cell(30,8,$row['course'],1);
    $pdf->Ln();
}

$pdf->Output('I','students.pdf');
?>