<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="../admin/dashboard.php">
            <i class="bi bi-journal-medical"></i> Guidance Inventory System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../admin/dashboard.php">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../admin/add_student.php">
                        <i class="bi bi-person-plus"></i> Add Student
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../admin/view_students.php">
                        <i class="bi bi-people"></i> View Students
                    </a>
                </li>
                <li class="nav-item">
                    <span class="nav-link text-white-50">
                        <i class="bi bi-person-circle"></i> <?php echo sanitize($_SESSION['username'] ?? 'User'); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-light btn-sm ms-2" href="../auth/logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
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
