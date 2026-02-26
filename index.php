<?php
/**
 * Application Entry Point
 */
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect to dashboard if authenticated, otherwise to login
if (isAuthenticated()) {
    redirect('admin/dashboard.php');
} else {
    redirect('auth/login.php');
}
