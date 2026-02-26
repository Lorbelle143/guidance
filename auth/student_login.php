<?php
/**
 * Student Login Page
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isAuthenticated()) {
    if (isStudent()) {
        redirect('../student/student_dashboard.php');
    } else {
        redirect('../admin/admin_dashboard.php');
    }
}

$pageTitle = 'Student Login - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg" style="width: 450px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="bi bi-person-badge text-success" style="font-size: 3rem;"></i>
                <h3 class="mt-3">Student Portal</h3>
                <p class="text-muted">Sign in to access your information</p>
            </div>

            <?php
            $flash = getFlash();
            if ($flash):
                $alertType = $flash['type'] === 'error' ? 'danger' : 'success';
            ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo sanitize($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form action="student_login_process.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                        <input type="text" name="student_id" id="student_id" class="form-control" 
                               placeholder="Enter your Student ID" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Enter password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 mb-3">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>

                <div class="text-center">
                    <p class="mb-0">Don't have an account? 
                        <a href="student_register.php" class="text-decoration-none fw-bold">Register here</a>
                    </p>
                </div>
            </form>

            <hr class="my-4">
            
            <div class="text-center">
                <a href="../index.php" class="text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
