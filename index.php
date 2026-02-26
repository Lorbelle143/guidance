<?php
/**
 * Application Entry Point
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

// Show landing page with login options
$pageTitle = 'Guidance Office System';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="row w-100">
        <div class="col-md-10 mx-auto">
            <div class="text-center mb-5">
                <i class="bi bi-journal-medical text-primary" style="font-size: 5rem;"></i>
                <h1 class="mt-3">Guidance Office System</h1>
                <p class="text-muted">Select your login type to continue</p>
            </div>

            <div class="row g-4">
                <!-- Admin Login -->
                <div class="col-md-6">
                    <div class="card shadow-lg h-100 border-primary">
                        <div class="card-body text-center p-5">
                            <i class="bi bi-shield-lock text-primary" style="font-size: 4rem;"></i>
                            <h3 class="mt-3">Admin Portal</h3>
                            <p class="text-muted mb-4">For administrators and staff members</p>
                            <a href="auth/admin_login.php" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Admin Login
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Student Login -->
                <div class="col-md-6">
                    <div class="card shadow-lg h-100 border-success">
                        <div class="card-body text-center p-5">
                            <i class="bi bi-person-badge text-success" style="font-size: 4rem;"></i>
                            <h3 class="mt-3">Student Portal</h3>
                            <p class="text-muted mb-4">For registered students</p>
                            <a href="auth/student_login.php" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Student Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
