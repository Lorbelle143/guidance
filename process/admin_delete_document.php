<?php
/**
 * Admin Delete Document Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('error', 'Invalid document ID.');
    redirect('../admin/view_documents.php');
}

try {
    $db = getDB();
    
    // Get document info
    $stmt = $db->prepare("SELECT * FROM student_documents WHERE id = ?");
    $stmt->execute([$id]);
    $document = $stmt->fetch();
    
    if (!$document) {
        setFlash('error', 'Document not found.');
        redirect('../admin/view_documents.php');
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
    redirect('../admin/view_documents.php');
    
} catch (PDOException $e) {
    error_log("Admin delete document error: " . $e->getMessage());
    setFlash('error', 'An error occurred while deleting the document.');
    redirect('../admin/view_documents.php');
}
