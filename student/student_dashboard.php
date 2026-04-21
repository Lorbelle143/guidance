<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireStudent();
$pageTitle = 'Dashboard - NBSC Student Portal';
require_once __DIR__.'/../includes/header.php';

try {
    $db = getDB();
    $s = $db->prepare("SELECT * FROM students WHERE id=?");
    $s->execute([$_SESSION['student_id']]);
    $st = $s->fetch();
    if (!$st) { setFlash('error','Record not found.'); redirect('../auth/logout.php'); }
    $_SESSION['student_photo'] = $st['photo'] ?? '';
    $ds = $db->prepare("SELECT * FROM student_documents WHERE student_id=? ORDER BY uploaded_at DESC");
    $ds->execute([$_SESSION['student_id']]);
    $docs = $ds->fetchAll();
} catch (PDOException $e) {
    $docs = [];
    $st = ['first_name'=>'Student','last_name'=>'','student_id'=>'','photo'=>'','email'=>'','is_active'=>1,'last_login'=>null,'created_at'=>date('Y-m-d')];
}

$total    = count($docs);
$complete = count(array_filter($docs, fn($d) => !empty($d['file_path'])));
$dtypes   = ['inventory_form'=>'Inventory Form','whodas'=>'WHODAS 2.0','pid5'=>'PID-5','consent_form'=>'Consent Form','other'=>'Other'];
$dcolors  = ['inventory_form'=>['#dbeafe','#1d4ed8'],'whodas'=>['#d1fae5','#059669'],'pid5'=>['#ede9fe','#7c3aed'],'consent_form'=>['#fef3c7','#d97706'],'other'=>['#f3f4f6','#6b7280']];
$ini      = strtoupper(substr($st['first_name'],0,1).substr($st['last_name'],0,1));
$flash    = getFlash();
?>
<style>
/* ── Notification bell ── */
.notif-btn{position:relative;width:38px;height:38px;border-radius:50%;background:var(--gray-100);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--gray-600);transition:all .15s}
.notif-btn:hover{background:var(--gray-200);color:var(--gray-900)}
.notif-btn svg{width:18px;height:18px}
.notif-dot{position:absolute;top:7px;right:7px;width:8px;height:8px;background:#ef4444;border-radius:50%;border:2px solid #fff}
.notif-panel{position:absolute;top:calc(100% + 10px);right:0;width:320px;background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-xl);border:1px solid var(--gray-200);z-index:100;overflow:hidden}
.notif-panel-hd{padding:14px 18px;border-bottom:1px solid var(--gray-100);display:flex;align-items:center;justify-content:space-between}
.notif-panel-hd h4{font-size:.875rem;font-weight:700;color:var(--gray-900)}
.notif-item{padding:14px 18px;border-bottom:1px solid var(--gray-100);display:flex;gap:12px;align-items:flex-start;transition:background .1s;cursor:pointer}
.notif-item:hover{background:var(--gray-50)}
.notif-item:last-child{border-bottom:none}
.notif-ico{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1rem}
.notif-item .txt{font-size:.82rem;font-weight:600;color:var(--gray-800);margin-bottom:2px}
.notif-item .sub{font-size:.75rem;color:var(--gray-500)}
/* ── Stat cards row ── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
/* ── Quick action 3-col ── */
.quick-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
</style>

<div class="dash">
<div class="dash">
  <!-- ═══ SIDEBAR ═══ -->
  <?php require_once __DIR__.'/../includes/student_sidebar.php'; ?>

  <!-- ═══ MAIN ═══ -->
  <div class="main-area">

    <!-- ── Topbar with notification bell + profile pic ── -->
    <header class="topbar">
      <div class="topbar-left">
        <h2>Dashboard</h2>
        <p>Welcome back, <?= htmlspecialchars($st['first_name']) ?>!</p>
      </div>
      <div class="topbar-right">
        <!-- Notification Bell -->
        <div style="position:relative" id="notifWrap">
          <?php require_once __DIR__.'/../includes/student_notif_bell.php'; ?>
        </div>

        <!-- Profile Avatar -->
        <div class="topbar-avatar" onclick="location.href='profile.php'" title="Edit Profile">
          <?php if (!empty($st['photo']) && file_exists(UPLOAD_PATH.$st['photo'])): ?>
            <img src="../uploads/<?= htmlspecialchars($st['photo']) ?>" alt="">
          <?php else: ?><?= $ini ?><?php endif; ?>
        </div>
      </div>
    </header>

    <!-- ── Page body ── -->
    <main class="page-body">
      <?php if ($flash): ?>
      <div class="alert <?= $flash['type']==='error' ? 'error' : 'success' ?>" style="margin-bottom:20px">
        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <span><?= sanitize($flash['message']) ?></span>
      </div>
      <?php endif; ?>

      <!-- Hero Banner -->
      <div class="hero blue" style="margin-bottom:24px">
        <div class="hero-text">
          <h2>Welcome back, <?= htmlspecialchars($st['first_name']) ?>!</h2>
          <p>Manage your inventory submissions and mental health assessments</p>
        </div>
        <div class="hero-icon">
          <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
        </div>
      </div>

      <!-- ── 4 Stat Cards ── -->
      <div class="stats-grid">
        <!-- Total Documents -->
        <div class="stat-card">
          <div class="stat-icon blue">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
          <div>
            <div class="stat-val"><?= $total ?></div>
            <div class="stat-lbl">Total Documents</div>
          </div>
        </div>
        <!-- Complete -->
        <div class="stat-card">
          <div class="stat-icon green">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <div class="stat-val"><?= $complete ?></div>
            <div class="stat-lbl">Complete</div>
          </div>
        </div>
        <!-- Mental Health (Total Submissions) -->
        <div class="stat-card">
          <div class="stat-icon rose">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
          </div>
          <div>
            <div class="stat-val"><?= $total ?></div>
            <div class="stat-lbl">Total Submissions</div>
          </div>
        </div>
        <!-- Last Upload -->
        <div class="stat-card">
          <div class="stat-icon purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <div>
            <div class="stat-val" style="font-size:1.2rem"><?= $docs ? date('M d', strtotime($docs[0]['uploaded_at'])) : 'N/A' ?></div>
            <div class="stat-lbl">Last Upload</div>
          </div>
        </div>
      </div>

      <!-- ── Quick Actions: Inventory Form | Mental Health | Total Submissions ── -->
      <div class="card" style="margin-bottom:20px">
        <div class="card-header">
          <h3>
            <div class="ch-icon" style="background:#d1fae5;color:#059669">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            Quick Actions
          </h3>
        </div>
        <div class="card-body">
          <div class="quick-grid">
            <!-- Inventory Form -->
            <button class="q-card blue" onclick="location.href='inventory_form.php'">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              <h4>Inventory Form</h4>
              <p>Fill out your student inventory</p>
            </button>
            <!-- Mental Health Assessment -->
            <button class="q-card green" onclick="location.href='mental_health.php'">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <h4>Mental Health</h4>
              <p>Take BSRS-5 assessment</p>
            </button>
            <!-- Total Submissions -->
            <button class="q-card purple" onclick="document.getElementById('docs-section').scrollIntoView({behavior:'smooth'})">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <h4>Total Submissions</h4>
              <p style="font-size:1.5rem;font-weight:800;margin-top:4px"><?= $total ?></p>
            </button>
          </div>
          <div class="info-box" style="margin-top:16px">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p><strong>Need Help?</strong> Contact the Guidance and Counseling Office for assistance with your submissions.</p>
          </div>
        </div>
      </div>

      <!-- ── Documents Table ── -->
      <div class="card" id="docs-section">
        <div class="card-header">
          <h3>
            <div class="ch-icon" style="background:#e0e7ff;color:#4f46e5">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            My Documents
          </h3>
          <a href="upload_document.php" class="btn-main" style="font-size:.8rem;padding:8px 14px">
            <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Upload New
          </a>
        </div>
        <?php if (empty($docs)): ?>
        <div class="empty-state">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
          <h4>No documents yet</h4>
          <p>Upload your first document to get started</p>
          <a href="upload_document.php" class="btn-main">Upload Document</a>
        </div>
        <?php else: ?>
        <div class="tbl-wrap">
          <table class="tbl">
            <thead>
              <tr>
                <th>Document Type</th>
                <th>File Name</th>
                <th>Upload Date</th>
                <th>Size</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($docs as $d):
                $c = $dcolors[$d['document_type']] ?? ['#f3f4f6','#6b7280'];
              ?>
              <tr>
                <td><span class="badge" style="background:<?= $c[0] ?>;color:<?= $c[1] ?>"><?= $dtypes[$d['document_type']] ?? 'Document' ?></span></td>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($d['document_name']) ?></td>
                <td><?= date('M d, Y h:i A', strtotime($d['uploaded_at'])) ?></td>
                <td><?= number_format($d['file_size']/1024, 1) ?> KB</td>
                <td>
                  <div style="display:flex;gap:6px">
                    <a href="../uploads/documents/<?= htmlspecialchars($d['file_path']) ?>" target="_blank" class="btn-sm blue">
                      <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      View
                    </a>
                    <a href="../process/delete_document.php?id=<?= $d['id'] ?>" class="btn-sm red" onclick="return confirm('Delete this document?')">
                      <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      Delete
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>

    </main>
  </div><!-- end main-area -->
</div><!-- end dash -->

<script>
// Notification panel toggle
function toggleNotif() {
    const p = document.getElementById('notifPanel');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
}
// Close when clicking outside
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('notifWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('notifPanel').style.display = 'none';
    }
});
</script>

<?php require_once __DIR__.'/../includes/footer.php'; ?>
