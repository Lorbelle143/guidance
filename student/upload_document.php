<?php
/**
 * Student Document Upload Page
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireStudent();

$pageTitle = 'Upload Documents - Student Portal';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/student_navbar.php';

// Get student information
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $student = $stmt->fetch();
    
    // Get existing documents
    $docStmt = $db->prepare("SELECT * FROM student_documents WHERE student_id = ? ORDER BY uploaded_at DESC");
    $docStmt->execute([$_SESSION['student_id']]);
    $documents = $docStmt->fetchAll();
} catch (PDOException $e) {
    error_log("Upload document error: " . $e->getMessage());
    $documents = [];
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-file-earmark-arrow-up"></i> Upload Documents</h2>
            <p class="text-muted">Scan and upload your guidance office documents</p>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> Upload New Document</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Supported Documents:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Individual Inventory Form</li>
                            <li>WHODAS 2.0 Assessment</li>
                            <li>PID-5 Personality Inventory</li>
                            <li>Counseling Consent Form</li>
                            <li>Other guidance documents</li>
                        </ul>
                    </div>

                    <form action="../process/upload_document_process.php" method="POST" enctype="multipart/form-data" id="uploadForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="document_type" class="form-label">Document Type <span class="text-danger">*</span></label>
                            <select name="document_type" id="document_type" class="form-select" required>
                                <option value="">Select document type...</option>
                                <option value="inventory_form">Individual Inventory Form</option>
                                <option value="whodas">WHODAS 2.0 Assessment</option>
                                <option value="pid5">PID-5 Personality Inventory</option>
                                <option value="consent_form">Counseling Consent Form</option>
                                <option value="other">Other Document</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="document" class="form-label">Upload Document <span class="text-danger">*</span></label>
                            <input type="file" name="document" id="document" class="form-control" 
                                   accept="image/*,.pdf" required>
                            <div class="form-text">
                                Accepted formats: JPG, PNG, PDF. Maximum size: 10MB
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                      placeholder="Add any notes about this document..."></textarea>
                        </div>

                        <div class="mb-3" id="imagePreview" style="display: none;">
                            <label class="form-label">Preview</label>
                            <div>
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 400px;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload"></i> Upload Document
                        </button>
                        <a href="student_dashboard.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-question-circle"></i> How to Upload</h6>
                </div>
                <div class="card-body">
                    <ol class="small">
                        <li>Take a clear photo of your document or scan it</li>
                        <li>Select the document type from the dropdown</li>
                        <li>Click "Choose File" and select your document</li>
                        <li>Add notes if needed</li>
                        <li>Click "Upload Document"</li>
                    </ol>
                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle"></i> 
                        Make sure the document is clear and readable before uploading.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Uploaded Documents -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-files"></i> My Uploaded Documents</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($documents)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">No documents uploaded yet.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Document Type</th>
                                    <th>File Name</th>
                                    <th>Upload Date</th>
                                    <th>Size</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $types = [
                                            'inventory_form' => 'Inventory Form',
                                            'whodas' => 'WHODAS 2.0',
                                            'pid5' => 'PID-5',
                                            'consent_form' => 'Consent Form',
                                            'other' => 'Other'
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
                                        <a href="../process/delete_document.php?id=<?php echo $doc['id']; ?>" 
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
    </div>
</div>

<script>
// Image preview
document.getElementById('document').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagePreview').style.display = 'none';
    }
});

// Form validation
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('document');
    const file = fileInput.files[0];
    
    if (file) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            e.preventDefault();
            alert('File size exceeds 10MB. Please choose a smaller file.');
            return false;
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
