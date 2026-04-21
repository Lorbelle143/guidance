<?php
if (!defined('SESSION_LIFETIME')) require_once __DIR__ . '/../config/config.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="https://nbscgco.vercel.app/logo.png" alt="Logo" onerror="this.style.display='none'">
        <div class="brand-text">
            <h3>GCO System</h3>
            <p>NBSC Guidance Office</p>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="../admin/admin_dashboard.php" class="<?php echo $currentPage === 'admin_dashboard.php' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="../admin/view_students.php" class="<?php echo $currentPage === 'view_students.php' ? 'active' : ''; ?>">
            <i class="bi bi-people"></i> Students
        </a>
        <a href="../admin/view_documents.php" class="<?php echo $currentPage === 'view_documents.php' ? 'active' : ''; ?>">
            <i class="bi bi-files"></i> Documents
        </a>

        <div class="nav-section-label" style="margin-top: 12px;">Management</div>
        <a href="../admin/add_student.php" class="<?php echo $currentPage === 'add_student.php' ? 'active' : ''; ?>">
            <i class="bi bi-person-plus"></i> Add Student
        </a>
        <a href="../admin/scan_qr.php" class="<?php echo $currentPage === 'scan_qr.php' ? 'active' : ''; ?>">
            <i class="bi bi-qr-code-scan"></i> Scan QR
        </a>
        <a href="../admin/export_pdf.php" class="<?php echo $currentPage === 'export_pdf.php' ? 'active' : ''; ?>">
            <i class="bi bi-file-pdf"></i> Export PDF
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="avatar">
                <i class="bi bi-shield-fill" style="font-size: 1rem;"></i>
            </div>
            <div class="user-info">
                <div class="name">Administrator</div>
                <div class="role">Master Admin</div>
            </div>
            <a href="../auth/logout.php" class="logout-btn" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</aside>
