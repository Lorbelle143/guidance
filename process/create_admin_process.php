<?php
/**
 * Create Admin Process Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../admin/create_admin.php');
}

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request.');
    redirect('../admin/create_admin.php');
}

$full_name = sanitize($_POST['full_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$username = sanitize($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$role = sanitize($_POST['role'] ?? 'user');

$errors = [];

if (empty($full_name)) {
    $errors[] = "Full name is required.";
}

if (empty($email)) {
    $errors[] = "Email is required.";
} elseif (!validateEmail($email)) {
    $errors[] = "Invalid email format.";
}

if (empty($username)) {
    $errors[] = "Username is required.";
} elseif (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
    $errors[] = "Username must be 4-20 characters and contain only letters, numbers, and underscore.";
}

if (empty($password)) {
    $errors[] = "Password is required.";
} elseif (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}

if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

if (!in_array($role, ['admin', 'user'])) {
    $errors[] = "Invalid role selected.";
}

if (!empty($errors)) {
    setFlash('error', implode('<br>', $errors));
    redirect('../admin/create_admin.php');
}

try {
    $db = getDB();
    
    // Check if username already exists
    $checkUsername = $db->prepare("SELECT id FROM users WHERE username = ?");
    $checkUsername->execute([$username]);
    
    if ($checkUsername->fetch()) {
        setFlash('error', 'Username already exists. Please choose another.');
        redirect('../admin/create_admin.php');
    }
    
    // Check if email already exists
    $checkEmail = $db->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->execute([$email]);
    
    if ($checkEmail->fetch()) {
        setFlash('error', 'Email already registered. Please use another email.');
        redirect('../admin/create_admin.php');
    }
    
    // Hash password
    $hashedPassword = hashPassword($password);
    
    // Insert new admin/user
    $stmt = $db->prepare("
        INSERT INTO users (username, password, full_name, email, role, is_active, created_at) 
        VALUES (?, ?, ?, ?, ?, 1, NOW())
    ");
    
    $stmt->execute([$username, $hashedPassword, $full_name, $email, $role]);
    
    setFlash('success', ucfirst($role) . ' account created successfully!');
    redirect('../admin/manage_users.php');
    
} catch (PDOException $e) {
    error_log("Create admin error: " . $e->getMessage());
    setFlash('error', 'An error occurred. Please try again later.');
    redirect('../admin/create_admin.php');
}
