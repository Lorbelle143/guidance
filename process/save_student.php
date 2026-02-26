<?php
/**
 * Save Student Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../admin/add_student.php');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request. Please try again.');
    redirect('../admin/add_student.php');
}

// Validate and sanitize input
$student_id = sanitize($_POST['student_id'] ?? '');
$last_name = sanitize($_POST['last_name'] ?? '');
$first_name = sanitize($_POST['first_name'] ?? '');
$middle_name = sanitize($_POST['middle_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');

$errors = [];

// Validate required fields
if (empty($student_id)) {
    $errors[] = "Student ID is required.";
}

if (empty($last_name)) {
    $errors[] = "Last name is required.";
}

if (empty($first_name)) {
    $errors[] = "First name is required.";
}

// Validate email if provided
if (!empty($email) && !validateEmail($email)) {
    $errors[] = "Invalid email format.";
}

// Validate file upload
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
    $errors[] = "Photo is required.";
} else {
    $fileErrors = validateFileUpload($_FILES['photo']);
    if (!empty($fileErrors)) {
        $errors = array_merge($errors, $fileErrors);
    }
}

// If there are validation errors, redirect back
if (!empty($errors)) {
    setFlash('error', implode('<br>', $errors));
    redirect('../admin/add_student.php');
}

try {
    $db = getDB();
    
    // Check if student ID already exists
    $checkStmt = $db->prepare("SELECT id FROM students WHERE student_id = ?");
    $checkStmt->execute([$student_id]);
    
    if ($checkStmt->fetch()) {
        setFlash('error', 'Student ID already exists.');
        redirect('../admin/add_student.php');
    }
    
    // Upload file
    $uploadResult = uploadFile($_FILES['photo']);
    
    if (!$uploadResult['success']) {
        setFlash('error', implode('<br>', $uploadResult['errors']));
        redirect('../admin/add_student.php');
    }
    
    $filename = $uploadResult['filename'];
    
    // Insert student record
    $stmt = $db->prepare("
        INSERT INTO students (student_id, last_name, first_name, middle_name, email, photo, created_by, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $student_id,
        $last_name,
        $first_name,
        $middle_name,
        $email,
        $filename,
        $_SESSION['user_id']
    ]);
    
    setFlash('success', 'Student record added successfully.');
    redirect('../admin/view_students.php');
    
} catch (PDOException $e) {
    error_log("Save student error: " . $e->getMessage());
    
    // Delete uploaded file if database insert fails
    if (isset($filename) && file_exists(UPLOAD_PATH . $filename)) {
        unlink(UPLOAD_PATH . $filename);
    }
    
    setFlash('error', 'An error occurred while saving the student record.');
    redirect('../admin/add_student.php');
}
