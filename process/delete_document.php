<?php
/**
 * Student Delete Document Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireStudent();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('error', 'Invalid document ID.');
    redirect('../student/upload_document.php');
}

try {
    $db = getDB();
    
    // Get document info and verify ownership
    $stmt = $db->prepare("SELECT * FROM student_documents WHERE id = ? AND student_id = ?");
    $stmt->execute([$id, $_SESSION['student_id']]);
    $document = $stmt->fetch();
    
    if (!$document) {
        setFlash('error', 'Document not found or access denied.');
        redirect('../student/upload_document.php');
    }
    
    // Delete document record
    $deleteStmt = $db->prepare("DELETE FROM student_documents WHERE id = ?");
    $deleteStmt->execute([$id]);
    
    // Delete file
    $filePath = BASE_PATH . '/uploads/documents/' . $document['file_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    setFlash('success', 'Document deleted successfully.');
    redirect('../student/upload_document.php');
    
} catch (PDOException $e) {
    error_log("Delete document error: " . $e->getMessage());
    setFlash('error', 'An error occurred while deleting the document.');
    redirect('../student/upload_document.php');
}
