<?php
/**
 * Student Dashboard
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireStudent();

$pageTitle = 'Student Dashboard - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/student_navbar.php';

// Get student information
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $student = $stmt->fetch();
    
    if (!$student) {
        setFlash('error', 'Student record not found.');
        redirect('../auth/logout.php');
    }
} catch (PDOException $e) {
    error_log("Student dashboard error: " . $e->getMessage());
    setFlash('error', 'An error occurred.');
    redirect('../auth/logout.php');
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-person-badge"></i> My Dashboard</h2>
            <p class="text-muted">Welcome, <?php echo htmlspecialchars($student['first_name']); ?>!</p>
        </div>
    </div>

    <div class="row">
        <!-- Student Profile Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <?php if (!empty($student['photo']) && file_exists(UPLOAD_PATH . $student['photo'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($student['photo']); ?>" 
                         alt="Profile Photo" class="rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 150px; height: 150px;">
                        <i class="bi bi-person text-white" style="font-size: 4rem;"></i>
                    </div>
                    <?php endif; ?>
                    
                    <h4><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h4>
                    <p class="text-muted mb-2">
                        <?php 
                        if (!empty($student['middle_name'])) {
                            echo htmlspecialchars($student['middle_name']);
                        }
                        ?>
                    </p>
                    <h5 class="text-primary"><?php echo htmlspecialchars($student['student_id']); ?></h5>
                    
                    <?php if ($student['is_active']): ?>
                    <span class="badge bg-success">Active</span>
                    <?php else: ?>
                    <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="col-md-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Personal Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Student ID:</strong></td>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Full Name:</strong></td>
                            <td>
                                <?php 
                                echo htmlspecialchars($student['first_name'] . ' ');
                                if (!empty($student['middle_name'])) {
                                    echo htmlspecialchars($student['middle_name'] . ' ');
                                }
                                echo htmlspecialchars($student['last_name']);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?php echo htmlspecialchars($student['email'] ?? 'Not provided'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
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
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Scanner Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-qr-code-scan"></i> My QR Code & Documents</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">My QR Code</h6>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                Show this QR code to the guidance office staff for quick access
                            </div>
                            
                            <div class="text-center">
                                <div id="qrcode" class="mb-3 d-inline-block"></div>
                                <div>
                                    <button onclick="downloadQR()" class="btn btn-success btn-sm me-2">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                    <button onclick="printQR()" class="btn btn-primary btn-sm">
                                        <i class="bi bi-printer"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="mb-3">Upload Documents</h6>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                Upload your guidance office forms and documents
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="upload_document.php" class="btn btn-success btn-lg">
                                    <i class="bi bi-file-earmark-arrow-up"></i> Upload Documents
                                </a>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <strong>Accepted documents:</strong><br>
                                    • Individual Inventory Form<br>
                                    • WHODAS 2.0 Assessment<br>
                                    • PID-5 Personality Inventory<br>
                                    • Counseling Consent Form
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
// Generate QR Code with student data
const studentData = {
    student_id: '<?php echo htmlspecialchars($student['student_id']); ?>',
    name: '<?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>',
    email: '<?php echo htmlspecialchars($student['email'] ?? ''); ?>',
    photo: '<?php echo htmlspecialchars($student['photo'] ?? ''); ?>'
};

const qrcode = new QRCode(document.getElementById("qrcode"), {
    text: JSON.stringify(studentData),
    width: 256,
    height: 256,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});

function downloadQR() {
    const canvas = document.querySelector('#qrcode canvas');
    const url = canvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.download = 'student_qr_<?php echo $student['student_id']; ?>.png';
    link.href = url;
    link.click();
}

function printQR() {
    const canvas = document.querySelector('#qrcode canvas');
    const dataUrl = canvas.toDataURL();
    const windowContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Student QR Code - <?php echo $student['student_id']; ?></title>
            <style>
                body { text-align: center; font-family: Arial, sans-serif; padding: 20px; }
                h2 { margin-bottom: 10px; }
                .info { margin: 20px 0; }
            </style>
        </head>
        <body>
            <h2>Student QR Code</h2>
            <div class="info">
                <strong>Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?><br>
                <strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?>
            </div>
            <img src="${dataUrl}" alt="QR Code">
            <script>window.print(); window.close();</script>
        </body>
        </html>
    `;
    const printWindow = window.open('', '', 'width=600,height=600');
    printWindow.document.open();
    printWindow.document.write(windowContent);
    printWindow.document.close();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
