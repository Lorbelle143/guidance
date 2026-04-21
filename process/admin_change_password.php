<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../admin/change_password.php');
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request.');
    redirect('../admin/change_password.php');
}

$current  = $_POST['current_password'] ?? '';
$new      = $_POST['new_password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if (empty($current) || empty($new) || empty($confirm)) {
    setFlash('error', 'All fields are required.');
    redirect('../admin/change_password.php');
}
if (strlen($new) < 6) {
    setFlash('error', 'New password must be at least 6 characters.');
    redirect('../admin/change_password.php');
}
if ($new !== $confirm) {
    setFlash('error', 'New passwords do not match.');
    redirect('../admin/change_password.php');
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !verifyPassword($current, $user['password'])) {
        setFlash('error', 'Current password is incorrect.');
        redirect('../admin/change_password.php');
    }

    $hash = hashPassword($new);
    $db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?")->execute([$hash, $_SESSION['user_id']]);

    // Force re-login after password change
    session_destroy();
    session_start();
    setFlash('success', 'Password changed successfully. Please log in again.');
    redirect('../auth/login.php?tab=admin');

} catch (PDOException $e) {
    error_log("Admin change password error: " . $e->getMessage());
    setFlash('error', 'An error occurred. Please try again.');
    redirect('../admin/change_password.php');
}
