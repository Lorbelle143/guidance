<?php
/**
 * Login Process Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Check if already logged in
if (isAuthenticated()) {
    redirect('../admin/dashboard.php');
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request. Please try again.');
    redirect('login.php');
}

// Validate input
$username = sanitize($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    setFlash('error', 'Username and password are required.');
    redirect('login.php');
}

try {
    $db = getDB();
    
    // Prepare statement to prevent SQL injection
    $stmt = $db->prepare("SELECT id, username, password, full_name FROM users WHERE username = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && verifyPassword($password, $user['password'])) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['login_time'] = time();
        
        // Update last login
        $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        
        setFlash('success', 'Welcome back, ' . sanitize($user['full_name']) . '!');
        redirect('../admin/dashboard.php');
    } else {
        // Log failed attempt
        error_log("Failed login attempt for username: " . $username);
        
        setFlash('error', 'Invalid username or password.');
        redirect('login.php');
    }
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    setFlash('error', 'An error occurred. Please try again later.');
    redirect('login.php');
}
