<?php
if (!defined('SESSION_LIFETIME')) require_once __DIR__ . '/../config/config.php';
$currentPage = basename($_SERVER['PHP_SELF']);

// Get student photo
$studentPhoto = $_SESSION['student_photo'] ?? '';
$studentName = $_SESSION['full_name'] ?? 'Student';
$studentId = $_SESSION['student_number'] ?? '';
$initials = '';
if ($studentName) {
    $parts = explode(' ', $studentName);
    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="https://nbscgco.vercel.app/logo.png" alt="Logo" onerror="this.style.display='none'">
        <div class="brand-text">
            <h3>Student Portal</h3>
            <p>NBSC Guidance Office</p>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="../student/student_dashboard.php" class="<?php echo $currentPage === 'student_dashboard.php' ? 'active' : ''; ?>">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <a href="../student/inventory_form.php" class="<?php echo $currentPage === 'inventory_form.php' ? 'active' : ''; ?>">
            <i class="bi bi-clipboard-text"></i> Inventory Form
        </a>
        <a href="../student/profile.php" class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">
            <i class="bi bi-person-circle"></i> My Profile
        </a>

        <div class="nav-section-label" style="margin-top: 12px;">Documents</div>
        <a href="../student/upload_document.php" class="<?php echo $currentPage === 'upload_document.php' ? 'active' : ''; ?>">
            <i class="bi bi-cloud-upload"></i> Upload Documents
        </a>

        <div class="nav-section-label" style="margin-top: 12px;">Account</div>
        <a href="../student/change_password.php" class="<?php echo $currentPage === 'change_password.php' ? 'active' : ''; ?>">
            <i class="bi bi-key"></i> Change Password
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="avatar">
                <?php if (!empty($studentPhoto) && file_exists(UPLOAD_PATH . $studentPhoto)): ?>
                <img src="../uploads/<?php echo htmlspecialchars($studentPhoto); ?>" alt="Photo">
                <?php else: ?>
                <?php echo $initials ?: '<i class="bi bi-person"></i>'; ?>
                <?php endif; ?>
            </div>
            <div class="user-info">
                <div class="name"><?php echo htmlspecialchars($studentName); ?></div>
                <div class="role"><?php echo htmlspecialchars($studentId); ?></div>
            </div>
            <a href="../auth/logout.php" class="logout-btn" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</aside>
