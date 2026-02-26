<?php
/**
 * Student Registration Page
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

$pageTitle = 'Student Registration - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; padding: 20px 0;">
    <div class="card shadow-lg" style="width: 600px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill text-success" style="font-size: 3rem;"></i>
                <h3 class="mt-3">Student Registration</h3>
                <p class="text-muted">Create your student account</p>
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

            <form action="student_register_process.php" method="POST" enctype="multipart/form-data" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                        <input type="text" name="student_id" id="student_id" class="form-control" 
                               placeholder="e.g., 2024-001" required pattern="[A-Za-z0-9\-]+" 
                               title="Only letters, numbers, and hyphens allowed">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" 
                               placeholder="your.email@example.com">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-control" 
                               placeholder="Enter last name" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control" 
                               placeholder="Enter first name" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="middle_name" class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" class="form-control" 
                           placeholder="Enter middle name (optional)">
                </div>

                <div class="mb-3">
                    <label for="photo" class="form-label">Upload Photo <span class="text-danger">*</span></label>
                    <input type="file" name="photo" id="photo" class="form-control" 
                           accept="image/jpeg,image/png,image/gif" required>
                    <div class="form-text">
                        Allowed formats: JPG, PNG, GIF. Maximum size: 5MB
                    </div>
                </div>

                <div class="mb-3" id="imagePreview" style="display: none;">
                    <label class="form-label">Preview</label>
                    <div>
                        <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Create a password" required minlength="6">
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
                               placeholder="Confirm your password" required minlength="6">
                    </div>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-person-plus"></i> Create Account
                    </button>
                </div>

                <div class="text-center">
                    <p class="mb-0">Already have an account? 
                        <a href="student_login.php" class="text-decoration-none fw-bold">Sign In</a>
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

<script>
// Image preview
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

// Form validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    const fileInput = document.getElementById('photo');
    const file = fileInput.files[0];
    
    if (file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            e.preventDefault();
            alert('File size exceeds 5MB. Please choose a smaller file.');
            return false;
        }
        
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            e.preventDefault();
            alert('Invalid file type. Please upload JPG, PNG, or GIF image.');
            return false;
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
