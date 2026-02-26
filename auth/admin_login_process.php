<?php
/**
 * Admin Login Process Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/master_key.php';
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
    redirect('admin_login.php');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request. Please try again.');
    redirect('admin_login.php');
}

// Validate input
$master_key = $_POST['master_key'] ?? '';

if (empty($master_key)) {
    setFlash('error', 'Master key is required.');
    redirect('admin_login.php');
}

// Verify master key
if ($master_key === MASTER_KEY) {
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = 999999; // Use a high number instead of 0
    $_SESSION['username'] = MASTER_USERNAME;
    $_SESSION['full_name'] = 'Administrator';
    $_SESSION['role'] = 'admin';
    $_SESSION['user_type'] = 'admin';
    $_SESSION['is_master'] = true;
    $_SESSION['login_time'] = time();
    
    error_log("Admin login successful - redirecting to dashboard");
    setFlash('success', 'Welcome, Administrator!');
    redirect('../admin/admin_dashboard.php');
} else {
    error_log("Failed admin login attempt with invalid master key");
    setFlash('error', 'Invalid master key.');
    redirect('admin_login.php');
}
