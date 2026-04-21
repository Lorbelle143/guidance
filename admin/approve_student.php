<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    try {
        $db = getDB();
        $db->prepare("UPDATE students SET is_active=1 WHERE id=?")->execute([$id]);
        setFlash('success', 'Student account approved successfully.');
    } catch (PDOException $e) {
        setFlash('error', 'Error approving account.');
    }
}
redirect('view_students.php');
