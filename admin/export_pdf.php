<?php
/**
 * Export Students to PDF
 */
ob_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

// Check if FPDF library exists
if (!file_exists(__DIR__ . '/../libs/fpdf/fpdf.php')) {
    die('FPDF library not found. Please install it in the libs/fpdf directory.');
}

require(__DIR__ . '/../libs/fpdf/fpdf.php');

try {
    $db = getDB();
    
    // Search functionality
    $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
    $whereClause = '';
    $params = [];
    
    if (!empty($search)) {
        $whereClause = "WHERE student_id LIKE ? OR last_name LIKE ? OR first_name LIKE ? OR email LIKE ?";
        $searchParam = "%{$search}%";
        $params = [$searchParam, $searchParam, $searchParam, $searchParam];
    }
    
    $stmt = $db->prepare("SELECT * FROM students {$whereClause} ORDER BY last_name, first_name");
    $stmt->execute($params);
    $students = $stmt->fetchAll();
    
    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 18);
    
    // Title
    $pdf->Cell(0, 10, 'Student Records Report', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Generated: ' . date('F d, Y h:i A'), 0, 1, 'C');
    
    if (!empty($search)) {
        $pdf->Cell(0, 5, 'Search: ' . $search, 0, 1, 'C');
    }
    
    $pdf->Ln(5);
    
    // Table Header
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(13, 110, 253);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(30, 8, 'Student ID', 1, 0, 'C', true);
    $pdf->Cell(60, 8, 'Name', 1, 0, 'C', true);
    $pdf->Cell(70, 8, 'Email', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Date Added', 1, 1, 'C', true);
    
    // Table Body
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $fill = false;
    
    if (empty($students)) {
        $pdf->Cell(190, 10, 'No records found.', 1, 1, 'C');
    } else {
        foreach ($students as $student) {
            $name = $student['last_name'] . ', ' . $student['first_name'];
            if (!empty($student['middle_name'])) {
                $name .= ' ' . substr($student['middle_name'], 0, 1) . '.';
            }
            
            $email = !empty($student['email']) ? $student['email'] : 'N/A';
            $dateAdded = date('M d, Y', strtotime($student['created_at']));
            
            $pdf->Cell(30, 8, $student['student_id'], 1, 0, 'C', $fill);
            $pdf->Cell(60, 8, $name, 1, 0, 'L', $fill);
            $pdf->Cell(70, 8, $email, 1, 0, 'L', $fill);
            $pdf->Cell(30, 8, $dateAdded, 1, 1, 'C', $fill);
            
            $fill = !$fill;
        }
    }
    
    // Footer
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 5, 'Total Records: ' . count($students), 0, 1, 'L');
    $pdf->Cell(0, 5, 'Guidance Office Inventory System', 0, 1, 'L');
    
    ob_end_clean();
    $pdf->Output('D', 'student_records_' . date('Y-m-d') . '.pdf');
    exit;
    
} catch (PDOException $e) {
    error_log("Export PDF error: " . $e->getMessage());
    ob_end_clean();
    setFlash('error', 'An error occurred while generating the PDF.');
    redirect('view_students.php');
}
