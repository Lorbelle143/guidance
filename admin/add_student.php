<?php
/**
 * Add Student Page
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin(); // Only admins can add students

$pageTitle = 'Add Student - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus"></i> Individual Inventory Form</h5>
                </div>
                <div class="card-body">
                    <form action="../process/save_student.php" method="POST" enctype="multipart/form-data" id="studentForm">
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
                                       placeholder="student@example.com">
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
                            <label for="password" class="form-label">Default Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control" 
                                   placeholder="Set default password" required minlength="6">
                            <small class="text-muted">Student will use this to login initially</small>
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

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Student
                            </button>
                            <a href="view_students.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
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
document.getElementById('studentForm').addEventListener('submit', function(e) {
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