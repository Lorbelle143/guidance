<?php
/**
 * Delete Student Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('error', 'Invalid student ID.');
    redirect('view_students.php');
}

try {
    $db = getDB();
    
    // Get student info
    $stmt = $db->prepare("SELECT photo FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        setFlash('error', 'Student not found.');
        redirect('view_students.php');
    }
    
    // Delete student record
    $deleteStmt = $db->prepare("DELETE FROM students WHERE id = ?");
    $deleteStmt->execute([$id]);
    
    // Delete photo file
    if (!empty($student['photo']) && file_exists(UPLOAD_PATH . $student['photo'])) {
        unlink(UPLOAD_PATH . $student['photo']);
    }
    
    setFlash('success', 'Student record deleted successfully.');
    redirect('view_students.php');
    
} catch (PDOException $e) {
    error_log("Delete student error: " . $e->getMessage());
    setFlash('error', 'An error occurred while deleting the student record.');
    redirect('view_students.php');
}
