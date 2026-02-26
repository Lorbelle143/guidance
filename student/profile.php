<?php
/**
 * Student Profile Page
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireStudent();

$pageTitle = 'My Profile - Student Portal';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/student_navbar.php';

// Get student information
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $student = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Student profile error: " . $e->getMessage());
    setFlash('error', 'An error occurred.');
    redirect('student_dashboard.php');
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> My Complete Profile</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center border-end">
                            <?php if (!empty($student['photo']) && file_exists(UPLOAD_PATH . $student['photo'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($student['photo']); ?>" 
                                 alt="Profile Photo" class="rounded-circle mb-3" 
                                 style="width: 200px; height: 200px; object-fit: cover;">
                            <?php else: ?>
                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 200px; height: 200px;">
                                <i class="bi bi-person text-white" style="font-size: 5rem;"></i>
                            </div>
                            <?php endif; ?>
                            
                            <h4><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h4>
                            <p class="text-muted">
                                <?php 
                                if (!empty($student['middle_name'])) {
                                    echo htmlspecialchars($student['middle_name']);
                                }
                                ?>
                            </p>
                            <h5 class="text-success"><?php echo htmlspecialchars($student['student_id']); ?></h5>
                        </div>
                        
                        <div class="col-md-8">
                            <h6 class="text-muted mb-3">Personal Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%"><strong>Student ID:</strong></td>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Last Name:</strong></td>
                                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>First Name:</strong></td>
                                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Middle Name:</strong></td>
                                    <td><?php echo htmlspecialchars($student['middle_name'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo htmlspecialchars($student['email'] ?? 'Not provided'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Account Status:</strong></td>
                                    <td>
                                        <?php if ($student['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login:</strong></td>
                                    <td>
                                        <?php 
                                        echo $student['last_login'] 
                                            ? date('F d, Y h:i A', strtotime($student['last_login'])) 
                                            : 'First time login'; 
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Registered:</strong></td>
                                    <td><?php echo date('F d, Y', strtotime($student['created_at'])); ?></td>
                                </tr>
                            </table>
                            
                            <div class="mt-4">
                                <a href="change_password.php" class="btn btn-primary">
                                    <i class="bi bi-key"></i> Change Password
                                </a>
                                <a href="student_dashboard.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
