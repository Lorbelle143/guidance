<?php
/**
 * Edit Student Page
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin(); // Only admins can edit students

$pageTitle = 'Edit Student - Guidance Office System';

// Get student ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('error', 'Invalid student ID.');
    redirect('view_students.php');
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        setFlash('error', 'Student not found.');
        redirect('view_students.php');
    }
} catch (PDOException $e) {
    error_log("Edit student error: " . $e->getMessage());
    setFlash('error', 'An error occurred.');
    redirect('view_students.php');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Student Record</h5>
                </div>
                <div class="card-body">
                    <form action="../process/update_student.php" method="POST" enctype="multipart/form-data" id="studentForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                        <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($student['photo']); ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                                <input type="text" name="student_id" id="student_id" class="form-control" 
                                       value="<?php echo htmlspecialchars($student['student_id']); ?>"
                                       required pattern="[A-Za-z0-9\-]+">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($student['email']); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($student['middle_name']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Photo</label>
                            <div>
                                <?php if (!empty($student['photo']) && file_exists(UPLOAD_PATH . $student['photo'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($student['photo']); ?>" 
                                     alt="Current Photo" class="img-thumbnail" style="max-width: 200px;">
                                <?php else: ?>
                                <p class="text-muted">No photo available</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Upload New Photo (Optional)</label>
                            <input type="file" name="photo" id="photo" class="form-control" 
                                   accept="image/jpeg,image/png,image/gif">
                            <div class="form-text">
                                Leave empty to keep current photo. Allowed formats: JPG, PNG, GIF. Maximum size: 5MB
                            </div>
                        </div>

                        <div class="mb-3" id="imagePreview" style="display: none;">
                            <label class="form-label">New Photo Preview</label>
                            <div>
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Student
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
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
