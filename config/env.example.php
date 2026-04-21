<?php
/**
 * Environment-specific credentials
 *
 * HOW TO USE:
 *   1. Copy this file → config/env.php
 *   2. Fill in your InfinityFree (or other host) DB credentials
 *   3. env.php is in .gitignore — never commit it
 *
 * InfinityFree DB details are found in:
 *   iFastNet Panel → MySQL Databases → your database
 */

// ── InfinityFree / Production DB ─────────────────────────────────────────────
define('DB_HOST',    'sql123.infinityfree.com');   // from your hosting panel
define('DB_NAME',    'if0_xxxxxxxx_inventory_db'); // from your hosting panel
define('DB_USER',    'if0_xxxxxxxx');              // from your hosting panel
define('DB_PASS',    'your_db_password_here');
define('DB_CHARSET', 'utf8mb4');

// ── App URL ───────────────────────────────────────────────────────────────────
define('APP_URL', 'https://yourdomain.infinityfreeapp.com');
