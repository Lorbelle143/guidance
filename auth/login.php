<?php
/**
 * Login Page
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isAuthenticated()) {
    redirect('../admin/dashboard.php');
}

$pageTitle = 'Login - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg" style="width: 450px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="bi bi-journal-medical text-primary" style="font-size: 3rem;"></i>
                <h3 class="mt-3">Guidance Office System</h3>
                <p class="text-muted">Sign in to your account</p>
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

            <form action="login_process.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" id="username" class="form-control" 
                               placeholder="Enter username" required autofocus>
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

                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>