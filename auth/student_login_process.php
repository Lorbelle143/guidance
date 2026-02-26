<?php
/**
 * Student Login Process Handler
 */
require_once __DIR__ . '/../config/database.php';
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
    redirect('student_login.php');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request. Please try again.');
    redirect('student_login.php');
}

// Validate input
$student_id = sanitize($_POST['student_id'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($student_id) || empty($password)) {
    setFlash('error', 'Student ID and password are required.');
    redirect('student_login.php');
}

try {
    $db = getDB();
    
    // Student login
    $stmt = $db->prepare("SELECT id, student_id, password, first_name, last_name, is_active FROM students WHERE student_id = ? LIMIT 1");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        error_log("Failed student login attempt - student not found: " . $student_id);
        setFlash('error', 'Invalid Student ID or password.');
        redirect('student_login.php');
    }
    
    if (!$student['is_active']) {
        error_log("Failed student login attempt - account inactive: " . $student_id);
        setFlash('error', 'Your account is inactive. Please contact the guidance office.');
        redirect('student_login.php');
    }
    
    if (verifyPassword($password, $student['password'])) {
        session_regenerate_id(true);
        
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_number'] = $student['student_id'];
        $_SESSION['username'] = $student['student_id'];
        $_SESSION['full_name'] = $student['first_name'] . ' ' . $student['last_name'];
        $_SESSION['user_type'] = 'student';
        $_SESSION['login_time'] = time();
        
        // Update last login
        $updateStmt = $db->prepare("UPDATE students SET last_login = NOW() WHERE id = ?");
        $updateStmt->execute([$student['id']]);
        
        setFlash('success', 'Welcome back, ' . sanitize($student['first_name']) . '!');
        redirect('../student/student_dashboard.php');
    } else {
        error_log("Failed student login attempt - wrong password: " . $student_id);
        setFlash('error', 'Invalid Student ID or password.');
        redirect('student_login.php');
    }
} catch (PDOException $e) {
    error_log("Student login error: " . $e->getMessage());
    setFlash('error', 'An error occurred. Please try again later.');
    redirect('student_login.php');
}
