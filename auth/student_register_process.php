<?php
/**
 * Student Registration Process Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Check if already logged in
if (isAuthenticated()) {
    if (isStudent()) {
        redirect('../student/student_dashboard.php');
    } else {
        redirect('../admin/admin_dashboard.php');
    }
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('student_register.php');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request. Please try again.');
    redirect('student_register.php');
}

// Validate and sanitize input
$student_id = sanitize($_POST['student_id'] ?? '');
$last_name = sanitize($_POST['last_name'] ?? '');
$first_name = sanitize($_POST['first_name'] ?? '');
$middle_name = sanitize($_POST['middle_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

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

if (empty($password)) {
    $errors[] = "Password is required.";
} elseif (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}

if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
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
    redirect('student_register.php');
}

try {
    $db = getDB();
    
    // Check if student ID already exists
    $checkStmt = $db->prepare("SELECT id FROM students WHERE student_id = ?");
    $checkStmt->execute([$student_id]);
    
    if ($checkStmt->fetch()) {
        setFlash('error', 'Student ID already exists. Please use a different Student ID or contact the guidance office.');
        redirect('student_register.php');
    }
    
    // Check if email already exists (if provided)
    if (!empty($email)) {
        $checkEmailStmt = $db->prepare("SELECT id FROM students WHERE email = ?");
        $checkEmailStmt->execute([$email]);
        
        if ($checkEmailStmt->fetch()) {
            setFlash('error', 'Email already registered. Please use a different email.');
            redirect('student_register.php');
        }
    }
    
    // Upload file
    $uploadResult = uploadFile($_FILES['photo']);
    
    if (!$uploadResult['success']) {
        setFlash('error', implode('<br>', $uploadResult['errors']));
        redirect('student_register.php');
    }
    
    $filename = $uploadResult['filename'];
    
    // Hash password
    $hashedPassword = hashPassword($password);
    
    // Insert student record
    $stmt = $db->prepare("
        INSERT INTO students (student_id, password, last_name, first_name, middle_name, email, photo, is_active, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())
    ");
    
    $stmt->execute([
        $student_id,
        $hashedPassword,
        $last_name,
        $first_name,
        $middle_name,
        $email,
        $filename
    ]);
    
    setFlash('success', 'Registration successful! You can now login with your Student ID and password.');
    redirect('student_login.php');
    
} catch (PDOException $e) {
    error_log("Student registration error: " . $e->getMessage());
    
    // Delete uploaded file if database insert fails
    if (isset($filename) && file_exists(UPLOAD_PATH . $filename)) {
        unlink(UPLOAD_PATH . $filename);
    }
    
    setFlash('error', 'An error occurred during registration. Please try again later.');
    redirect('student_register.php');
}
