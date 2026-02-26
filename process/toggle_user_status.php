<?php
/**
 * Toggle User Status (Admin Only)
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('error', 'Invalid user ID.');
    redirect('../admin/manage_users.php');
}

// Prevent admin from deactivating themselves
if ($id == $_SESSION['user_id']) {
    setFlash('error', 'You cannot deactivate your own account.');
    redirect('../admin/manage_users.php');
}

try {
    $db = getDB();
    
    // Get current status
    $stmt = $db->prepare("SELECT is_active FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        setFlash('error', 'User not found.');
        redirect('../admin/manage_users.php');
    }
    
    // Toggle status
    $newStatus = $user['is_active'] ? 0 : 1;
    $updateStmt = $db->prepare("UPDATE users SET is_active = ? WHERE id = ?");
    $updateStmt->execute([$newStatus, $id]);
    
    $message = $newStatus ? 'User activated successfully.' : 'User deactivated successfully.';
    setFlash('success', $message);
    redirect('../admin/manage_users.php');
    
} catch (PDOException $e) {
    error_log("Toggle user status error: " . $e->getMessage());
    setFlash('error', 'An error occurred.');
    redirect('../admin/manage_users.php');
}
