<?php
/**
 * Manage Users Page (Admin Only)
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pageTitle = 'Manage Users - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Get all users
try {
    $db = getDB();
    $stmt = $db->query("SELECT id, username, full_name, email, role, is_active, last_login, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Manage users error: " . $e->getMessage());
    $users = [];
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-person-gear"></i> Manage Users</h2>
            <p class="text-muted">Total: <?php echo count($users); ?> users</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="create_admin.php" class="btn btn-danger">
                <i class="bi bi-shield-plus"></i> Create Admin Account
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                <p class="text-muted mt-3">No users found.</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge bg-danger">Admin</span>
                                <?php else: ?>
                                <span class="badge bg-primary">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                echo $user['last_login'] 
                                    ? date('M d, Y h:i A', strtotime($user['last_login'])) 
                                    : 'Never'; 
                                ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id'] && !isset($_SESSION['is_master'])): ?>
                                <a href="../process/toggle_user_status.php?id=<?php echo $user['id']; ?>" 
                                   class="btn btn-sm btn-<?php echo $user['is_active'] ? 'warning' : 'success'; ?>" 
                                   title="<?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>"
                                   onclick="return confirm('Are you sure you want to <?php echo $user['is_active'] ? 'deactivate' : 'activate'; ?> this user?');">
                                    <i class="bi bi-<?php echo $user['is_active'] ? 'pause' : 'play'; ?>-circle"></i>
                                </a>
                                <a href="../process/delete_user.php?id=<?php echo $user['id']; ?>" 
                                   class="btn btn-sm btn-danger" title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php elseif (isset($_SESSION['is_master'])): ?>
                                <span class="text-muted small">Master Admin</span>
                                <?php else: ?>
                                <span class="text-muted small">Current User</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
