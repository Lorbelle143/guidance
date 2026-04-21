<?php
/**
 * Logout Handler
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Check user type before clearing session
$isStudent = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student';

// Clear all session data
$_SESSION = [];

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Start new session for flash message
session_start();
setFlash('success', 'You have been logged out successfully.');

// Redirect based on user type
if ($isStudent) {
    redirect('login.php');
} else {
    redirect('login.php');
}
