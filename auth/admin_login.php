<?php
/**
 * Admin Login Page
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

$pageTitle = 'Admin Login - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg" style="width: 450px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock text-primary" style="font-size: 3rem;"></i>
                <h3 class="mt-3">Admin Portal</h3>
                <p class="text-muted">Sign in to manage the system</p>
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

            <form action="admin_login_process.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="mb-4">
                    <label for="master_key" class="form-label">Master Key</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                        <input type="password" name="master_key" id="master_key" class="form-control" 
                               placeholder="Enter master key" required autofocus>
                    </div>
                    <small class="text-muted">Contact system administrator if you don't have the master key</small>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
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
