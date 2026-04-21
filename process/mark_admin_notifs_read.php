<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) { http_response_code(403); exit; }
try {
    $db = getDB();
    $db->exec("UPDATE notifications SET admin_read=1 WHERE type='student_activity' AND admin_read=0");
    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false]);
}
