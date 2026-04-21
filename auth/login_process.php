<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';

if (isAuthenticated()) {
    isStudent() ? redirect('../student/student_dashboard.php') : redirect('../admin/admin_dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('login.php');

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request. Please try again.');
    redirect('login.php');
}

$identifier = trim($_POST['identifier'] ?? '');
$password   = $_POST['password'] ?? '';

if (empty($identifier) || empty($password)) {
    setFlash('error', 'Please fill in all fields.');
    redirect('login.php');
}

try {
    $db = getDB();

    // ── 1. Check admin table (users) by email ─────────────────────────────────
    $stmt = $db->prepare("SELECT id, email, password, full_name, role FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$identifier]);
    $admin = $stmt->fetch();

    if ($admin && verifyPassword($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']    = $admin['id'];
        $_SESSION['username']   = $admin['email'];
        $_SESSION['full_name']  = $admin['full_name'];
        $_SESSION['role']       = $admin['role'];
        $_SESSION['user_type']  = 'admin';
        $_SESSION['login_time'] = time();

        $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);

        setFlash('success', 'Welcome back, ' . sanitize($admin['full_name']) . '!');
        redirect('../admin/admin_dashboard.php');
    }

    // ── 2. Check student table by student_id ──────────────────────────────────
    $stmt = $db->prepare("SELECT id, student_id, password, first_name, last_name, is_active FROM students WHERE student_id = ? LIMIT 1");
    $stmt->execute([$identifier]);
    $student = $stmt->fetch();

    if ($student) {
        if (!$student['is_active']) {
            setFlash('error', 'Your account is inactive. Please contact the guidance office.');
            redirect('login.php');
        }

        if (verifyPassword($password, $student['password'])) {
            session_regenerate_id(true);
            $_SESSION['student_id']     = $student['id'];
            $_SESSION['student_number'] = $student['student_id'];
            $_SESSION['username']       = $student['student_id'];
            $_SESSION['full_name']      = $student['first_name'] . ' ' . $student['last_name'];
            $_SESSION['user_type']      = 'student';
            $_SESSION['login_time']     = time();

            $db->prepare("UPDATE students SET last_login = NOW() WHERE id = ?")->execute([$student['id']]);

            setFlash('success', 'Welcome back, ' . sanitize($student['first_name']) . '!');
            redirect('../student/student_dashboard.php');
        }
    }

    // ── 3. Nothing matched ────────────────────────────────────────────────────
    setFlash('error', 'Invalid credentials. Please check your email/Student ID and password.');
    redirect('login.php');

} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    setFlash('error', 'An error occurred. Please try again later.');
    redirect('login.php');
}
