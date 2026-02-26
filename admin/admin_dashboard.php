<?php
/**
 * Admin Dashboard
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$pageTitle = 'Admin Dashboard - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Get statistics
try {
    $db = getDB();
    
    // Total students
    $totalStudents = $db->query("SELECT COUNT(*) as count FROM students")->fetch()['count'];
    
    // Recent students (last 7 days)
    $recentStudents = $db->query("SELECT COUNT(*) as count FROM students WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch()['count'];
    
    // Total documents
    $totalDocuments = $db->query("SELECT COUNT(*) as count FROM student_documents")->fetch()['count'];
    
    // Students with documents
    $studentsWithDocs = $db->query("SELECT COUNT(DISTINCT student_id) as count FROM student_documents")->fetch()['count'];
    
    // Recent documents (last 7 days)
    $recentDocs = $db->query("SELECT COUNT(*) as count FROM student_documents WHERE uploaded_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch()['count'];
    
    // Get recent student uploads
    $recentUploads = $db->query("
        SELECT sd.*, s.student_id, s.first_name, s.last_name, s.photo
        FROM student_documents sd
        INNER JOIN students s ON sd.student_id = s.id
        ORDER BY sd.uploaded_at DESC
        LIMIT 10
    ")->fetchAll();
    
    // Get students with document counts
    $studentsWithCounts = $db->query("
        SELECT s.*, 
               COUNT(sd.id) as doc_count,
               MAX(sd.uploaded_at) as last_upload
        FROM students s
        LEFT JOIN student_documents sd ON s.id = sd.student_id
        GROUP BY s.id
        ORDER BY s.last_name, s.first_name
        LIMIT 10
    ")->fetchAll();
    
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $totalStudents = 0;
    $recentStudents = 0;
    $totalDocuments = 0;
    $studentsWithDocs = 0;
    $recentDocs = 0;
    $recentUploads = [];
    $studentsWithCounts = [];
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-speedometer2"></i> Admin Dashboard</h2>
            <p class="text-muted">Welcome to the Guidance Office System</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Students</h6>
                            <h2 class="mb-0"><?php echo number_format($totalStudents); ?></h2>
                            <small>+<?php echo $recentStudents; ?> this week</small>
                        </div>
                        <i class="bi bi-people" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Documents</h6>
                            <h2 class="mb-0"><?php echo number_format($totalDocuments); ?></h2>
                            <small>+<?php echo $recentDocs; ?> this week</small>
                        </div>
                        <i class="bi bi-files" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Students w/ Docs</h6>
                            <h2 class="mb-0"><?php echo number_format($studentsWithDocs); ?></h2>
                            <small><?php echo $totalStudents > 0 ? round(($studentsWithDocs/$totalStudents)*100) : 0; ?>% of total</small>
                        </div>
                        <i class="bi bi-person-check" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">System Status</h6>
                            <h5 class="mb-0"><i class="bi bi-check-circle"></i> Active</h5>
                            <small>All systems operational</small>
                        </div>
                        <i class="bi bi-activity" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Uploads and Students -->
    <div class="row mb-4">
        <!-- Recent Document Uploads -->
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Document Uploads</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($recentUploads)): ?>
                    <p class="text-muted text-center py-3">No documents uploaded yet</p>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentUploads as $upload): ?>
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <?php if (!empty($upload['photo']) && file_exists(UPLOAD_PATH . $upload['photo'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($upload['photo']); ?>" 
                                     class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                <?php else: ?>
                                <div class="bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <strong><?php echo htmlspecialchars($upload['last_name'] . ', ' . $upload['first_name']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php
                                        $types = [
                                            'inventory_form' => 'Inventory Form',
                                            'whodas' => 'WHODAS 2.0',
                                            'pid5' => 'PID-5',
                                            'consent_form' => 'Consent Form',
                                            'other' => 'Other'
                                        ];
                                        echo $types[$upload['document_type']] ?? 'Document';
                                        ?>
                                        • <?php echo date('M d, h:i A', strtotime($upload['uploaded_at'])); ?>
                                    </small>
                                </div>
                                <a href="../uploads/documents/<?php echo htmlspecialchars($upload['file_path']); ?>" 
                                   class="btn btn-sm btn-primary" target="_blank">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="view_documents.php" class="btn btn-sm btn-primary">
                        View All Documents <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Students List with Document Counts -->
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Students & Documents</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($studentsWithCounts)): ?>
                    <p class="text-muted text-center py-3">No students registered yet</p>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($studentsWithCounts as $student): ?>
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <?php if (!empty($student['photo']) && file_exists(UPLOAD_PATH . $student['photo'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($student['photo']); ?>" 
                                     class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                <?php else: ?>
                                <div class="bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <strong><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        ID: <?php echo htmlspecialchars($student['student_id']); ?> • 
                                        <span class="badge bg-<?php echo $student['doc_count'] > 0 ? 'success' : 'secondary'; ?>">
                                            <?php echo $student['doc_count']; ?> document<?php echo $student['doc_count'] != 1 ? 's' : ''; ?>
                                        </span>
                                    </small>
                                </div>
                                <a href="view_documents.php?search=<?php echo urlencode($student['student_id']); ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-files"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="view_students.php" class="btn btn-sm btn-primary">
                        View All Students <i class="bi bi-arrow-right"></i>
                    </a>
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
                        <?php if (isAdmin()): ?>
                        <div class="col-md-3">
                            <a href="add_student.php" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-person-plus"></i> Add New Student
                            </a>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-<?php echo isAdmin() ? '3' : '4'; ?>">
                            <a href="view_students.php" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-people"></i> View All Students
                            </a>
                        </div>
                        <div class="col-md-<?php echo isAdmin() ? '3' : '4'; ?>">
                            <a href="view_documents.php" class="btn btn-info btn-lg w-100">
                                <i class="bi bi-files"></i> Student Documents
                            </a>
                        </div>
                        <div class="col-md-<?php echo isAdmin() ? '3' : '4'; ?>">
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