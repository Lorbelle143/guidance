<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
require_once __DIR__.'/../includes/notifications.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../admin/send_notification.php');
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request.');
    redirect('../admin/send_notification.php');
}

$recipient  = sanitize($_POST['recipient'] ?? 'all');
$studentId  = ($recipient === 'specific') ? (int)($_POST['student_id'] ?? 0) : null;
$subject    = trim(sanitize($_POST['subject'] ?? ''));
$message    = trim(sanitize($_POST['message'] ?? ''));
$sendBell   = isset($_POST['send_bell']);
$sendEmail  = isset($_POST['send_email']);
$adminId    = $_SESSION['user_id'] ?? null;

if (empty($subject) || empty($message)) {
    setFlash('error', 'Subject and message are required.');
    redirect('../admin/send_notification.php');
}
if ($recipient === 'specific' && !$studentId) {
    setFlash('error', 'Please select a student.');
    redirect('../admin/send_notification.php');
}

try {
    $db = getDB();

    // Get target students
    if ($studentId) {
        $stmt = $db->prepare("SELECT id, first_name, last_name, email FROM students WHERE id = ? AND is_active = 1");
        $stmt->execute([$studentId]);
        $targets = $stmt->fetchAll();
    } else {
        $targets = $db->query("SELECT id, first_name, last_name, email FROM students WHERE is_active = 1")->fetchAll();
    }

    if (empty($targets)) {
        setFlash('error', 'No active students found.');
        redirect('../admin/send_notification.php');
    }

    $bellCount  = 0;
    $emailCount = 0;
    $emailFail  = 0;

    if ($sendBell) {
        // One notification record — student_id NULL = broadcast, or specific
        createNotification($db, 'admin_to_student', $studentId, $subject, $message, $adminId);
        $bellCount = count($targets);
    }

    if ($sendEmail) {
        foreach ($targets as $t) {
            if (!empty($t['email'])) {
                $sent = sendEmail($t['email'], $t['first_name'].' '.$t['last_name'], $subject, $message);
                $sent ? $emailCount++ : $emailFail++;
            }
        }
    }

    $parts = [];
    if ($sendBell)  $parts[] = "Bell notification sent to {$bellCount} student(s).";
    if ($sendEmail) {
        if (empty(SMTP_USER)) {
            $parts[] = "Email skipped — SMTP not configured yet. Set SMTP_USER in .env to enable.";
        } else {
            $parts[] = "Email sent to {$emailCount} student(s)" . ($emailFail > 0 ? " ({$emailFail} failed — check SMTP credentials in .env)." : ".");
        }
    }
    setFlash('success', implode(' ', $parts));

} catch (PDOException $e) {
    error_log("Send notification error: " . $e->getMessage());
    setFlash('error', 'Database error. Please try again.');
}

redirect('../admin/send_notification.php');
