<?php
// Config and session should already be loaded by parent page
if (!defined('SESSION_LIFETIME')) {
    require_once __DIR__ . '/../config/config.php';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="../student/student_dashboard.php">
            <i class="bi bi-journal-medical"></i> Student Portal
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../student/student_dashboard.php">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../student/upload_document.php">
                        <i class="bi bi-file-earmark-arrow-up"></i> Upload Documents
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../student/profile.php">
                        <i class="bi bi-person-circle"></i> My Profile
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-badge"></i> <?php echo sanitize($_SESSION['student_number'] ?? 'Student'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../student/change_password.php"><i class="bi bi-key"></i> Change Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php
// Display flash messages
$flash = getFlash();
if ($flash):
    $alertType = $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : 'info');
?>
<div class="container mt-3">
    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
        <?php echo sanitize($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>
