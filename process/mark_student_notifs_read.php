<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireStudent();
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) { http_response_code(403); exit; }
$sid = (int)$_SESSION['student_id'];
try {
    $db = getDB();
    $db->prepare("UPDATE notifications SET is_read=1 WHERE type='admin_to_student' AND (student_id=? OR student_id IS NULL) AND is_read=0")->execute([$sid]);
    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false]);
}
