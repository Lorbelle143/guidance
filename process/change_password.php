<?php
/**
 * Change Password Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../admin/profile.php');
}

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request.');
    redirect('../admin/profile.php');
}

$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

$errors = [];

if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $errors[] = "All fields are required.";
}

if (strlen($new_password) < 6) {
    $errors[] = "New password must be at least 6 characters.";
}

if ($new_password !== $confirm_password) {
    $errors[] = "New passwords do not match.";
}

if (!empty($errors)) {
    setFlash('error', implode('<br>', $errors));
    redirect('../admin/profile.php');
}

try {
    $db = getDB();
    
    // Get current password hash
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user || !verifyPassword($current_password, $user['password'])) {
        setFlash('error', 'Current password is incorrect.');
        redirect('../admin/profile.php');
    }
    
    // Update password
    $newHash = hashPassword($new_password);
    $updateStmt = $db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
    $updateStmt->execute([$newHash, $_SESSION['user_id']]);
    
    setFlash('success', 'Password changed successfully.');
    redirect('../admin/profile.php');
    
} catch (PDOException $e) {
    error_log("Change password error: " . $e->getMessage());
    setFlash('error', 'An error occurred while changing password.');
    redirect('../admin/profile.php');
}
