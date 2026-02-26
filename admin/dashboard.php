<?php
/**
 * Admin Dashboard
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$pageTitle = 'Dashboard - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Get statistics
try {
    $db = getDB();
    
    $totalStudents = $db->query("SELECT COUNT(*) as count FROM students")->fetch()['count'];
    $recentStudents = $db->query("SELECT COUNT(*) as count FROM students WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch()['count'];
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $totalStudents = 0;
    $recentStudents = 0;
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
            <p class="text-muted">Welcome to the Guidance Office Inventory System</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Students</h6>
                            <h2 class="mb-0"><?php echo number_format($totalStudents); ?></h2>
                        </div>
                        <i class="bi bi-people" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">New This Week</h6>
                            <h2 class="mb-0"><?php echo number_format($recentStudents); ?></h2>
                        </div>
                        <i class="bi bi-person-plus" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">System Status</h6>
                            <h5 class="mb-0"><i class="bi bi-check-circle"></i> Active</h5>
                        </div>
                        <i class="bi bi-activity" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="add_student.php" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-person-plus"></i> Add New Student
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="view_students.php" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-people"></i> View All Students
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="export_pdf.php" class="btn btn-warning btn-lg w-100">
                                <i class="bi bi-file-pdf"></i> Export to PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>