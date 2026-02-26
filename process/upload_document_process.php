<?php
/**
 * Student Document Upload Process
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireStudent();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../student/upload_document.php');
}

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request.');
    redirect('../student/upload_document.php');
}

$document_type = sanitize($_POST['document_type'] ?? '');
$notes = sanitize($_POST['notes'] ?? '');

$errors = [];

// Validate document type
$validTypes = ['inventory_form', 'whodas', 'pid5', 'consent_form', 'other'];
if (empty($document_type) || !in_array($document_type, $validTypes)) {
    $errors[] = "Please select a valid document type.";
}

// Validate file upload
if (!isset($_FILES['document']) || $_FILES['document']['error'] === UPLOAD_ERR_NO_FILE) {
    $errors[] = "Please select a document to upload.";
} else {
    $file = $_FILES['document'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error occurred.";
    }
    
    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        $errors[] = "File size exceeds 10MB maximum.";
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    if (!in_array($ext, $allowedExts)) {
        $errors[] = "File type not allowed. Allowed types: JPG, PNG, GIF, PDF";
    }
}

if (!empty($errors)) {
    setFlash('error', implode('<br>', $errors));
    redirect('../student/upload_document.php');
}

try {
    $db = getDB();
    
    // Create documents directory if it doesn't exist
    $docPath = BASE_PATH . '/uploads/documents/';
    if (!file_exists($docPath)) {
        if (!mkdir($docPath, 0755, true)) {
            error_log("Failed to create documents directory: " . $docPath);
            setFlash('error', 'Failed to create upload directory. Please contact administrator.');
            redirect('../student/upload_document.php');
        }
    }
    
    // Check if directory is writable
    if (!is_writable($docPath)) {
        error_log("Documents directory is not writable: " . $docPath);
        setFlash('error', 'Upload directory is not writable. Please contact administrator.');
        redirect('../student/upload_document.php');
    }
    
    // Generate unique filename
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'doc_' . $_SESSION['student_id'] . '_' . uniqid() . '.' . $ext;
    $destination = $docPath . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Insert document record
        $stmt = $db->prepare("
            INSERT INTO student_documents (student_id, document_type, document_name, file_path, file_size, notes, uploaded_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $_SESSION['student_id'],
            $document_type,
            $file['name'],
            $filename,
            $file['size'],
            $notes
        ]);
        
        setFlash('success', 'Document uploaded successfully!');
        redirect('../student/upload_document.php');
    } else {
        error_log("Failed to move uploaded file to: " . $destination);
        setFlash('error', 'Failed to save document. Please try again.');
        redirect('../student/upload_document.php');
    }
    
} catch (PDOException $e) {
    error_log("Upload document error: " . $e->getMessage());
    
    // Delete uploaded file if database insert fails
    if (isset($destination) && file_exists($destination)) {
        unlink($destination);
    }
    
    setFlash('error', 'Database error: ' . $e->getMessage());
    redirect('../student/upload_document.php');
} catch (Exception $e) {
    error_log("Upload document exception: " . $e->getMessage());
    setFlash('error', 'An error occurred: ' . $e->getMessage());
    redirect('../student/upload_document.php');
}
