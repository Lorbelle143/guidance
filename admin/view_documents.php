<?php
/**
 * Admin View All Student Documents
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$pageTitle = 'Student Documents - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Get all documents sorted by student name
try {
    $db = getDB();
    
    // Search functionality
    $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
    $docType = isset($_GET['doc_type']) ? sanitize($_GET['doc_type']) : '';
    
    $whereClause = '';
    $params = [];
    
    if (!empty($search)) {
        $whereClause = "WHERE (s.student_id LIKE ? OR s.last_name LIKE ? OR s.first_name LIKE ?)";
        $searchParam = "%{$search}%";
        $params = [$searchParam, $searchParam, $searchParam];
    }
    
    if (!empty($docType)) {
        $whereClause .= ($whereClause ? ' AND ' : 'WHERE ') . "sd.document_type = ?";
        $params[] = $docType;
    }
    
    $stmt = $db->prepare("
        SELECT sd.*, s.student_id, s.first_name, s.last_name, s.middle_name
        FROM student_documents sd
        INNER JOIN students s ON sd.student_id = s.id
        {$whereClause}
        ORDER BY s.last_name, s.first_name, sd.uploaded_at DESC
    ");
    $stmt->execute($params);
    $documents = $stmt->fetchAll();
    
    // Get document count by type
    $countStmt = $db->query("
        SELECT document_type, COUNT(*) as count 
        FROM student_documents 
        GROUP BY document_type
    ");
    $docCounts = $countStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
} catch (PDOException $e) {
    error_log("View documents error: " . $e->getMessage());
    $documents = [];
    $docCounts = [];
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-files"></i> Student Documents</h2>
            <p class="text-muted">All uploaded documents sorted by student name</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Inventory Forms</h6>
                    <h3><?php echo $docCounts['inventory_form'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>WHODAS 2.0</h6>
                    <h3><?php echo $docCounts['whodas'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>PID-5</h6>
                    <h3><?php echo $docCounts['pid5'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Consent Forms</h6>
                    <h3><?php echo $docCounts['consent_form'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by student ID or name..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <select name="doc_type" class="form-select">
                        <option value="">All Document Types</option>
                        <option value="inventory_form" <?php echo $docType === 'inventory_form' ? 'selected' : ''; ?>>Inventory Form</option>
                        <option value="whodas" <?php echo $docType === 'whodas' ? 'selected' : ''; ?>>WHODAS 2.0</option>
                        <option value="pid5" <?php echo $docType === 'pid5' ? 'selected' : ''; ?>>PID-5</option>
                        <option value="consent_form" <?php echo $docType === 'consent_form' ? 'selected' : ''; ?>>Consent Form</option>
                        <option value="other" <?php echo $docType === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
            <?php if (!empty($search) || !empty($docType)): ?>
            <div class="mt-2">
                <a href="view_documents.php" class="btn btn-sm btn-secondary">
                    <i class="bi bi-x"></i> Clear Filters
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="card shadow">
        <div class="card-body">
            <?php if (empty($documents)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                <p class="text-muted mt-3">No documents found.</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Document Type</th>
                            <th>File Name</th>
                            <th>Upload Date</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $currentStudent = '';
                        foreach ($documents as $doc): 
                            $studentName = $doc['last_name'] . ', ' . $doc['first_name'];
                            if (!empty($doc['middle_name'])) {
                                $studentName .= ' ' . substr($doc['middle_name'], 0, 1) . '.';
                            }
                            
                            // Highlight row if new student
                            $isNewStudent = ($currentStudent !== $doc['student_id']);
                            $currentStudent = $doc['student_id'];
                        ?>
                        <tr <?php echo $isNewStudent ? 'class="table-primary"' : ''; ?>>
                            <td><strong><?php echo htmlspecialchars($doc['student_id']); ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($studentName); ?></strong></td>
                            <td>
                                <?php
                                $types = [
                                    'inventory_form' => '<span class="badge bg-primary">Inventory Form</span>',
                                    'whodas' => '<span class="badge bg-success">WHODAS 2.0</span>',
                                    'pid5' => '<span class="badge bg-info">PID-5</span>',
                                    'consent_form' => '<span class="badge bg-warning text-dark">Consent Form</span>',
                                    'other' => '<span class="badge bg-secondary">Other</span>'
                                ];
                                echo $types[$doc['document_type']] ?? 'Unknown';
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($doc['document_name']); ?></td>
                            <td><?php echo date('M d, Y h:i A', strtotime($doc['uploaded_at'])); ?></td>
                            <td><?php echo number_format($doc['file_size'] / 1024, 2); ?> KB</td>
                            <td>
                                <a href="../uploads/documents/<?php echo htmlspecialchars($doc['file_path']); ?>" 
                                   class="btn btn-sm btn-primary" target="_blank" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="../process/admin_delete_document.php?id=<?php echo $doc['id']; ?>" 
                                   class="btn btn-sm btn-danger" title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this document?');">
                                    <i class="bi bi-trash"></i>
                                </a>
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
