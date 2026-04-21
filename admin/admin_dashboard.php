<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Admin Dashboard - NBSC GCO';
require_once __DIR__.'/../includes/header.php';

try {
    $db = getDB();
    $totalStudents  = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $recentStudents = $db->query("SELECT COUNT(*) FROM students WHERE created_at>=DATE_SUB(NOW(),INTERVAL 7 DAY)")->fetchColumn();
    $totalDocs      = $db->query("SELECT COUNT(*) FROM student_documents")->fetchColumn();
    $studentsWithDocs = $db->query("SELECT COUNT(DISTINCT student_id) FROM student_documents")->fetchColumn();
    $recentDocs     = $db->query("SELECT COUNT(*) FROM student_documents WHERE uploaded_at>=DATE_SUB(NOW(),INTERVAL 7 DAY)")->fetchColumn();
    $uploads        = $db->query("SELECT sd.*,s.student_id as sid,s.first_name,s.last_name,s.photo FROM student_documents sd INNER JOIN students s ON sd.student_id=s.id ORDER BY sd.uploaded_at DESC LIMIT 8")->fetchAll();
    $studentsList   = $db->query("SELECT s.*,COUNT(sd.id) as doc_count FROM students s LEFT JOIN student_documents sd ON s.id=sd.student_id GROUP BY s.id ORDER BY s.last_name,s.first_name LIMIT 10")->fetchAll();
} catch (PDOException $e) {
    $totalStudents = $totalDocs = $studentsWithDocs = $recentStudents = $recentDocs = 0;
    $uploads = $studentsList = [];
}

$dtypes  = ['inventory_form'=>'Inventory Form','whodas'=>'WHODAS 2.0','pid5'=>'PID-5','consent_form'=>'Consent Form','other'=>'Other'];
$dbadges = ['inventory_form'=>'blue','whodas'=>'green','pid5'=>'purple','consent_form'=>'amber','other'=>'gray'];
$flash   = getFlash();
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>

  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left">
        <h2>Dashboard</h2>
        <p>Welcome back, Administrator</p>
      </div>
      <div class="topbar-right">
        <a href="add_student.php" class="btn-main">
          <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
          Add Student
        </a>
        <?php require_once __DIR__.'/../includes/admin_notif_bell.php'; ?>
        <div class="topbar-avatar" style="background:linear-gradient(135deg,#f97316,#dc2626);cursor:default" title="Administrator">
          <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
      </div>
    </header>

    <main class="page-body">
      <?php if ($flash): ?>
      <div class="alert <?= $flash['type']==='error'?'error':'success' ?>" style="margin-bottom:20px">
        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <span><?= sanitize($flash['message']) ?></span>
      </div>
      <?php endif; ?>

      <!-- Hero -->
      <div class="hero admin" style="margin-bottom:24px">
        <div class="hero-text">
          <h2>Admin Dashboard</h2>
          <p>Manage students, documents, and system settings</p>
        </div>
        <div class="hero-icon">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
          <div><div class="stat-val"><?= number_format($totalStudents) ?></div><div class="stat-lbl">Total Students</div><div class="stat-sub">+<?= $recentStudents ?> this week</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
          <div><div class="stat-val"><?= number_format($totalDocs) ?></div><div class="stat-lbl">Total Documents</div><div class="stat-sub">+<?= $recentDocs ?> this week</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
          <div><div class="stat-val"><?= number_format($studentsWithDocs) ?></div><div class="stat-lbl">With Documents</div><div class="stat-sub neutral"><?= $totalStudents > 0 ? round(($studentsWithDocs/$totalStudents)*100) : 0 ?>% of total</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon amber"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></div>
          <div><div class="stat-val"><?= number_format($recentStudents) ?></div><div class="stat-lbl">New This Week</div><div class="stat-sub neutral">Recent registrations</div></div>
        </div>
      </div>

      <!-- Two columns -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

        <!-- Recent Uploads -->
        <div class="card" style="margin-bottom:0">
          <div class="card-header">
            <h3>
              <div class="ch-icon" style="background:#dbeafe;color:#1d4ed8">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              Recent Uploads
            </h3>
            <a href="view_documents.php" class="btn-sm blue">View All</a>
          </div>
          <div style="max-height:380px;overflow-y:auto">
            <?php if (empty($uploads)): ?>
            <div class="empty-state" style="padding:40px 20px">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <p>No uploads yet</p>
            </div>
            <?php else: foreach ($uploads as $u): ?>
            <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid var(--gray-100)">
              <?php if (!empty($u['photo']) && file_exists(UPLOAD_PATH.$u['photo'])): ?>
              <img src="../uploads/<?= htmlspecialchars($u['photo']) ?>" style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--gray-200);flex-shrink:0">
              <?php else: ?>
              <div style="width:38px;height:38px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:700;flex-shrink:0"><?= strtoupper(substr($u['first_name'],0,1).substr($u['last_name'],0,1)) ?></div>
              <?php endif; ?>
              <div style="flex:1;min-width:0">
                <div style="font-size:.875rem;font-weight:600;color:var(--gray-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($u['last_name'].', '.$u['first_name']) ?></div>
                <div style="font-size:.75rem;color:var(--gray-500);display:flex;align-items:center;gap:6px;margin-top:2px">
                  <span class="badge <?= $dbadges[$u['document_type']]??'gray' ?>" style="padding:2px 8px"><?= $dtypes[$u['document_type']]??'Doc' ?></span>
                  <?= date('M d, h:i A', strtotime($u['uploaded_at'])) ?>
                </div>
              </div>
              <a href="../uploads/documents/<?= htmlspecialchars($u['file_path']) ?>" target="_blank" class="btn-sm blue">
                <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </a>
            </div>
            <?php endforeach; endif; ?>
          </div>
        </div>

        <!-- Students List -->
        <div class="card" style="margin-bottom:0">
          <div class="card-header">
            <h3>
              <div class="ch-icon" style="background:#d1fae5;color:#059669">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </div>
              Students
            </h3>
            <a href="view_students.php" class="btn-sm green">View All</a>
          </div>
          <div style="max-height:380px;overflow-y:auto">
            <?php if (empty($studentsList)): ?>
            <div class="empty-state" style="padding:40px 20px">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <p>No students yet</p>
            </div>
            <?php else: foreach ($studentsList as $s): ?>
            <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid var(--gray-100)">
              <?php if (!empty($s['photo']) && file_exists(UPLOAD_PATH.$s['photo'])): ?>
              <img src="../uploads/<?= htmlspecialchars($s['photo']) ?>" style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--gray-200);flex-shrink:0">
              <?php else: ?>
              <div style="width:38px;height:38px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:700;flex-shrink:0"><?= strtoupper(substr($s['first_name'],0,1).substr($s['last_name'],0,1)) ?></div>
              <?php endif; ?>
              <div style="flex:1;min-width:0">
                <div style="font-size:.875rem;font-weight:600;color:var(--gray-900)"><?= htmlspecialchars($s['last_name'].', '.$s['first_name']) ?></div>
                <div style="font-size:.75rem;color:var(--gray-500);margin-top:2px"><?= htmlspecialchars($s['student_id']) ?></div>
              </div>
              <span class="badge <?= $s['doc_count']>0?'green':'gray' ?>"><?= $s['doc_count'] ?> doc<?= $s['doc_count']!=1?'s':'' ?></span>
            </div>
            <?php endforeach; endif; ?>
          </div>
        </div>

      </div><!-- end two columns -->
    </main>
  </div><!-- end main-area -->
</div><!-- end dash -->
<?php require_once __DIR__.'/../includes/footer.php'; ?>
