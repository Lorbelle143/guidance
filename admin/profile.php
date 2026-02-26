<?php
/**
 * User Profile Page
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$pageTitle = 'My Profile - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Get user info
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Profile error: " . $e->getMessage());
    setFlash('error', 'An error occurred.');
    redirect('admin_dashboard.php');
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> My Profile</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <i class="bi bi-person-circle text-primary" style="font-size: 8rem;"></i>
                            <h5 class="mt-3"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                            <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                            <?php if ($user['role'] === 'admin'): ?>
                            <span class="badge bg-danger">Administrator</span>
                            <?php else: ?>
                            <span class="badge bg-primary">User</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted mb-3">Account Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo htmlspecialchars($user['email'] ?? 'Not set'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td><?php echo ucfirst($user['role']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <?php if ($user['is_active']): ?>
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
                                        echo $user['last_login'] 
                                            ? date('F d, Y h:i A', strtotime($user['last_login'])) 
                                            : 'Never'; 
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Member Since:</strong></td>
                                    <td><?php echo date('F d, Y', strtotime($user['created_at'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted mb-3">Change Password</h6>
                    <form action="../process/change_password.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" name="current_password" id="current_password" 
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" name="new_password" id="new_password" 
                                   class="form-control" required minlength="6">
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" 
                                   class="form-control" required minlength="6">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key"></i> Change Password
                        </button>
                        <a href="admin_dashboard.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
