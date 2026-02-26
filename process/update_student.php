<?php
/**
 * Update Student Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin(); // Only admins can update students

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../admin/view_students.php');
}

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request.');
    redirect('../admin/view_students.php');
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$student_id = sanitize($_POST['student_id'] ?? '');
$last_name = sanitize($_POST['last_name'] ?? '');
$first_name = sanitize($_POST['first_name'] ?? '');
$middle_name = sanitize($_POST['middle_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$current_photo = sanitize($_POST['current_photo'] ?? '');

$errors = [];

if ($id <= 0) {
    $errors[] = "Invalid student ID.";
}

if (empty($student_id) || empty($last_name) || empty($first_name)) {
    $errors[] = "Required fields are missing.";
}

if (!empty($email) && !validateEmail($email)) {
    $errors[] = "Invalid email format.";
}

if (!empty($errors)) {
    setFlash('error', implode('<br>', $errors));
    redirect('../admin/edit_student.php?id=' . $id);
}

try {
    $db = getDB();
    
    // Check if student exists
    $checkStmt = $db->prepare("SELECT id FROM students WHERE id = ?");
    $checkStmt->execute([$id]);
    if (!$checkStmt->fetch()) {
        setFlash('error', 'Student not found.');
        redirect('../admin/view_students.php');
    }
    
    // Check if student_id is taken by another student
    $dupStmt = $db->prepare("SELECT id FROM students WHERE student_id = ? AND id != ?");
    $dupStmt->execute([$student_id, $id]);
    if ($dupStmt->fetch()) {
        setFlash('error', 'Student ID already exists.');
        redirect('../admin/edit_student.php?id=' . $id);
    }
    
    $filename = $current_photo;
    
    // Handle new photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $fileErrors = validateFileUpload($_FILES['photo']);
        
        if (!empty($fileErrors)) {
            setFlash('error', implode('<br>', $fileErrors));
            redirect('../admin/edit_student.php?id=' . $id);
        }
        
        $uploadResult = uploadFile($_FILES['photo']);
        
        if (!$uploadResult['success']) {
            setFlash('error', implode('<br>', $uploadResult['errors']));
            redirect('../admin/edit_student.php?id=' . $id);
        }
        
        // Delete old photo
        if (!empty($current_photo) && file_exists(UPLOAD_PATH . $current_photo)) {
            unlink(UPLOAD_PATH . $current_photo);
        }
        
        $filename = $uploadResult['filename'];
    }
    
    // Update student record
    $stmt = $db->prepare("
        UPDATE students 
        SET student_id = ?, last_name = ?, first_name = ?, middle_name = ?, email = ?, photo = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->execute([$student_id, $last_name, $first_name, $middle_name, $email, $filename, $id]);
    
    setFlash('success', 'Student record updated successfully.');
    redirect('../admin/view_students.php');
    
} catch (PDOException $e) {
    error_log("Update student error: " . $e->getMessage());
    setFlash('error', 'An error occurred while updating the student record.');
    redirect('../admin/edit_student.php?id=' . $id);
}
