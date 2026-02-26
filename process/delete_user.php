<?php
/**
 * Delete User (Admin Only)
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

// Prevent admin from deleting themselves
if ($id == $_SESSION['user_id']) {
    setFlash('error', 'You cannot delete your own account.');
    redirect('../admin/manage_users.php');
}

try {
    $db = getDB();
    
    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    if (!$stmt->fetch()) {
        setFlash('error', 'User not found.');
        redirect('../admin/manage_users.php');
    }
    
    // Delete user
    $deleteStmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->execute([$id]);
    
    setFlash('success', 'User deleted successfully.');
    redirect('../admin/manage_users.php');
    
} catch (PDOException $e) {
    error_log("Delete user error: " . $e->getMessage());
    setFlash('error', 'An error occurred while deleting the user.');
    redirect('../admin/manage_users.php');
}
