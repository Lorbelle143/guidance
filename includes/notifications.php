<?php
/**
 * Notification helpers
 * Handles both in-app bell notifications and email sending via PHPMailer + SMTP.
 */

require_once __DIR__.'/../config/config.php';

// Load PHPMailer
require_once __DIR__.'/PHPMailer/Exception.php';
require_once __DIR__.'/PHPMailer/PHPMailer.php';
require_once __DIR__.'/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailException;

/**
 * Send email via SMTP (PHPMailer).
 * Configure SMTP credentials in .env or config/env.php.
 */
function sendEmail(string $to, string $toName, string $subject, string $message): bool {
    // If no SMTP user configured, skip silently
    if (empty(SMTP_USER)) {
        error_log("sendEmail: SMTP_USER not configured — skipping email to $to");
        return false;
    }

    $htmlBody = '<!DOCTYPE html><html><head><meta charset="UTF-8">
    <style>
      body{font-family:Arial,sans-serif;background:#f3f4f6;margin:0;padding:0}
      .wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)}
      .hd{background:linear-gradient(135deg,#1e3a8a,#1d4ed8);padding:28px 32px;text-align:center}
      .hd h1{color:#fff;font-size:1.2rem;margin:0;font-weight:700}
      .hd p{color:rgba(255,255,255,.75);font-size:.85rem;margin:6px 0 0}
      .body{padding:28px 32px}
      .body h2{font-size:1rem;color:#111827;margin:0 0 12px}
      .body p{font-size:.9rem;color:#374151;line-height:1.7;margin:0 0 16px}
      .msg-box{background:#f0f4ff;border-left:4px solid #1d4ed8;border-radius:6px;padding:16px 20px;margin:16px 0}
      .msg-box p{margin:0;color:#1e3a8a;font-size:.9rem;line-height:1.7}
      .ft{background:#f9fafb;padding:18px 32px;text-align:center;border-top:1px solid #e5e7eb}
      .ft p{font-size:.78rem;color:#9ca3af;margin:0}
    </style></head><body>
    <div class="wrap">
      <div class="hd">
        <h1>NBSC Guidance &amp; Counseling Office</h1>
        <p>Northern Bukidnon State College</p>
      </div>
      <div class="body">
        <h2>' . htmlspecialchars($subject) . '</h2>
        <p>Dear ' . htmlspecialchars($toName) . ',</p>
        <div class="msg-box"><p>' . nl2br(htmlspecialchars($message)) . '</p></div>
        <p>If you have questions, please contact the Guidance &amp; Counseling Office.</p>
      </div>
      <div class="ft">
        <p>&copy; ' . date('Y') . ' Northern Bukidnon State College &mdash; Guidance &amp; Counseling Office</p>
      </div>
    </div>
    </body></html>';

    try {
        $mail = new PHPMailer(true);

        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Sender & recipient
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to, $toName);
        $mail->addReplyTo('gco@nbsc.edu.ph', 'GCO NBSC');

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags($message);

        $mail->send();
        return true;

    } catch (MailException $e) {
        error_log("sendEmail failed to $to: " . $e->getMessage());
        return false;
    }
}

/**
 * Create an in-app notification record.
 *
 * @param PDO    $db
 * @param string $type         'admin_to_student' | 'student_activity'
 * @param int|null $studentId  target student (null = broadcast)
 * @param string $subject
 * @param string $message
 * @param int|null $sentBy     admin user id (for admin_to_student)
 * @param string|null $link    optional URL
 */
function createNotification(PDO $db, string $type, ?int $studentId, string $subject, string $message, ?int $sentBy = null, ?string $link = null): int {
    $stmt = $db->prepare("
        INSERT INTO notifications (type, sent_by, student_id, subject, message, link, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$type, $sentBy, $studentId, $subject, $message, $link]);
    return (int)$db->lastInsertId();
}

/**
 * Get unread admin bell count (student_activity notifications not yet read by admin).
 */
function getAdminUnreadCount(PDO $db): int {
    try {
        return (int)$db->query("SELECT COUNT(*) FROM notifications WHERE type='student_activity' AND admin_read=0")->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Get unread student bell count (admin_to_student for this student, not yet read).
 */
function getStudentUnreadCount(PDO $db, int $studentId): int {
    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM notifications
            WHERE type='admin_to_student'
              AND (student_id = ? OR student_id IS NULL)
              AND is_read = 0
        ");
        $stmt->execute([$studentId]);
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}
