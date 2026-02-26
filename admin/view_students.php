<?php
/**
 * View Students Page
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$pageTitle = 'View Students - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Search functionality
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

try {
    $db = getDB();
    
    // Build query
    $whereClause = '';
    $params = [];
    
    if (!empty($search)) {
        $whereClause = "WHERE student_id LIKE ? OR last_name LIKE ? OR first_name LIKE ? OR email LIKE ?";
        $searchParam = "%{$search}%";
        $params = [$searchParam, $searchParam, $searchParam, $searchParam];
    }
    
    // Get total count
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM students {$whereClause}");
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch()['total'];
    $totalPages = ceil($totalRecords / $perPage);
    
    // Get students
    $stmt = $db->prepare("
        SELECT * FROM students 
        {$whereClause}
        ORDER BY created_at DESC 
        LIMIT {$perPage} OFFSET {$offset}
    ");
    $stmt->execute($params);
    $students = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("View students error: " . $e->getMessage());
    $students = [];
    $totalRecords = 0;
    $totalPages = 0;
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-people"></i> Student Records</h2>
            <p class="text-muted">Total: <?php echo number_format($totalRecords); ?> students</p>
        </div>
        <div class="col-md-6 text-end">
            <?php if (isAdmin()): ?>
            <a href="add_student.php" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Add New Student
            </a>
            <?php endif; ?>
            <a href="export_pdf.php<?php echo !empty($search) ? '?search=' . urlencode($search) : ''; ?>" 
               class="btn btn-warning" target="_blank">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by ID, name, or email..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
            <?php if (!empty($search)): ?>
            <div class="mt-2">
                <a href="view_students.php" class="btn btn-sm btn-secondary">
                    <i class="bi bi-x"></i> Clear Search
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow">
        <div class="card-body">
            <?php if (empty($students)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                <p class="text-muted mt-3">No students found.</p>
                <a href="add_student.php" class="btn btn-primary">Add First Student</a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Photo</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date Added</th>
                            <?php if (isAdmin()): ?>
                            <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td>
                                <?php if (!empty($student['photo']) && file_exists(UPLOAD_PATH . $student['photo'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($student['photo']); ?>" 
                                     alt="Photo" class="rounded" width="50" height="50" style="object-fit: cover;">
                                <?php else: ?>
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td>
                                <?php 
                                echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']);
                                if (!empty($student['middle_name'])) {
                                    echo ' ' . htmlspecialchars(substr($student['middle_name'], 0, 1)) . '.';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                            <?php if (isAdmin()): ?>
                            <td>
                                <a href="edit_student.php?id=<?php echo $student['id']; ?>" 
                                   class="btn btn-sm btn-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="delete_student.php?id=<?php echo $student['id']; ?>" 
                                   class="btn btn-sm btn-danger" title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this student?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                            Previous
                        </a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                            Next
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>