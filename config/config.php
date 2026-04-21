<?php
/**
 * Application Configuration
 * Works on both localhost (XAMPP) and InfinityFree shared hosting.
 *
 * Environment is detected automatically:
 *   - localhost / 127.0.0.1  → development
 *   - everything else        → production (InfinityFree)
 *
 * To override DB credentials for production, edit config/env.php
 * (that file is gitignored and never committed).
 */

// ── Auto-detect environment ───────────────────────────────────────────────────
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocal = in_array($host, ['localhost', '127.0.0.1']) ||
           str_starts_with($host, 'localhost:');

define('APP_ENV', $isLocal ? 'development' : 'production');

// ── Load per-environment credentials from env.php (if it exists) ─────────────
// env.php is NOT committed to git. Copy env.example.php → env.php and fill in.
$envFile = __DIR__ . '/env.php';
if (file_exists($envFile)) {
    require_once $envFile;
}

// ── Database ──────────────────────────────────────────────────────────────────
if (!defined('DB_HOST')) {
    // Localhost defaults (XAMPP)
    define('DB_HOST',    'localhost');
    define('DB_NAME',    'inventory_db');
    define('DB_USER',    'root');
    define('DB_PASS',    '');
    define('DB_CHARSET', 'utf8mb4');
}

// ── Application ───────────────────────────────────────────────────────────────
if (!defined('APP_URL')) {
    define('APP_URL', $isLocal ? 'http://localhost' : 'https://' . $host);
}

define('BASE_PATH', dirname(__DIR__));

// ── Session ───────────────────────────────────────────────────────────────────
define('SESSION_LIFETIME', 7200); // 2 hours

// ── Uploads ───────────────────────────────────────────────────────────────────
define('UPLOAD_PATH',       BASE_PATH . '/uploads/');
define('UPLOAD_MAX_SIZE',   5242880); // 5 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// ── Error reporting ───────────────────────────────────────────────────────────
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ── Timezone ──────────────────────────────────────────────────────────────────
date_default_timezone_set('Asia/Manila');

// ── SMTP Email ────────────────────────────────────────────────────────────────
// Load from .env if present, otherwise fall back to env.php or defaults
if (!defined('SMTP_HOST')) {
    // Try loading from .env values already parsed above
    $envLines = [];
    if (file_exists(__DIR__.'/../.env')) {
        foreach (file(__DIR__.'/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
            [$k, $v] = explode('=', $line, 2);
            $envLines[trim($k)] = trim($v);
        }
    }
    define('SMTP_HOST',       $envLines['SMTP_HOST']       ?? 'smtp.gmail.com');
    define('SMTP_PORT',       (int)($envLines['SMTP_PORT'] ?? 587));
    define('SMTP_USER',       $envLines['SMTP_USER']       ?? '');
    define('SMTP_PASS',       $envLines['SMTP_PASS']       ?? '');
    define('SMTP_FROM_NAME',  $envLines['SMTP_FROM_NAME']  ?? 'NBSC Guidance & Counseling Office');
    define('SMTP_FROM_EMAIL', $envLines['SMTP_FROM_EMAIL'] ?? ($envLines['SMTP_USER'] ?? ''));
}
